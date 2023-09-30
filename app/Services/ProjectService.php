<?php

namespace App\Services;

use App\Exceptions\GraphQLException;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\ProjectEntry;
use App\Models\ProjectEntryBookmark;
use App\Models\ProjectEntryComment;
use App\Models\ProjectEntryGrade;
use App\Models\ProjectEntryLike;
use App\Models\ProjectMilestone;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ProjectService
{
    public function createOrUpdateProjectCategory($request)
    {
        if ($request->project_category_uuid) {

            $projectCategory = ProjectCategory::where('uuid', $request->project_category_uuid)->first();

            if ($projectCategory) {
                $projectCategory->update([
                    'title' => $request->title ? $request->title : $projectCategory->title,
                ]);
            } else {
                throw new GraphQLException("Project category not found");
            }

        } else {
            $projectCategory = ProjectCategory::create([
                'title' => $request->title,
            ]);

            $projectCategory->save();

            return $projectCategory;
        }
    }

    public function createOrUpdateProject($request)
    {
        if ($request->project_uuid) {

            $project = Project::where('uuid', $request->project_uuid)->first();

            if ($project) {

                $project->update([
                    'title' => $request->title ? $request->title : $project->title,
                    'end_date' => $request->end_date ? Carbon::parse($request->end_date) : $project->end_date,
                    'prize' => $request->prize ? $request->prize : $project->prize,
                    'description' => $request->description ? $request->description : $project->description,
                    'requirements' => $request->requirements ? $request->requirements : $project->requirements,
                    'photo_url' => $request->photo_url ? $request->photo_url : $project->photo_url,
                    'type' => $request->type ? $request->type : $project->type,
                    'total_points' => $request->total_points ? $request->total_points : $project->total_points,
                    'project_category_id' => $request->project_category_id ? $request->project_category_id : $project->project_category_id,
                    'status' => $request->status ? $request->status : $project->status,
                    'bouhaws_class_id' => $request->bouhaws_class_id ? $request->bouhaws_class_id : $project->bouhaws_class_id,
                ]);

            } else {
                throw new GraphQLException("Project not found");
            }

            return $project;

        } else {
            $project = Project::create([
                'title' => $request->title,
                'user_id' => $request->user_id,
                'end_date' => Carbon::parse($request->end_date),
                'prize' => $request->prize,
                'currency' => 'usd',
                'description' => $request->description,
                'requirements' => $request->requirements,
                'photo_url' => $request->photo_url,
                'type' => $request->type,
                'total_points' => $request->total_points,
                'project_category_id' => $request->project_category_id,
                'bouhaws_class_id' => $request->bouhaws_class_id,
            ]);

            $project->save();

            return $project;
        }
    }

    public function createOrUpdateProjectMilestone($request)
    {
        if ($request->project_milestone_uuid) {

            $projectMilestone = ProjectMilestone::where('uuid', $request->project_milestone_uuid)->first();

            if ($projectMilestone) {
                $projectMilestone->update([
                    'title' => $request->title,
                    'points' => $request->points,
                    'index' => $request->index,
                ]);

            } else {
                throw new GraphQLException("Project milestone not found");
            }

            return $projectMilestone;

        } else {
            $projectMilestone = ProjectMilestone::create([
                'title' => $request->title,
                'points' => $request->points,
                'project_id' => $request->project_id,
                'index' => $request->index,
            ]);

            $projectMilestone->save();

            return $projectMilestone;
        }
    }

    public function createOrUpdateProjectEntry($request)
    {
        if ($request->project_entry_uuid) {

            $projectEntry = ProjectEntry::where('uuid', $request->project_entry_uuid)->first();

            if ($projectEntry) {
                $projectEntry->update([
                    'current_milestone_index' => $request->current_milestone_index ? $request->current_milestone_index : $projectEntry->current_milestone_index,
                    'title' => $request->title ? $request->title : $projectEntry->title,
                    'description' => $request->description ? $request->description : $projectEntry->description,
                    'images' => $request->images ? $request->images : $projectEntry->images,
                    'status' => $request->status ? $request->status : $projectEntry->status,
                ]);
            } else {
                throw new GraphQLException("Project entry not found");
            }

            return $projectEntry;

        } else {
            $projectEntry = ProjectEntry::create([
                'user_id' => $request->user_id,
                'project_id' => $request->project_id,
                'current_milestone_index' => 0,
                'title' => $request->title,
                'description' => $request->description,
                'images' => $request->images,
                'status' => 'draft',
                'project_category_id' => $request->project_category_id,
            ]);

            $projectEntry->save();

            if (Auth::user()->id != $request->user_id) {
                // send notification
                $notificationService = new NotificationService();

                $notificationService->addNotification((object) [
                    "title" => Auth::user()->username . " submitted an entry to " . $projectEntry->project->title,
                    "body" => '',
                    "type" => 'activity',
                    "model_type" => 'project_entry',
                    'user_id' => $projectEntry->project->user_id,
                    "model_type_id" => $projectEntry->uuid,
                ]);
            }

            return $projectEntry;
        }
    }

    public function saveProjectEntryBookmark($request)
    {
        $projectBookmark = ProjectEntryBookmark::where('user_id', $request->user_id)->where('project_entry_id', $request->project_entry_id)->first();

        if ($projectBookmark) {
            $projectBookmarkToDelete = ProjectEntryBookmark::where('user_id', $request->user_id)->where('project_entry_id', $request->project_entry_id);

            $projectBookmarkToDelete->delete();
        } else {

            $projectBookmark = ProjectEntryBookmark::create([
                'user_id' => $request->user_id,
                'project_entry_id' => $request->project_entry_id,
            ]);

            $projectBookmark->save();

        }

        return $projectBookmark;
    }

    public function saveProjectEntryLike($request)
    {
        $projectLike = ProjectEntryLike::where('user_id', $request->user_id)->where('project_entry_id', $request->project_entry_id)->first();

        if ($projectLike) {
            $projectLikeToDelete = ProjectEntryLike::where('user_id', $request->user_id)->where('project_entry_id', $request->project_entry_id);

            $projectLikeToDelete->delete();
        } else {
            $projectLike = ProjectEntryLike::create([
                'user_id' => $request->user_id,
                'project_entry_id' => $request->project_entry_id,
            ]);

            $projectLike->save();

            if (Auth::user()->id != $request->user_id) {
                // send notification
                $notificationService = new NotificationService();

                $notificationService->addNotification((object) [
                    "title" => Auth::user()->username . " liked on your project entry",
                    "body" => '',
                    "type" => 'activity',
                    "model_type" => 'project_entry_like',
                    'user_id' => $projectLike->project_entry->user_id,
                    "model_type_id" => $projectLike->uuid,
                ]);
            }

        }

        return $projectLike;
    }

    public function saveProjectEntryComment($request)
    {
        $projectEntrycomment = ProjectEntryComment::create([
            'user_id' => $request->user_id,
            'project_entry_id' => $request->project_entry_id,
            'content' => $request->content,
            'is_reply' => $request->is_reply,
            'replied_comment_id' => $request->replied_comment_id,
        ]);

        $projectEntrycomment->save();

        if (Auth::user()->id != $request->user_id) {
            // send notification
            $notificationService = new NotificationService();

            $notificationService->addNotification((object) [
                "title" => Auth::user()->username . " commented on your project entry",
                "body" => $projectEntrycomment->content,
                "type" => 'activity',
                "model_type" => 'project_entry_comment',
                'user_id' => $projectEntrycomment->project_entry->user_id,
                "model_type_id" => $projectEntrycomment->uuid,
            ]);
        }

        return $projectEntrycomment;
    }

    public function gradeProjectEntry($request)
    {

        $projectEntry = ProjectEntry::where('uuid', $request->project_entry_uuid)->first();

        if ($projectEntry == null) {
            throw new GraphQLException("Project entry not found");
        }

        $entryGrade = ProjectEntryGrade::where('project_entry_id', $projectEntry->id)->first();

        $projectMilestones = json_decode($request->milestones, true);

        $totalPoints = 0;

        foreach ($projectMilestones as $milestone) {
            $totalPoints += $milestone['points'];
        }

        if ($entryGrade) {

            $entryGrade->update([
                'total_points' => $totalPoints,
                'milestones' => $request->milestones,
            ]);

        } else {
            $entryGrade = ProjectEntryGrade::create([
                "user_id" => $projectEntry->user_id,
                "project_entry_id" => $projectEntry->id,
                'total_points' => $totalPoints,
                'milestones' => $request->milestones,
            ]);

            $entryGrade->save();
        }

        return $entryGrade;
    }

    public function deleteProjectMilestone($uuid)
    {
        $projectMilestone = ProjectMilestone::where('uuid', $uuid)->first();

        if ($projectMilestone) {

            $projectMilestone->delete();

        } else {
            throw new GraphQLException("Project milestone not found");
        }

        return true;
    }

}
