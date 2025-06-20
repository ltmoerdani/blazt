<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WhatsAppAccountResource\Pages;
use App\Domain\WhatsApp\Models\WhatsAppAccount;
use App\Domain\User\Models\User as DomainUser;
use App\Interfaces\WhatsApp\WhatsAppServiceInterface;
use App\Services\EnhancedWhatsAppService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class WhatsAppAccountResource extends Resource
{
    protected static ?string $model = WhatsAppAccount::class;

    protected static ?string $navigationIcon = 'heroicon-o-device-phone-mobile';
    
    protected static ?string $navigationGroup = 'WhatsApp Management';
    
    protected static ?string $modelLabel = 'WhatsApp Account';
    
    protected static ?string $pluralModelLabel = 'WhatsApp Accounts';

    /**
     * Get the domain user from the authenticated user
     */
    protected static function getDomainUser(): DomainUser
    {
        $authUser = Auth::user();
        return DomainUser::find($authUser->id);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Account Information')
                    ->schema([
                        Forms\Components\TextInput::make('phone_number')
                            ->label('Phone Number')
                            ->tel()
                            ->required()
                            ->placeholder('628123456789')
                            ->helperText('Include country code (e.g. 628123456789)')
                            ->rules(['regex:/^[0-9]{10,15}$/']),
                            
                        Forms\Components\TextInput::make('display_name')
                            ->label('Display Name')
                            ->required()
                            ->placeholder('My WhatsApp Account'),
                            
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'disconnected' => 'Disconnected',
                                'connecting' => 'Connecting',
                                'connected' => 'Connected',
                                'banned' => 'Banned',
                            ])
                            ->disabled()
                            ->default('disconnected'),
                    ])
                    ->columns(2),
                    
                Forms\Components\Section::make('Connection Information')
                    ->schema([
                        Forms\Components\Placeholder::make('last_connected_at')
                            ->label('Last Connected')
                            ->content(fn ($record) => $record?->last_connected_at?->diffForHumans() ?? 'Never'),
                            
                        Forms\Components\Placeholder::make('health_check_at')
                            ->label('Last Health Check')
                            ->content(fn ($record) => $record?->health_check_at?->diffForHumans() ?? 'Never'),
                    ])
                    ->columns(2)
                    ->hidden(fn ($record) => !$record),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('phone_number')
                    ->label('Phone Number')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('display_name')
                    ->label('Display Name')
                    ->searchable()
                    ->limit(30),
                    
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'danger' => 'disconnected',
                        'warning' => 'connecting',
                        'success' => 'connected',
                        'danger' => 'banned',
                    ])
                    ->icons([
                        'heroicon-o-x-circle' => 'disconnected',
                        'heroicon-o-clock' => 'connecting',
                        'heroicon-o-check-circle' => 'connected',
                        'heroicon-o-exclamation-triangle' => 'banned',
                    ]),
                    
                Tables\Columns\TextColumn::make('last_connected_at')
                    ->label('Last Connected')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('Never'),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'disconnected' => 'Disconnected',
                        'connecting' => 'Connecting',
                        'connected' => 'Connected',
                        'banned' => 'Banned',
                    ]),
            ])
            ->actions([                Tables\Actions\Action::make('connect')
                    ->label('Connect')
                    ->icon('heroicon-o-play')
                    ->color('success')
                    ->visible(fn ($record) => $record->status === 'disconnected')
                    ->action(fn ($record) => static::handleConnect($record)),
                    
                Tables\Actions\Action::make('disconnect')
                    ->label('Disconnect')
                    ->icon('heroicon-o-stop')
                    ->color('danger')
                    ->visible(fn ($record) => in_array($record->status, ['connected', 'connecting']))
                    ->requiresConfirmation()
                    ->action(fn ($record) => static::handleDisconnect($record)),
                    
                Tables\Actions\Action::make('qr_code')
                    ->label('Show QR Code')
                    ->icon('heroicon-o-qr-code')
                    ->color('info')
                    ->visible(fn ($record) => $record->status === 'connecting')
                    ->modalContent(fn ($record) => view('filament.modals.enhanced-qr-code', [
                        'record' => $record
                    ]))
                    ->modalWidth('lg')
                    ->modalHeading('WhatsApp QR Code Connection'),
                    
                Tables\Actions\Action::make('refresh_status')
                    ->label('Refresh Status')
                    ->icon('heroicon-o-arrow-path')
                    ->color('gray')
                    ->action(fn ($record) => static::handleRefreshStatus($record)),
                    
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('user_id', Auth::id());
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWhatsAppAccounts::route('/'),
            'create' => Pages\CreateWhatsAppAccount::route('/create'),
            'edit' => Pages\EditWhatsAppAccount::route('/{record}/edit'),
        ];
    }

    /**
     * Handle connect action
     */
    protected static function handleConnect($record): void
    {
        try {
            $enhancedService = app(EnhancedWhatsAppService::class);
            $accountId = (string)$record->id;  // Use simple ID format
            
            // Check if Enhanced Handler is healthy
            if (!$enhancedService->isHealthy()) {
                throw new \RuntimeException('WhatsApp Enhanced Handler is not running. Please start the service first.');
            }
            
            // Start connection process
            $result = $enhancedService->connectAccount($accountId, $record->phone_number);
            
            if ($result['success']) {
                // Update status to connecting
                $record->update(['status' => 'connecting']);
                
                Notification::make()
                    ->title('Connection initiated')
                    ->success()
                    ->body('QR code is being generated. Click "Show QR Code" to see it.')
                    ->send();
            } else {
                throw new \RuntimeException($result['message'] ?? 'Failed to initiate connection');
            }
        } catch (\Exception $e) {
            Notification::make()
                ->title('Connection failed')
                ->danger()
                ->body($e->getMessage())
                ->send();
        }
    }

    /**
     * Handle disconnect action
     */
    protected static function handleDisconnect($record): void
    {
        try {
            $enhancedService = app(EnhancedWhatsAppService::class);
            $accountId = (string)$record->id;  // Use simple ID format
            
            $result = $enhancedService->disconnectAccount($accountId);
            
            if ($result['success']) {
                // Update status to disconnected
                $record->update(['status' => 'disconnected']);
                
                Notification::make()
                    ->title('Account disconnected')
                    ->success()
                    ->send();
            } else {
                throw new \RuntimeException($result['message'] ?? 'Failed to disconnect');
            }
        } catch (\Exception $e) {
            Notification::make()
                ->title('Disconnection failed')
                ->danger()
                ->body($e->getMessage())
                ->send();
        }
    }

    /**
     * Handle refresh status action
     */
    protected static function handleRefreshStatus($record): void
    {
        try {
            $enhancedService = app(EnhancedWhatsAppService::class);
            $accountId = (string)$record->id;  // Use simple ID format
            
            $status = $enhancedService->getConnectionStatus($accountId);
            
            if ($status['success']) {
                // Update status based on Enhanced Handler response
                $newStatus = static::determineStatus($status);
                
                $record->update([
                    'status' => $newStatus,
                    'last_connected_at' => $status['connected'] ? now() : null,
                    'health_check_at' => now(),
                ]);
                
                Notification::make()
                    ->title('Status updated')
                    ->success()
                    ->body("Status: {$newStatus}")
                    ->send();
            } else {
                throw new \RuntimeException($status['error'] ?? 'Failed to get status');
            }
        } catch (\Exception $e) {
            Notification::make()
                ->title('Status refresh failed')
                ->warning()
                ->body($e->getMessage())
                ->send();
        }
    }

    /**
     * Determine status from Enhanced Handler response
     */
    protected static function determineStatus(array $status): string
    {
        if ($status['connected']) {
            return 'connected';
        }
        
        if ($status['hasQR']) {
            return 'connecting';
        }
        
        return 'disconnected';
    }
}
