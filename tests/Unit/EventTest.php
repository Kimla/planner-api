<?php

namespace Tests\Unit;

use App\Event;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EventTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function can_query_future_events()
    {
        factory('App\Event', 2)->create();
        factory('App\Event')->create(['date' => $this->faker->dateTimeBetween('-1 years', '-1 days')]);

        $events = Event::future()->get();

        $this->assertCount(2, $events);
    }

    /** @test */
    public function can_query_events_based_on_date()
    {
        $date = $this->faker->dateTimeBetween('+0 days', '+1 years')->format('Y-m-d');
        factory('App\Event', 2)->create(['date' => $this->faker->dateTimeBetween('-1 years', '-1 days')]);
        factory('App\Event', 2)->create(['date' => $date]);

        $events = Event::date($date)->get();

        $this->assertCount(2, $events);
    }

    /** @test */
    public function can_query_events_based_on_week_and_year()
    {
        factory('App\Event', 2)->create(['date' => $this->faker->dateTimeBetween('-1 years', '-1 days')]);
        factory('App\Event', 2)->create(['date' => Carbon::now()->addYears(1)->weekday(1)->format('Y-m-d')]);

        $week = Carbon::now()->week();
        $year = Carbon::now()->addYears(1)->format('Y');

        $events = Event::weekYear($week, $year)->get();

        $this->assertCount(2, $events);

        factory('App\Event', 3)->create(['date' => Carbon::now()->addDays(7)->weekday(7)->format('Y-m-d')]);
        $events = Event::weekYear(Carbon::now()->addDays(7)->week(), Carbon::now()->format('Y'))->get();

        $this->assertCount(3, $events);
    }

    /** @test */
    public function always_orders_by_date()
    {
        factory('App\Event')->create(['date' => Carbon::now()->format('Y-m-d')]);
        $firstEvent = factory('App\Event')->create(['date' => Carbon::now()->weekday(-1)->format('Y-m-d')]);
        factory('App\Event')->create(['date' => Carbon::now()->weekday(1)->format('Y-m-d')]);

        $events = Event::all();
        
        $this->assertEquals($events[0]->id, $firstEvent->id);
    }
}
