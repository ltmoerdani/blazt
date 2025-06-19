<?php

namespace App\Filament\Pages\Auth;

use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Auth\PasswordReset\RequestPasswordReset as BaseRequestPasswordReset;

class RequestPasswordReset extends BaseRequestPasswordReset
{
    /**
     * @return array<int | string, string | Form>
     */
    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema([
                        $this->getEmailFormComponent(),
                    ])
                    ->statePath('data'),
            ),
        ];
    }

    protected function getEmailFormComponent(): Component
    {
        return TextInput::make('email')
            ->label('Email')
            ->email()
            ->required()
            ->autocomplete()
            ->autofocus()
            ->extraInputAttributes(['tabindex' => 1]);
    }

    public function getView(): string
    {
        return 'filament.pages.auth.password-reset.request-password-reset';
    }

    public function getHeading(): string
    {
        return 'Reset Password';
    }

    public function getSubheading(): ?string
    {
        return 'Masukkan email Anda untuk mendapatkan link reset password';
    }

    protected function getLayoutData(): array
    {
        return [
            'hasTopbar' => false,
        ];
    }
}
