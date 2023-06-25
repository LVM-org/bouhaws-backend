<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectEntry extends Model
{
    use HasFactory, HasUuid;

    protected $appends = [
        'images',
    ];

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

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getImagesAttribute()
    {
        return $this->attributes['images'] ? json_decode($this->attributes['images'], true) : [];
    }

    public function likes()
    {
        return $this->hasMany(ProjectEntryLike::class);
    }

    public function bookmarks()
    {
        return $this->hasMany(ProjectEntryBookmark::class);
    }

    public function comments()
    {
        return $this->hasMany(ProjectEntryComment::class);
    }

}
