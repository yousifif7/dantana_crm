<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        \App\Models\InventoryItem::class => \App\Policies\InventoryPolicy::class,
        \App\Models\Department::class => \App\Policies\DepartmentPolicy::class,
        \App\Models\Transaction::class => \App\Policies\TransactionPolicy::class,
        \App\Models\User::class => \App\Policies\UserPolicy::class,
        \App\Models\ProductionRecord::class => \App\Policies\ProductionPolicy::class,
        \App\Models\Process::class => \App\Policies\ProcessPolicy::class,
        \App\Models\PurchaseOrder::class => \App\Policies\PurchaseOrderPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Optional: you can add any additional gates here if needed.
    }
}
