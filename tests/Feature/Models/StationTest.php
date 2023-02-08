<?php

namespace Tests\Feature\Models;

use App\Models\Company;
use App\Models\Station;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Company::factory(2)->create();
        Station::factory(5)->create();
    }

    public function test_cannot_view_stations_as_guest(): void
    {
        $this->withHeader('accept', 'application/json')->get(
            route('station.index'),
        )->assertStatus(401)->assertJsonStructure(['message']);
    }

    public function test_customer_can_view_stations(): void
    {
        $user = User::factory()->customer()->create();

        $this->withHeader('accept', 'application/json')
            ->actingAs($user)->get(
                route('station.index'),
            )->assertStatus(200)->assertJsonStructure(['data'])->assertJsonCount(
                Station::count(), 'data'
            );
    }

    public function test_administrator_can_view_stations(): void
    {
        $user = User::factory()->administrator()->create();

        $this->withHeader('accept', 'application/json')
            ->actingAs($user)->get(
                route('station.index'),
            )->assertStatus(200)->assertJsonStructure(['data'])->assertJsonCount(
                Station::count(), 'data'
            );
    }

    public function test_customer_cannot_create_station(): void
    {
        $user = User::factory()->customer()->create();

        $this->withHeader('accept', 'application/json')
            ->actingAs($user)->post(
                route('station.store'), [
                    'name' => 'Test Station',
                    'address' => 'Street X',
                    'latitude' => 45,
                    'longitude' => 45,
                    'company_uuid' => Station::query()->pluck('uuid')->first()
                ]
            )->assertStatus(403)->assertJsonStructure(['message']);
    }

    public function test_administrator_can_create_station(): void
    {
        $user = User::factory()->administrator()->create();

        $stationsCount = Station::count();
        $response = $this->withHeader('accept', 'application/json')
            ->actingAs($user)->post(
                route('station.store'), [
                    'name' => 'Test Station',
                    'address' => 'Street X',
                    'latitude' => 45,
                    'longitude' => 45,
                    'company_uuid' => Company::query()->pluck('uuid')->first()
                ]
            )->assertStatus(201)->assertJsonStructure(['data' => ['uuid', 'name', 'address', 'latitude', 'longitude', 'company_uuid']]);

        $this->assertDatabaseCount('stations', $stationsCount + 1);

        /** @var Station $station */
        $station = Station::query()->where('uuid', $response->json('data.uuid'))->first();

        $this->assertNotNull($station);
        $this->assertEquals('Test Station', $station->name);
    }

    public function test_customer_can_view_station(): void
    {
        $user = User::factory()->customer()->create();

        /** @var Station $station */
        $station = Station::query()->first();

        $response = $this->withHeader('accept', 'application/json')
            ->actingAs($user)->get(
                route('station.show', $station->uuid)
            )->assertStatus(200)->assertJsonStructure(['data' => ['uuid', 'name', 'address', 'company_uuid']]);

        $this->assertEquals($response->json('data.name'), $station->name);
    }

    public function test_administrator_can_view_station(): void
    {
        $user = User::factory()->administrator()->create();

        /** @var Station $station */
        $station = Station::query()->first();

        $response = $this->withHeader('accept', 'application/json')
            ->actingAs($user)->get(
                route('station.show', $station->uuid)
            )->assertStatus(200)->assertJsonStructure(['data' => ['uuid', 'name', 'address', 'latitude', 'longitude', 'company_uuid']]);


        $this->assertEquals($response->json('data.name'), $station->name);
    }

    public function test_customer_cannot_update_station(): void
    {
        $user = User::factory()->customer()->create();

        /** @var Station $station */
        $station = Station::query()->first();

        $this->withHeader('accept', 'application/json')
            ->actingAs($user)->put(
                route('station.update', $station->uuid), [
                    'name' => 'Test Station',
                    'address' => 'Street X',
                    'latitude' => 45,
                    'longitude' => 45,
                    'company_uuid' => Company::query()->pluck('uuid')->first()
                ]
            )->assertStatus(403)->assertJsonStructure(['message']);
    }

    public function test_administrator_can_update_station(): void
    {
        $user = User::factory()->administrator()->create();

        /** @var Station $station */
        $station = Station::query()->first();

        $response = $this->withHeader('accept', 'application/json')
            ->actingAs($user)->put(
                route('station.update', $station->uuid), [
                    'name' => 'Test Station',
                    'address' => 'Street X',
                    'latitude' => 45,
                    'longitude' => 45,
                    'company_uuid' => Company::query()->pluck('uuid')->first()
                ]
            )->assertStatus(200)->assertJsonStructure(['data' => ['uuid', 'name', 'address', 'latitude', 'longitude', 'company_uuid']]);

        $this->assertEquals($station->uuid, $response->json('data.uuid'));
        $this->assertEquals('Test Station', $response->json('data.name'));

        /** @var Station $station */
        $station = Station::query()->where('uuid', $response->json('data.uuid'))->first();

        $this->assertNotNull($station);
        $this->assertEquals('Test Station', $station->name);
    }

    public function test_customer_cannot_delete_station(): void
    {
        $user = User::factory()->customer()->create();

        /** @var Station $station */
        $station = Station::query()->first();

        $this->withHeader('accept', 'application/json')
            ->actingAs($user)->delete(
                route('station.destroy', $station->uuid)
            )->assertStatus(403)->assertJsonStructure(['message']);
    }

    public function test_administrator_can_delete_company(): void
    {
        $user = User::factory()->administrator()->create();

        $stationsCount = Station::count();

        /** @var Station $station */
        $station = Station::query()->first();

        $this->withHeader('accept', 'application/json')
            ->actingAs($user)->delete(
                route('station.destroy', $station->uuid)
            )->assertStatus(204)->assertNoContent();

        $this->assertDatabaseCount('stations', $stationsCount - 1);
        $this->assertNull(
            Station::query()->where('uuid', $station->uuid)->first()
        );
    }
}
