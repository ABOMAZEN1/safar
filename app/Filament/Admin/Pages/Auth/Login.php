<?php

namespace App\Filament\Admin\Pages\Auth;

use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Filament\Facades\Filament;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\SessionGuard;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Validation\ValidationException;

class Login extends BaseLogin
{
    protected function getEmailFormComponent(): Component
    {
        return TextInput::make('phone_number')
            ->label(__('رقم الهاتف'))
            ->tel()
            ->required()
            ->autofocus();
    }

    protected function getCredentialsFromFormData(array $data): array
    {
        return [
            'phone_number' => $data['phone_number'] ?? null,
            'password' => $data['password'] ?? null,
        ];
    }

    protected function throwFailureValidationException(): never
    {
        throw ValidationException::withMessages([
            'data.phone_number' => __('filament-panels::auth/pages/login.messages.failed'),
        ]);
    }

    public function authenticate(): ?\Filament\Auth\Http\Responses\Contracts\LoginResponse
    {
        // نفس منطق Filament الأساسي مع رسائل أدق
        $data = $this->form->getState();

        /** @var SessionGuard $authGuard */
        $authGuard = Filament::auth();
        $authProvider = $authGuard->getProvider();

        $credentials = $this->getCredentialsFromFormData($data);

        // 1) موجود؟
        $user = $authProvider->retrieveByCredentials($credentials);
        if (! $user) {
            throw ValidationException::withMessages([
                'data.phone_number' => 'رقم الهاتف غير موجود.',
            ]);
        }

        // 2) كلمة السر صحيحة؟
        if (! $authProvider->validateCredentials($user, $credentials)) {
            throw ValidationException::withMessages([
                'data.password' => 'كلمة المرور غير صحيحة.',
            ]);
        }

        // 3) صلاحية الوصول للوحة؟
        if ($user instanceof FilamentUser) {
            if (! $user->canAccessPanel(Filament::getCurrentOrDefaultPanel())) {
                throw ValidationException::withMessages([
                    'data.phone_number' => 'لا تملك صلاحية الدخول إلى لوحة الإدارة.',
                ]);
            }
        }

        if (! $authGuard->attemptWhen($credentials, function (Authenticatable $u): bool {
            return true; // اجتزنا الشروط بالأعلى
        }, $data['remember'] ?? false)) {
            // احتياط
            event(app(Failed::class, ['guard' => property_exists($authGuard, 'name') ? $authGuard->name : '', 'user' => $user, 'credentials' => $credentials]));
            $this->throwFailureValidationException();
        }

        session()->regenerate();

        return app(\Filament\Auth\Http\Responses\Contracts\LoginResponse::class);
    }
}

 