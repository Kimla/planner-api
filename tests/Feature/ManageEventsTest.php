<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class ManageEventsTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
    }

    /** @test */
    public function unauthenticaded_users_cannot_create_events()
    {
        $attributes = factory('App\Event')->raw();

        $this->postJson('/api/events', $attributes)
            ->assertStatus(401);

        $this->assertDatabaseMissing('events', $attributes);
    }

    /** @test */
    public function a_user_can_only_updated_own_events()
    {
        $this->withoutExceptionHandling();

        $event = factory('App\Event')->create();

        $newAttributes = [
            'title' => 'Updated title',
            'description' => 'Updated desc',
            'date' => $this->faker->dateTimeBetween('+0 days', '+1 years')->format('Y-m-d'),
        ];

        $this->actingAs($this->user)
            ->put('/api/events/' . $event->id, $newAttributes)
            ->assertStatus(401);
    }

    /** @test */
    public function a_user_can_only_get_own_events()
    {
        $this->withoutExceptionHandling();

        factory('App\Event', 2)->create([
            'user_id' => $this->user->id,
            'date' => $this->faker->dateTimeBetween('+1 days', '+1 years')
        ]);
        factory('App\Event')->create(['date' => $this->faker->dateTimeBetween('+1 days', '+1 years')]);

        $res = $this->actingAs($this->user)
            ->get('/api/events')
            ->assertStatus(200);

        $this->assertCount(2, $res->json());
    }

    /** @test */
    public function can_get_upcoming_events()
    {
        $this->withoutExceptionHandling();

        factory('App\Event', 2)->create([
            'date' => $this->faker->dateTimeBetween('+1 days', '+1 years'),
            'user_id' => $this->user->id,
        ]);
        factory('App\Event')->create(['date' => $this->faker->dateTimeBetween('-1 years', '-1 days')]);

        $res = $this->actingAs($this->user)
            ->get('/api/events')
            ->assertStatus(200);

        $this->assertCount(2, $res->json());
    }

    /** @test */
    public function can_get_events_by_week()
    {
        $this->withoutExceptionHandling();

        factory('App\Event', 2)->create([
            'user_id' => $this->user->id,
            'date' => Carbon::now()->weekday(1)->format('Y-m-d')
        ]);

        $currentWeek = Carbon::now()->week();
        $currentYear = Carbon::now()->format('Y');

        $res = $this->actingAs($this->user)
            ->get("/api/events/week/$currentWeek/$currentYear")
            ->assertStatus(200);

        $this->assertCount(2, $res->json());
    }

    /** @test */
    public function can_get_events_by_date()
    {
        $this->withoutExceptionHandling();

        $date = $this->faker->dateTimeBetween('+0 days', '+1 years')->format('Y-m-d');
        factory('App\Event', 2)->create(['date' => $this->faker->dateTimeBetween('-1 years', '-1 days')]);
        factory('App\Event', 2)->create([
            'date' => $date,
            'user_id' => $this->user->id,
        ]);

        $res = $this->actingAs($this->user)
            ->get('/api/events/date/' . $date)
            ->assertStatus(200);

        $this->assertCount(2, $res->json());
    }

    /** @test */
    public function a_user_can_create_a_event()
    {
        $this->withoutExceptionHandling();

        $attributes = factory('App\Event')->raw();

        $this->actingAs($this->user)
            ->post('/api/events', $attributes)
            ->assertStatus(201);
    }

    /** @test */
    public function a_user_can_update_a_event()
    {
        $this->withoutExceptionHandling();

        $event = factory('App\Event')->create(['user_id' => $this->user->id]);

        $newAttributes = [
            'title' => 'Updated title',
            'description' => 'Updated desc',
            'date' => $this->faker->dateTimeBetween('+0 days', '+1 years')->format('Y-m-d'),
        ];

        $this->actingAs($this->user)
            ->put('/api/events/' . $event->id, $newAttributes)
            ->assertStatus(200);
    }

    /** @test */
    public function authenticaded_users_can_only_delete_own_events()
    {
        $this->withoutExceptionHandling();

        $event = factory('App\Event')->create(['user_id' => $this->user->id]);
        
        $this->actingAs($this->user)
            ->delete("/api/events/{$event->id}")
            ->assertOk();

        $secondEvent = factory('App\Event')->create();

        $this->actingAs($this->user)
            ->delete("/api/events/{$secondEvent->id}")
            ->assertStatus(401);

        $this->assertDatabaseHas('events', $secondEvent->only('id'));
    }

    /** @test */
    public function a_event_requries_a_title()
    {    
        $this->actingAs($this->user)
            ->post('/api/events', factory('App\Event')->raw(['title' => null]))
            ->assertSessionHasErrors();
    }

    /** @test */
    public function a_event_requries_a_date()
    {
        $this->actingAs($this->user)
            ->post('/api/events', factory('App\Event')->raw(['date' => null]))
            ->assertSessionHasErrors();
    }

    /** @test */
    public function a_event_doesnt_require_a_description()
    {
        $this->actingAs($this->user)
            ->post('/api/events', factory('App\Event')->raw(['description' => null]))
            ->assertStatus(201);
    }
}
