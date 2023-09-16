<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
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
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function project_entry()
    {
        return $this->belongsTo(ProjectEntry::class, 'model_type_id', 'uuid');
    }

    public function project_entry_comment()
    {
        return $this->belongsTo(ProjectEntryComment::class, 'model_type_id', 'uuid');
    }

    public function project_entry_like()
    {
        return $this->belongsTo(ProjectEntryLike::class, 'model_type_id', 'uuid');
    }

}
