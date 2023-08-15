<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory, HasUuid;

    /**
     * The attributes that are not mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    protected $appends = ['enrolled_courses', 'enrolled_classes', 'photo_url'];

    /**
     * Get the route key for the liquidation.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'uuid';
    }

    public function getPhotoUrlAttribute()
    {
        if ($this->attributes['photo_url']) {
            return $this->attributes['photo_url'];
        } else {
            $avatarAvailable = [1, 2, 3, 4, 5, 6, 7, 8];
            $randomKey = array_rand($avatarAvailable);

            $profilePhotoUrl = "/images/avatars/avatar-{$randomKey}.svg";

            $this->update([
                'photo_url' => $profilePhotoUrl,
            ]);

            return $profilePhotoUrl;
        }
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function getEnrolledCoursesAttribute()
    {
        $enrolled_courses_uuid = $this->enrolled_courses_uuid ? json_decode($this->enrolled_courses_uuid) : [];
        return Course::whereIn('uuid', $enrolled_courses_uuid)->get();
    }

    public function getEnrolledClassesAttribute()
    {
        $enrolled_classes_uuid = $this->enrolled_classes_uuid ? json_decode($this->enrolled_classes_uuid) : [];
        return BouhawsClass::whereIn('uuid', $enrolled_classes_uuid)->get();
    }
}
