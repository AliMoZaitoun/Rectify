<?php

namespace App\Providers;

use App\Events\ComplaintStatusUpdated;
use App\Events\ComplaintReplyAdded;
use App\Listeners\SendComplaintNotification;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{

    public function register(): void
    {
        $this->app->bind(\App\Services\TransactionService::class, \App\Services\Transaction::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Event::listen(
            ComplaintStatusUpdated::class,
            SendComplaintNotification::class
        );

        Event::listen(
            ComplaintReplyAdded::class,
            SendComplaintNotification::class
        );
    }
}
