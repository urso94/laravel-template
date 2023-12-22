<?php

namespace Tests\Feature;

use App\Services\TenancyService;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use ReflectionClass;
use ReflectionException;
use Tests\TestCase;

class TenancyTest extends TestCase
{
    /**
     * A basic feature test example.
     * @throws ReflectionException
     */
    public function test_example(): void
    {
        TenancyService::init('my-tenant');
        $this->assertSame('my-tenant', DB::connection('tenancy')->getConfig('database'));
        $this->assertSame(
            '/opt/storage/framework/cache/data/my-tenant',
            Cache::driver('file')->getStore()->getDirectory()
        );
        $this->assertSame('my-tenant', Session::driver('file')->getName());
        $this->assertSame('/opt/storage/framework/sessions/my-tenant', $this->getSessionPath());
        $this->assertSame('/opt/storage/logs/my-tenant.log', config('logging.channels.single.path'));
        $this->assertTrue(App::has(TenancyService::class));
    }

    /**
     * @throws ReflectionException
     */
    protected function getSessionPath(): mixed
    {
        $reflectedClass = new ReflectionClass(Session::driver('file')->getHandler());
        $reflection = $reflectedClass->getProperty('path');

        return $reflection->getValue(Session::driver('file')->getHandler());
    }
}
