<?php

namespace App\Filament\Pages;

use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class Settings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    
    protected static ?string $navigationGroup = 'System Configuration';
    
    protected static ?int $navigationSort = 1;

    protected static string $view = 'filament.pages.settings';
    
    public ?array $profileData = [];
    
    public ?array $passwordData = [];

    public function mount(): void
    {
        $user = Auth::user();
        
        $this->profileData = [
            'name' => $user->name,
            'email' => $user->email,
            'timezone' => $user->timezone,
        ];
    }

    public function profileForm(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->label('Full Name'),
                    
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->label('Email Address'),
                    
                Forms\Components\Select::make('timezone')
                    ->options([
                        'UTC' => 'UTC',
                        'Asia/Jakarta' => 'Asia/Jakarta',
                        'Asia/Singapore' => 'Asia/Singapore',
                        'America/New_York' => 'America/New_York',
                        'Europe/London' => 'Europe/London'
                    ])
                    ->default('UTC')
                    ->required()
                    ->label('Timezone'),
            ])
            ->statePath('profileData');
    }

    public function passwordForm(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('current_password')
                    ->password()
                    ->required()
                    ->label('Current Password'),
                    
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->required()
                    ->rule(Password::default())
                    ->label('New Password'),
                    
                Forms\Components\TextInput::make('password_confirmation')
                    ->password()
                    ->required()
                    ->same('password')
                    ->label('Confirm New Password'),
            ])
            ->statePath('passwordData');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('updateProfile')
                ->label('Update Profile')
                ->action('updateProfile')
                ->color('primary'),
                
            Action::make('updatePassword')
                ->label('Update Password')
                ->action('updatePassword')
                ->color('warning'),
        ];
    }

    public function updateProfile(): void
    {
        $data = $this->profileForm->getState();
        
        /** @var User $user */
        $user = Auth::user();
        $user->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'timezone' => $data['timezone'],
        ]);

        Notification::make()
            ->title('Profile updated successfully!')
            ->success()
            ->send();
    }

    public function updatePassword(): void
    {
        $data = $this->passwordForm->getState();
        
        /** @var User $user */
        $user = Auth::user();
        
        if (!Hash::check($data['current_password'], $user->password)) {
            Notification::make()
                ->title('Current password is incorrect')
                ->danger()
                ->send();
            return;
        }

        $user->update([
            'password' => Hash::make($data['password']),
        ]);

        $this->passwordData = [];

        Notification::make()
            ->title('Password updated successfully!')
            ->success()
            ->send();
    }
}
