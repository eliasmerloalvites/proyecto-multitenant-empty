<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Stancl\Tenancy\Events\TenantCreated;
use App\Listeners\CreateTenantDatabase;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [];
}
