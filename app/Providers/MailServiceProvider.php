<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Mail\MailManager;
use App\Mail\Transport\PhpMailTransport;

class MailServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->app->extend('mail.manager', function (MailManager $manager) {
            $manager->extend('phpmail', function () {
                return new PhpMailTransport();
            });
            
            return $manager;
        });
    }
}