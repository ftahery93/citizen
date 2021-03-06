<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\Event' => [
            'App\Listeners\EventListener',
        ],

        // Revoke Old Tokens (for now let generate multiple access tokens)
        // 'Laravel\Passport\Events\AccessTokenCreated' => [
        //     'App\Listeners\RevokeOldTokens',
        // ],

        // Prune Old Tokens (for now let generate multiple access tokens)
        // 'Laravel\Passport\Events\RefreshTokenCreated' => [
        //     'App\Listeners\PruneOldTokens',
        // ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
