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

    protected $appends = ['user_entry'];

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

    public function getUserEntryAttribute()
    {
        return ProjectEntry::where('user_id', Auth::user()->id)->where('project_id', $this->id)->first();
    }

    public function milestones()
    {
        return $this->hasMany(ProjectMilestone::class);
    }

    public function bouhawsclass()
    {
        return $this->belongsTo(BouhawsClass::class, 'bouhaws_class_id', 'id');
    }
}
