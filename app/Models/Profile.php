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

    protected $appends = ['enrolled_courses', 'enrolled_classes'];

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
        return $this->belongsTo(Profile::class);
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
