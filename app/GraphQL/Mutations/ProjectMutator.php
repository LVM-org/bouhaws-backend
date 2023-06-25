<?php

namespace App\GraphQL\Mutations;

use App\Services\ProjectService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

final class ProjectMutator
{
    protected $projectService;

    public function __construct()
    {
        $this->projectService = new ProjectService();
    }

    public function uploadImage($_, array $args)
    {
        $mediaUrl = '';

        // upload to cloud
        if (isset($args['image'])) {

        }

        return $mediaUrl;
    }

    public function createProjectCategory($_, array $args)
    {
        return $this->projectService->createOrUpdateProjectCategory(new Request([
            'title' => $args['title'],
        ]));
    }

    public function updateProjectCategory($_, array $args)
    {
        return $this->projectService->createOrUpdateProjectCategory(new Request([
            'project_category_uuid' => $args['project_category_uuid'],
            'title' => $args['title'],
        ]));
    }

    public function createProject($_, array $args)
    {
        $mediaUrl = null;

        if (isset($args['photo_url'])) {
            // save file to cloud
        }

        return $this->projectService->createOrUpdateProject(new Request([
            'title' => $args['title'],
            'user_id' => Auth::user()->id,
            'end_date' => $args['end_date'],
            'prize' => $args['prize'],
            'description' => $args['description'],
            'requirements' => $args['requirements'],
            'photo_url' => $mediaUrl,
            'type' => $args['type'],
            'total_points' => $args['total_points'],
            'project_category_id' => $args['project_category_id'],
        ]));
    }

    public function updateProject($_, array $args)
    {
        $mediaUrl = null;

        if (isset($args['photo_url'])) {
            // save file to cloud
        }

        return $this->projectService->createOrUpdateProject(new Request([
            'project_uuid' => $args['project_uuid'],
            'title' => isset($args['title']) ? $args['title'] : null,
            'end_date' => isset($args['end_date']) ? $args['end_date'] : null,
            'prize' => isset($args['prize']) ? $args['prize'] : null,
            'description' => isset($args['description']) ? $args['description'] : null,
            'requirements' => isset($args['requirements']) ? $args['requirements'] : null,
            'photo_url' => $mediaUrl,
            'type' => isset($args['type']) ? $args['type'] : null,
            'total_points' => isset($args['total_points']) ? $args['total_points'] : null,
            'project_category_id' => isset($args['project_category_id']) ? $args['project_category_id'] : null,
            'status' => isset($args['status']) ? $args['status'] : null,
        ]));
    }

    public function createProjectMilestone($_, array $args)
    {
        return $this->projectService->createOrUpdateProjectMilestone(new Request([
            'title' => $args['title'],
            'points' => $args['points'],
            'project_id' => $args['project_id'],
            'index' => $args['index'],
        ]));
    }

    public function updateProjectMilestone($_, array $args)
    {
        return $this->projectService->createOrUpdateProjectMilestone(new Request([
            'project_milestone_uuid' => $args['project_milestone_uuid'],
            'title' => isset($args['title']) ? $args['title'] : null,
            'points' => isset($args['points']) ? $args['points'] : null,
            'index' => isset($args['index']) ? $args['index'] : null,
        ]));
    }

    public function joinProject($_, array $args)
    {
        return $this->projectService->createOrUpdateProjectEntry(new Request([
            'user_id' => Auth::user()->id,
            'project_id' => $args['project_id'],
            'title' => $args['title'],
            'description' => $args['description'],
            'images' => json_encode([]),
        ]));
    }

    public function updateProjectEntry($_, array $args)
    {
        return $this->projectService->createOrUpdateProjectEntry(new Request([
            'project_entry_uuid' => $args['project_entry_uuid'],
            'title' => isset($args['title']) ? $args['title'] : null,
            'description' => isset($args['description']) ? $args['description'] : null,
            'images' => isset($args['images']) ? json_encode($args['images']) : null,
            'status' => isset($args['status']) ? $args['status'] : null,
        ]));
    }

    public function saveProjectEntryBookmark($_, array $args)
    {
        return $this->projectService->saveProjectEntryBookmark(new Request([
            'user_id' => Auth::user()->id,
            'project_entry_id' => $args['project_entry_id'],
        ]));
    }

    public function saveProjectEntryLike($_, array $args)
    {
        return $this->projectService->saveProjectEntryLike(new Request([
            'user_id' => Auth::user()->id,
            'project_entry_id' => $args['project_entry_id'],
        ]));
    }

    public function saveProjectEntryComment($_, array $args)
    {
        return $this->projectService->saveProjectEntryComment(new Request([
            'user_id' => Auth::user()->id,
            'project_entry_id' => $args['project_entry_id'],
            'content' => $args['content'],
            'is_reply' => $args['is_reply'],
            'replied_comment_id' => isset($args['replied_comment_id']) ? $args['replied_comment_id'] : null,
        ]));
    }

    public function deleteProjectMilestone($_, array $args)
    {
        return $this->projectService->deleteProjectMilestone($args['uuid']);
    }
}
