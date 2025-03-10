<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BouhawsClass extends Model
{
    use HasFactory, HasUuid;

    /**
     * The attributes that are not mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    protected $appends = ['students'];

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

    public function projects()
    {
        return $this->hasMany(Project::class, 'bouhaws_class_id', 'id');
    }

    public function getStudentsAttribute()
    {
        $allEntriesUsers = ProjectEntry::whereIn('project_id', $this->projects()->pluck('id')->toArray())->pluck('user_id')->toArray();

        return User::whereIn('id', $allEntriesUsers)->with('profile')->get();
    }
}
