<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Project extends Model
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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(ProjectCategory::class, 'project_category_id', 'id');
    }

    public function entries()
    {
        return $this->hasMany(ProjectEntry::class);
    }

    public function user_entry()
    {
        if (Auth::user()) {
            return ProjectEntry::where('project_id', $this->id)->where('user_id', Auth::user()->id)->first();
        } else {
            return null;
        }

    }

    public function milestones()
    {
        return $this->hasMany(ProjectMilestone::class);
    }
}
