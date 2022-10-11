<?php

namespace Aparlay\Core\Tests;

use Aparlay\Core\Admin\Models\User;
use Aparlay\Core\Models\Enums\UserType;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Laravel\Dusk\Browser;
use Laravel\Dusk\TestCase as BaseTestCase;

abstract class DuskTestCase extends BaseTestCase
{
    use CreatesApplication;

    protected $superAdminUser;

    protected static bool $isSeeded = false;

    public function setUp(): void
    {
        parent::setUp();

        $this->app->make('config')->set('app.url', env('ADMIN_URL'));
        $this->app->make('config')->set('app.is_testing', true);

        if (! static::$isSeeded) {
            $this->artisan('db:seed', ['--class' => '\Aparlay\Core\Database\Seeders\DatabaseSeeder', '--database' => 'testing']);

            $this->artisan('migrate', ['--path' => 'packages/Aparlay/Core/database/migrations', '--database' => 'testing']);
            $this->artisan('migrate', ['--path' => 'packages/Aparlay/Payment/database/migrations', '--database' => 'testing']);
            $this->artisan('migrate', ['--path' => 'packages/Aparlay/Payout/database/migrations', '--database' => 'testing']);
            $this->artisan('migrate', ['--path' => 'packages/Aparlay/Chat/database/migrations', '--database' => 'testing']);

            static::$isSeeded = true;
        }

        $this->superAdminUser = User::where('type', UserType::ADMIN->value)->first();
        $this->superAdminUser->assignRole('super-administrator');

        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->superAdminUser, 'admin');
        });
    }

    /**
     * Prepare for Dusk test execution.
     *
     * @beforeClass
     * @return void
     */
    public static function prepare()
    {
        if (! static::runningInSail()) {
            static::startChromeDriver();
        }
    }

    /**
     * Create the RemoteWebDriver instance.
     *
     * @return \Facebook\WebDriver\Remote\RemoteWebDriver
     */
    protected function driver()
    {
        $options = (new ChromeOptions())->addArguments(collect([
            '--window-size=1920,1080',
        ])->unless($this->hasHeadlessDisabled(), function ($items) {
            return $items->merge([
                '--disable-gpu',
                '--headless',
                '--no-sandbox',
            ]);
        })->all());

        return RemoteWebDriver::create(
            env('DUSK_DRIVER_URL', 'http://selenium:4444/wd/hub'),
            DesiredCapabilities::chrome()->setCapability(
                ChromeOptions::CAPABILITY,
                $options
            )
        );
    }

    /**
     * Determine whether the Dusk command has disabled headless mode.
     *
     * @return bool
     */
    protected function hasHeadlessDisabled()
    {
        return isset($_SERVER['DUSK_HEADLESS_DISABLED']) ||
               isset($_ENV['DUSK_HEADLESS_DISABLED']);
    }
}
