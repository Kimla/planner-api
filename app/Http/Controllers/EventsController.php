<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Event;

class EventsController extends Controller
{
    public function getUpcoming()
    {
        return Event::future()->get();
    }

    public function getByWeek($week, $year)
    {
        return Event::weekYear($week, $year)->get();
    }

    public function getByDate($date)
    {
        return Event::date($date)->get();
    }

    public function store()
    {
        return Event::create($this->validateRequest());
    }

    public function update(Event $event)
    {
        $event->update($this->validateRequest());

        return $event->fresh();
    }

    public function destroy(Event $event)
    {
        $event->delete();

        return response()->json(null);
    }

    protected function validateRequest()
    {
        return request()->validate([
            'title' => 'required',
            'date' => 'required',
            'description' => 'nullable'
        ]);
    }
}
