<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Role;
use App\Observers\AuditTrailObserver;

use Illuminate\Auth\Notifications\ResetPassword;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Role::observe(AuditTrailObserver::class);
        ResetPassword::createUrlUsing(function (object $notifiable, string $token) {
            return config('app.frontend_url')."/password-reset/$token?email={$notifiable->getEmailForPasswordReset()}";
        });
    }
}
