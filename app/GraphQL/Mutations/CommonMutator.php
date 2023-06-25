<?php

namespace App\GraphQL\Mutations;

use App\Services\SchoolService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

final class CommonMutator
{
    protected $schoolService;

    public function __construct()
    {
        $this->schoolService = new SchoolService();
    }

    public function createBouhawsClass($_, array $args)
    {
        return $this->schoolService->createOrUpdateClass(new Request([
            'title' => $args['title'],
            'user_id' => Auth::user()->id,
            'description' => $args['description'],
        ]));
    }

    public function updateBouhawsClass($_, array $args)
    {
        return $this->schoolService->createOrUpdateClass(new Request([
            'bouhaws_class_uuid' => $args['bouhaws_class_uuid'],
            'title' => isset($args['title']) ? $args['title'] : null,
            'projects_id' => isset($args['projects_id']) ? json_encode($args['projects_id']) : null,
            'description' => isset($args['description']) ? $args['description'] : null,
        ]));
    }

    public function createCourse($_, array $args)
    {
        $mediaUrl = null;

        if (isset($args['photo_url'])) {
            // save file to cloud
        }

        return $this->schoolService->createOrUpdateCourse(new Request([
            'code' => $args['code'],
            'photo_url' => $mediaUrl,
            'title' => $args['title'],
            'bouhaws_class_id' => $args['bouhaws_class_id'],
        ]));
    }

    public function updateCourse($_, array $args)
    {
        $mediaUrl = null;

        if (isset($args['photo_url'])) {
            // save file to cloud
        }

        return $this->schoolService->createOrUpdateCourse(new Request([
            'course_uuid' => $args['course_uuid'],
            'code' => isset($args['code']) ? $args['code'] : null,
            'photo_url' => $mediaUrl,
            'title' => isset($args['title']) ? $args['title'] : null,
            'status' => isset($args['status']) ? $args['status'] : null,
        ]));
    }
}
