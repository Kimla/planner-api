<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Event;
use Carbon\Carbon;

class EventsController extends Controller
{
    public function getUpcoming()
    {
        return auth()->user()->events()->future()->get();
    }

    public function getByWeek($week, $year)
    {
        return auth()->user()->events()->weekYear($week, $year)->get();
    }

    public function getByDate($date)
    {
        return auth()->user()->events()->date($date)->get();
    }

    public function store()
    {
        return auth()->user()->events()->create($this->validateRequest());
    }

    public function update(Event $event)
    {
        try {
            $this->authorize('manage', $event);
        } catch (\Throwable $th) {
            return response()->json(null, 403);
        }

        $event->update($this->validateRequest());

        return $event->fresh();
    }

    public function destroy(Event $event)
    {
        try {
            $this->authorize('manage', $event);
        } catch (\Throwable $th) {
            return response()->json(null, 403);
        }

        $event->delete();

        return response()->json(null);
    }

    protected function validateRequest()
    {
        request()->validate([
            'title' => 'required',
            'date' => 'required',
            'description' => 'nullable'
        ]);

        return [
            'user_id' => auth()->user()->id,
            'title' => request('title'),
            'date' => Carbon::parse(request('date'))->format('Y-m-d'),
            'description' => request('description'),
        ];
    }
}
