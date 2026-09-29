<?php

namespace App\Providers;

use App\Models\Contract;
use App\Models\Exchange;
use App\Models\Message;
use App\Models\Profile;
use App\Models\Service;
use App\Models\User;
use App\Policies\ContractPolicy;
use App\Policies\ExchangePolicy;
use App\Policies\MessagePolicy;
use App\Policies\ProfilePolicy;
use App\Policies\ServicePolicy;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $prefix = config('app.route_prefix') ?: 'alphaview';
        View::share('base', "/{$prefix}");

        Gate::policy(Service::class, ServicePolicy::class);
        Gate::policy(Message::class, MessagePolicy::class);
        Gate::policy(Exchange::class, ExchangePolicy::class);
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Contract::class, ContractPolicy::class);
        Gate::policy(Profile::class, ProfilePolicy::class);

        Gate::define('admin', function (User $user) {
            return $user->isAdmin();
        });
    }
}
