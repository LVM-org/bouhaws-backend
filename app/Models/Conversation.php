<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use HasFactory, HasUuid;

    /**
     * The attributes that are not mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    protected $appends = ['associated_users', 'other_member'];

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

    public function getAssociatedUsersAttribute()
    {
        $associated_users_uuid = $this->associated_users_uuid ? json_decode($this->associated_users_uuid) : [];

        return User::whereIn('uuid', $associated_users_uuid)->get();
    }

    public function getOtherMemberAttribute()
    {
        return ConversationMember::where('user_uuid', '!=', $this->user->uuid)->user()->first();
    }
}
