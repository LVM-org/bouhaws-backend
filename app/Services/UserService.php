<?php

namespace App\Services;

use App\Exceptions\GraphQLException;
use App\Models\Conversation;
use App\Models\ConversationMember;
use App\Models\ConversationMessage;
use App\Models\Profile;
use App\Models\User;

class UserService
{
    public function createOrUpdateProfile($request)
    {
        $userProfile = Profile::where('user_id', $request->user_id)->first();

        if ($userProfile == null) {
            $userProfile = Profile::create([
                'type' => 'student',
                'user_id' => $request->user_id,
            ]);

            $userProfile->save();
        } else {
            $userProfile->update([
                'photo_url' => $request->photo_url ? $request->photo_url : $userProfile->photo_url,
                'bio' => $request->bio ? $request->bio : $userProfile->bio,
                'school' => $request->school ? $request->school : $userProfile->school,
                'student_number' => $request->student_number ? $request->student_number : $userProfile->student_number,
                'year_of_enrollment' => $request->year_of_enrollment ? $request->year_of_enrollment : $userProfile->year_of_enrollment,
                'type' => $request->type ? $request->type : $userProfile->type,
                'enrolled_courses_uuid' => $request->enrolled_courses_uuid ? $request->enrolled_courses_uuid : $userProfile->enrolled_courses_uuid,
                'enrolled_classes_uuid' => $request->enrolled_classes_uuid ? $request->enrolled_classes_uuid : $userProfile->enrolled_classes_uuid,
                'push_notification_enabled' => $request->push_notification_enabled != null ? $request->push_notification_enabled : $userProfile->push_notification_enabled,
            ]);

        }

        return $userProfile;
    }

    public function addToUserPoint($request)
    {
        $profile = Profile::where('user_id', $request->user_id)->first();

        if ($profile) {
            $currentPoint = (float) $profile->points;

            $newCurrentPoint = (float) $request->point + $currentPoint;

            $profile->update([
                'points' => $newCurrentPoint,
            ]);
        }

        return true;
    }

    public function createOrUpdateConversation($request)
    {
        if ($request->conversation_uuid) {

            $conversation = Conversation::where('uuid', $request->conversation_uuid)->first();

            if ($conversation) {

                $conversation->update([
                    'associated_users_uuid' => $request->associated_users_uuid ? json_encode($request->associated_users_uuid) : $conversation->associated_users_uuid,
                ]);

                foreach ($request->associated_users_uuid as $user_uuid) {
                    $currentMember = ConversationMember::where('user_uuid', $user_uuid)->where('conversation_uuid', $conversation->uuid)->first();

                    if ($currentMember == null) {
                        $conversationMember = ConversationMember::create([
                            'user_uuid' => $user_uuid,
                            'conversation_uuid' => $conversation->uuid,
                        ]);

                        $conversationMember->save();
                    }

                }

            } else {
                throw new GraphQLException("Conversion not found");
            }

            return $conversation;

        } else {

            $conversation = Conversation::create([
                'user_id' => $request->user_id,
                'associated_users_uuid' => json_encode($request->associated_users_uuid),
            ]);

            $user = User::where('id', $request->user_id)->first();

            if ($user) {
                $conversationOwner = ConversationMember::create([
                    'user_uuid' => $user->uuid,
                    'conversation_uuid' => $conversation->uuid,
                ]);

                $conversationOwner->save();

            }

            foreach ($request->associated_users_uuid as $user_uuid) {
                $conversationMember = ConversationMember::create([
                    'user_uuid' => $user_uuid,
                    'conversation_uuid' => $conversation->uuid,
                ]);

                $conversationMember->save();
            }

            $conversation->save();

            return $conversation;

        }
    }

    public function saveConversationMessage($request)
    {
        $message = ConversationMessage::create([
            'type' => $request->type,
            'user_id' => $request->user_id,
            'conversation_id' => $request->conversation_id,
            'content' => $request->content,
            'media' => $request->media,
        ]);

        $message->save();

        return $message;
    }

    public function uploadFile($request, $resizeImg = true)
    {

        $fileName = time() . '_' . $request->file('attachment')->getClientOriginalName();
        $filePath = $request->file('file')->storeAs('uploads', $fileName, 'public');
        $filePath = '/storage/' . $filePath;

        return asset($filePath);

        // $allowedMimeTypes = ['image/jpeg', 'image/gif', 'image/png'];
        // $contentType = $request->file('attachment')->getClientMimeType();

        // $photo = null;
        // if (!in_array($contentType, $allowedMimeTypes)) {
        //     $photo = Storage::disk('do_spaces')->putFile('main', $request->file('attachment'), 'public');
        // } else {
        //     $data = getimagesize($request->file('attachment'));
        //     $width = $data[0];
        //     $height = $data[1];

        //     $image = Image::make($request->file('attachment'))->resize(640, 400);

        //     if ($resizeImg == false) {
        //         $image = Image::make($request->file('attachment'))->resize($width, $height);
        //     }

        //     $extension = $request->file('attachment')->getClientOriginalExtension();

        //     $extension = $extension ? $extension : 'jpg';

        //     $fileName = Str::random(30) . '.' . $extension;

        //     $image = $image->stream();

        //     $photo = Storage::disk('do_spaces')->put('main/' . $fileName, $image->__toString(), 'public');

        //     return env('DO_SPACE_URL', "https://roof.nyc3.digitaloceanspaces.com") . '/' . "main/" . $fileName;
        // }

        // return Storage::disk('do_spaces')->url($photo);
    }
}
