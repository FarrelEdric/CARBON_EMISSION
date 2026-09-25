<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email'    => ['nullable', 'string'],
            'username' => ['nullable', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    public function attributes(): array
    {
        return [
            'email'    => 'Email',
            'username' => 'Username',
            'password' => 'Password',
        ];
    }

    /**
     * Attempt login using email or username field.
     * Supports both email and username as login identifier.
     *
     * @throws ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $credential = $this->filled('email') ? $this->input('email') : $this->input('username');
        
        if (empty($credential)) {
            throw ValidationException::withMessages([
                'email' => 'Silakan masukkan Email atau Username Anda.',
            ]);
        }

        $password = $this->input('password');

        // Try login with email first, then username
        $success = Auth::attempt(['email' => $credential, 'password' => $password], $this->boolean('remember'))
            || Auth::attempt(['username' => $credential, 'password' => $password], $this->boolean('remember'));

        if (!$success) {
            RateLimiter::hit($this->throttleKey());
            throw ValidationException::withMessages([
                'email' => 'Email/Username atau password salah.',
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    public function ensureIsNotRateLimited(): void
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    public function throttleKey(): string
    {
        $login = $this->input('email') ?? $this->input('username') ?? '';
        return Str::transliterate(Str::lower($login) . '|' . $this->ip());
    }
}
