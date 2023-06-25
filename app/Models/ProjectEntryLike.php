<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectEntryLike extends Model
{
    use HasFactory, HasUuid;

    /**
     * The attributes that are not mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Get the route key for the liquidation.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'uuid';
    }

    public function project_entry()
    {
        return $this->belongsTo(ProjectEntry::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
