<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Repositories\CommonRepository as CommonRepo;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // for update member timezone
        // $timezone = request()->header('timezone');

        // \Log::info('boot method timezone == '.print_r($timezone, true));

        // $auth_user = \Helper::get_authorize_user();

        // if ($auth_user) {
        //     CommonRepo::update_member_timezone($timezone, $auth_user->association_id, $auth_user->association_type_term);
        // }
        // End update member timezone
    }
}
