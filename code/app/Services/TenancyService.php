<?php

namespace App\Services;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class TenancyService
{
    /**
     * Initialize the service
     *
     * @param string $tenant The tenant used to configure the service
     * @return TenancyService The tenancy service instance
     */
    public static function init(string $tenant): TenancyService
    {
        return new TenancyService($tenant);
    }

    private function __construct(protected readonly string $tenant)
    {
        $this->boot();
    }

    /**
     * Boot the tenant configuration setting:
     * - the database name for the connection `tenant`
     * - the folder and the prefix used by the cache for the tenant
     * - the sessions folder used by the tenant
     */
    public function boot(): void
    {
        $code = strtolower($this->tenant);

        if (!App::has(self::class)) {
            App::instance(self::class, $this);
        }

        $this->configureDatabase($code);
        $this->configureCache($code);
        $this->configureSession($code);
        $this->configureLogs($code);
    }

    /**
     * Configure the connection `tenant` database using the tenant code
     *
     * @param string $tenant The code of the tenant used to set the database name in the connection
     */
    protected function configureDatabase(string $tenant): void
    {
        config()->set('database.connections.tenancy.database', strtolower($tenant));
        DB::purge('tenant');
    }

    /**
     * Configure the tenant application cache using the tenant code
     *
     * @param string $tenant The code of the tenant used for the prefix and the folder name
     */
    protected function configureCache(string $tenant): void
    {
        config()->set('cache.stores.file.path', '/opt/storage/framework/cache/data/' . $tenant);
        Cache::purge();
    }

    /**
     * Configure the tenant application session using the tenant code
     *
     * @param string $tenant The code of the tenant used for the name of the folder
     */
    protected function configureSession(string $tenant): void
    {
        config()->set('session.files', '/opt/storage/framework/sessions/' . $tenant);
        Session::driver('file')->setName($tenant);
    }

    /**
     * Configure the tenant logs
     *
     * @param string $tenant The code of the tenant used for the name of the folder
     */
    protected function configureLogs(string $tenant): void
    {
        config()->set('logging.channels.single.path', '/opt/storage/logs/' . $tenant . '.log');
    }
}
