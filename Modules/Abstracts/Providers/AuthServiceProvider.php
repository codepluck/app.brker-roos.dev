<?php

namespace Modules\Abstracts\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as FrameworkAuthServiceProvider;

abstract class AuthServiceProvider extends FrameworkAuthServiceProvider
{
    protected $policies = [];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
