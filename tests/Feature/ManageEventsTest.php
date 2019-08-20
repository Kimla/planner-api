<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class ManageEventsTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function can_get_upcoming_events()
    {
        $this->withoutExceptionHandling();

        factory('App\Event', 2)->create();
        factory('App\Event')->create(['date' => $this->faker->dateTimeBetween('-1 years', '-1 days')]);

        $res = $this->get('/api/events')
            ->assertStatus(200);

        $this->assertCount(2, $res->json());
    }

    /** @test */
    public function can_get_events_by_week()
    {
        $this->withoutExceptionHandling();

        factory('App\Event', 2)->create(['date' => Carbon::now()->weekday(1)->format('Y-m-d')]);

        $currentWeek = Carbon::now()->week();
        $currentYear = Carbon::now()->format('Y');

        $res = $this->get("/api/events/week/$currentWeek/$currentYear")
            ->assertStatus(200);

        $this->assertCount(2, $res->json());
    }

    /** @test */
    public function can_get_events_by_date()
    {
        $this->withoutExceptionHandling();

        $date = $this->faker->dateTimeBetween('+0 days', '+1 years')->format('Y-m-d');
        factory('App\Event', 2)->create(['date' => $this->faker->dateTimeBetween('-1 years', '-1 days')]);
        factory('App\Event', 2)->create(['date' => $date]);

        $res = $this->get('/api/events/date/' . $date)
            ->assertStatus(200);

        $this->assertCount(2, $res->json());
    }

    /** @test */
    public function a_event_can_be_created()
    {
        $this->withoutExceptionHandling();

        $attributes = factory('App\Event')->raw();

        $this->post('/api/events', $attributes)
            ->assertStatus(201);

        $this->assertDatabaseHas('events', $attributes);
    }

    /** @test */
    public function a_event_can_be_updated()
    {
        $this->withoutExceptionHandling();

        $event = factory('App\Event')->create();

        $newAttributes = [
            'title' => 'Updated title',
            'description' => 'Updated desc',
            'date' => $this->faker->dateTimeBetween('+0 days', '+1 years')->format('Y-m-d'),
        ];

        $this->put('/api/events/' . $event->id, $newAttributes)->assertStatus(200);

        $this->assertDatabaseHas('events', $newAttributes);
    }

    /** @test */
    public function a_event_can_be_deleted()
    {
        $this->withoutExceptionHandling();

        $event = factory('App\Event')->create();

        $this->delete("/api/events/{$event->id}")
            ->assertOk();

        $this->assertDatabaseMissing('events', $event->only('id'));
    }

    /** @test */
    public function a_event_requries_a_title()
    {    
        $this->post('/api/events', factory('App\Event')->raw(['title' => null]))
            ->assertSessionHasErrors();
    }

    /** @test */
    public function a_event_requries_a_date()
    {
        $this->post('/api/events', factory('App\Event')->raw(['date' => null]))
            ->assertSessionHasErrors();
    }

    /** @test */
    public function a_event_doesnt_require_a_description()
    {
        $this->post('/api/events', factory('App\Event')->raw(['description' => null]))
            ->assertStatus(201);
    }
}
