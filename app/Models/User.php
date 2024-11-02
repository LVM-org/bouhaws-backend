<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Laravel\Cashier\Billable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Model implements AuthenticatableContract, AuthorizableContract, JWTSubject
{
    use Authenticatable, Authorizable, HasFactory, HasUuid, CanResetPassword, Billable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'username',
        'email_verified_at',
        'otp',
        'otp_expires_at',
        'phone_number',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    protected $appends = ['conversations', 'my_classes'];

    /**
     * Get the route key for the liquidation.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'uuid';
    }

    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }

    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    public function getConversationsAttribute()
    {
        $conversationMemberships = ConversationMember::where('user_uuid', $this->uuid)->pluck('conversation_uuid')->toArray();

        return Conversation::whereIn('uuid', $conversationMemberships)->get();
    }

    public function getMyClassesAttribute()
    {
        if ($this->profile->type == 'student') {
            $allProjectsId = $this->project_entries()->pluck('project_id')->toArray();
            $allClassesId = Project::whereIn('id', $allProjectsId)->pluck('bouhaws_class_id')->toArray();
            return BouhawsClass::whereIn('id', $allClassesId)->get();
        } else {
            return BouhawsClass::where('user_id', $this->id)->get();
        }
    }

    public function project_entries()
    {
        return $this->hasMany(ProjectEntry::class);
    }

    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    public function project_bookmarked()
    {
        return $this->hasMany(ProjectEntryBookmark::class);
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

}
