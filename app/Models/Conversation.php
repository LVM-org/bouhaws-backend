<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Conversation extends Model
{
    use HasFactory, HasUuid;

    /**
     * The attributes that are not mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    protected $appends = ['associated_users', 'other_member', 'last_message'];

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
        $authUser = Auth::user();
        return ConversationMember::where('conversation_uuid', $this->uuid)->where('user_uuid', '!=', $authUser->uuid)->first()->user;
    }

    public function getLastMessageAttribute()
    {
        return ConversationMessage::where('conversation_id', $this->id)->orderBy('created_at', 'desc')->first();
    }
}
