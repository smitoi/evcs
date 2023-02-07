<?php

namespace Tests\Feature\Models;

use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompanyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Company::factory(5)->create();
    }

    public function test_cannot_view_companies_as_guest(): void
    {
        $this->withHeader('accept', 'application/json')->get(
            route('company.index'),
        )->assertStatus(401)->assertJsonStructure(['message']);
    }

    public function test_customer_can_view_companies(): void
    {
        $user = User::factory()->customer()->create();

        $this->withHeader('accept', 'application/json')
            ->actingAs($user)->get(
                route('company.index'),
            )->assertStatus(200)->assertJsonStructure(['data'])->assertJsonCount(
                Company::count(), 'data'
            );
    }

    public function test_administrator_can_view_companies(): void
    {
        $user = User::factory()->administrator()->create();

        $this->withHeader('accept', 'application/json')
            ->actingAs($user)->get(
                route('company.index'),
            )->assertStatus(200)->assertJsonStructure(['data'])->assertJsonCount(
                Company::count(), 'data'
            );
    }

    public function test_customer_cannot_create_company(): void
    {
        $user = User::factory()->customer()->create();

        $this->withHeader('accept', 'application/json')
            ->actingAs($user)->post(
                route('company.store'), [
                    'name' => 'Test Company',
                ]
            )->assertStatus(403)->assertJsonStructure(['message']);
    }

    public function test_administrator_can_create_company(): void
    {
        $user = User::factory()->administrator()->create();

        $companiesCount = Company::count();
        $response = $this->withHeader('accept', 'application/json')
            ->actingAs($user)->post(
                route('company.store'), [
                    'name' => 'Test Company',
                ]
            )->assertStatus(201)->assertJsonStructure(['data' => ['uuid', 'name']]);

        $this->assertDatabaseCount('companies', $companiesCount + 1);

        /** @var Company $company */
        $company = Company::query()->where('uuid', $response->json('data.uuid'))->first();

        $this->assertNotNull($company);
        $this->assertEquals('Test Company', $company->name);
    }

    public function test_customer_can_view_company(): void
    {
        $user = User::factory()->customer()->create();

        /** @var Company $company */
        $company = Company::query()->first();

        $response = $this->withHeader('accept', 'application/json')
            ->actingAs($user)->get(
                route('company.show', $company->uuid)
            )->assertStatus(200)->assertJsonStructure(['data' => ['uuid', 'name']]);

        $this->assertEquals($response->json('data.name'), $company->name);
    }

    public function test_administrator_can_view_company(): void
    {
        $user = User::factory()->administrator()->create();

        /** @var Company $company */
        $company = Company::query()->first();

        $response = $this->withHeader('accept', 'application/json')
            ->actingAs($user)->get(
                route('company.show', $company->uuid)
            )->assertStatus(200)->assertJsonStructure(['data' => ['uuid', 'name']]);

        $this->assertEquals($response->json('data.name'), $company->name);
    }

    public function test_customer_cannot_update_company(): void
    {
        $user = User::factory()->customer()->create();

        /** @var Company $company */
        $company = Company::query()->first();

        $this->withHeader('accept', 'application/json')
            ->actingAs($user)->put(
                route('company.update', $company->uuid), [
                    'name' => 'Test Company',
                ]
            )->assertStatus(403)->assertJsonStructure(['message']);
    }

    public function test_administrator_can_update_company(): void
    {
        $user = User::factory()->administrator()->create();

        /** @var Company $company */
        $company = Company::query()->first();

        $response = $this->withHeader('accept', 'application/json')
            ->actingAs($user)->put(
                route('company.update', $company->uuid), [
                    'name' => 'Test Company',
                ]
            )->assertStatus(200)->assertJsonStructure(['data' => ['uuid', 'name']]);

        $this->assertEquals($company->uuid, $response->json('data.uuid'));
        $this->assertEquals('Test Company', $response->json('data.name'));

        /** @var Company $company */
        $company = Company::query()->where('uuid', $response->json('data.uuid'))->first();

        $this->assertNotNull($company);
        $this->assertEquals('Test Company', $company->name);
    }

    public function test_customer_cannot_delete_company(): void
    {
        $user = User::factory()->customer()->create();

        /** @var Company $company */
        $company = Company::query()->first();

        $this->withHeader('accept', 'application/json')
            ->actingAs($user)->delete(
                route('company.destroy', $company->uuid)
            )->assertStatus(403)->assertJsonStructure(['message']);
    }

    public function test_administrator_can_delete_company(): void
    {
        $user = User::factory()->administrator()->create();

        $companiesCount = Company::count();

        /** @var Company $company */
        $company = Company::query()->first();

        $this->withHeader('accept', 'application/json')
            ->actingAs($user)->delete(
                route('company.destroy', $company->uuid)
            )->assertStatus(204)->assertNoContent();

        $this->assertDatabaseCount('companies', $companiesCount - 1);
        $this->assertNull(
            Company::query()->where('uuid', $company->uuid)->first()
        );
    }
}
