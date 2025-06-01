<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        ResetPassword::toMailUsing(function ($user, string $token) {
            return (new MailMessage) // Now this will work
                ->subject('Custom Password Reset')
                ->line('You requested a password reset. Click the button below:')
                ->action('Reset Password', url("/reset-password?token=$token&email={$user->email}"))
                ->line('This link expires in 60 minutes.');
        });
    }

    
}
