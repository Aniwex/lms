<?php

namespace App\Providers;

use App\Models\Integration;
use App\Models\Project;
use App\Models\Source;
use App\Models\User;
use App\Policies\ClaimPolicy;
use App\Policies\IntegrationPolicy;
use App\Policies\ProjectPolicy;
use App\Policies\SourcePolicy;
use App\Policies\TagPolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        User::class => UserPolicy::class,
        Project::class => ProjectPolicy::class,
        Integration::class => IntegrationPolicy::class,
        Source::class => SourcePolicy::class,
        Tag::class => TagPolicy::class,
        Claim::class => ClaimPolicy::class
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //
    }
}
