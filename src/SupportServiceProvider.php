<?php

namespace MEkramy\Support;

use MEkramy\Support\Macros;
use MEkramy\Support\Validators;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

class SupportServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot(): void
    {
        // Register macros
        (new Macros())->register();

        Validator::extend('username', Validators::class . '@username');
        Validator::replacer('username', Validators::class . '@usernameReplacer');
        Validator::extend('tel', Validators::class . '@tel');
        Validator::replacer('tel', Validators::class . '@telReplacer');
        Validator::extend('mobile', Validators::class . '@mobile');
        Validator::replacer('mobile', Validators::class . '@mobileReplacer');
        Validator::extend('postalcode', Validators::class . '@postalcode');
        Validator::replacer('postalcode', Validators::class . '@postalcodeReplacer');
        Validator::extend('id', Validators::class . '@identifier');
        Validator::replacer('id', Validators::class . '@identifierReplacer');
        Validator::extend('unsigned', Validators::class . '@unsigned');
        Validator::replacer('unsigned', Validators::class . '@unsignedReplacer');
        Validator::extend('range', Validators::class . '@range');
        Validator::replacer('range', Validators::class . '@rangeReplacer');
        Validator::extend('maxlength', Validators::class . '@maxlength');
        Validator::replacer('maxlength', Validators::class . '@maxlengthReplacer');
        Validator::extend('idnumber', Validators::class . '@idnumber');
        Validator::replacer('idnumber', Validators::class . '@idnumberReplacer');
        Validator::extend('nationalcode', Validators::class . '@nationalcode');
        Validator::replacer('nationalcode', Validators::class . '@nationalcodeReplacer');
        Validator::extend('jalali', Validators::class . '@jalali');
        Validator::replacer('jalali', Validators::class . '@jalaliReplacer');
        Validator::extend('numericarray', Validators::class . '@numericarray');
        Validator::replacer('numericarray', Validators::class . '@numericarrayReplacer');
        Validator::extend('length', Validators::class . '@length');
        Validator::replacer('length', Validators::class . '@lengthReplacer');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register(): void
    {
        $this->loadTranslationsFrom(__DIR__ . '/translations/validations.php', 'mekramy-support');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/translations/validations.php' => resource_path('lang/vendor/mekramy-support'),
            ]);
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides(): array
    {
        return [];
    }
}
