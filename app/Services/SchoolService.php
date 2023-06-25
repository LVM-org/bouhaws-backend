<?php

namespace App\Services;

use App\Exceptions\GraphQLException;
use App\Models\BouhawsClass;
use App\Models\Course;

class SchoolService
{
    public function createOrUpdateClass($request)
    {
        if ($request->bouhaws_class_uuid) {

            $bouhawsClass = BouhawsClass::where('uuid', $request->bouhaws_class_uuid)->first();

            if ($bouhawsClass) {
                $bouhawsClass->update([
                    'title' => $request->title ? $request->title : $bouhawsClass->title,
                    'projects_id' => $request->projects_id ? $request->projects_id : $bouhawsClass->projects_id,
                    'description' => $request->description ? $request->description : $bouhawsClass->description,
                ]);

            } else {
                throw new GraphQLException("Class not found");
            }

            return $bouhawsClass;

        } else {
            $bouhawsClass = BouhawsClass::create([
                'title' => $request->title,
                'user_id' => $request->user_id,
                'description' => $request->description,
            ]);

            $bouhawsClass->save();

            return $bouhawsClass;
        }
    }

    public function createOrUpdateCourse($request)
    {
        if ($request->course_uuid) {

            $course = Course::where('uuid', $request->course_uuid)->first();

            if ($course) {
                $course->update([
                    'code' => $request->code ? $request->code : $course->code,
                    'title' => $request->title ? $request->title : $course->title,
                    'photo_url' => $request->photo_url ? $request->photo_url : $course->photo_url,
                    'status' => $request->status ? $request->status : $course->status,
                ]);

            } else {
                throw new GraphQLException("Course not found");
            }

            return $course;

        } else {
            $course = Course::create([
                'code' => $request->code,
                'title' => $request->title,
                'photo_url' => $request->photo_url,
                'status' => 'inactive',
                'bouhaws_class_id' => $request->bouhaws_class_id,
            ]);

            $course->save();

            return $course;
        }
    }

}
