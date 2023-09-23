<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ProjectEntry extends Model
{
    use HasFactory, HasUuid;

    protected $appends = [
        'images',
        'category',
        'liked',
        'bookmarked',
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

    public function getCategoryAttribute()
    {
        if ($this->project_category_id) {
            return ProjectCategory::where('id', $this->project_category_id)->first();
        } else if ($this->project) {
            return ProjectCategory::where('id', $this->project->project_category_id)->first();
        } else {
            return null;
        }
    }

    public function getLikedAttribute()
    {
        $userLike = ProjectEntryLike::where('user_id', Auth::user()->id)->where('project_entry_id', $this->id)->first();

        return $userLike ? true : false;
    }

    public function getBookmarkedAttribute()
    {
        $userBookmark = ProjectEntryBookmark::where('user_id', Auth::user()->id)->where('project_entry_id', $this->id)->first();

        return $userBookmark ? true : false;
    }

}
