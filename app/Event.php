<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class Event extends Model
{
    protected $guarded = [];

    protected static function boot() 
    {
        parent::boot();

        static::addGlobalScope('order', function (Builder $builder) {
            $builder
                ->orderBy('date', 'asc')
                ->orderBy('id', 'desc');
        });
    }

    public function scopeFuture($query) 
    {
        return $query->where('date', '>=', date('Y-m-d'));
    }

    public function scopeDate($query, $date) 
    {
        return $query->where('date', $date);
    }

    public function scopeWeekYear($query, $week, $year) 
    {
        $date = Carbon::now()
            ->setISODate($year, $week);

        $startOfWeek = $date->startOfWeek()->format('Y-m-d');
        $endOfWeek = $date->endOfWeek()->format('Y-m-d');

        return $query->whereBetween('date', [$startOfWeek, $endOfWeek]);
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
