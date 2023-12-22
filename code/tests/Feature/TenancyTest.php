<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Services\TenancyService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Session;
use ReflectionClass;
use ReflectionException;
use Tests\TestCase;

class TenancyTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
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

    public function test_the_tenancy_domain_is_configured()
    {
        $this->assertTrue(config()->has('domains.tenancy'));
        $this->assertSame('http://' . config('domains.tenancy'), route('tenancy.index'));
    }

    public function test_the_tenants_index_returns_a_503_response_if_the_tenant_is_not_provided()
    {
        $response = $this->get(route('tenancy.index'));

        $response->assertStatus(503)
            ->assertJson(['message' => 'Tenant not provided']);
    }

    public function test_the_tenants_index_returns_a_503_response_if_the_tenant_is_not_configured()
    {
        $response = $this->withHeader('x-tenant', 'not-exists')->get(route('tenancy.index'));

        $response->assertStatus(503)
            ->assertJson(['message' => 'Tenant not configured']);
    }

    public function test_the_tenants_index_returns_a_successful_response(): void
    {
        Tenant::factory()->create(['code' => 'test']);
        Queue::fake();

        $response = $this->withHeader('x-tenant', 'test')->get(route('tenancy.index'));

        $response->assertStatus(200)
            ->assertJson(['message' => 'It works!']);
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
