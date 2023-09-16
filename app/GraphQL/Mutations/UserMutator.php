<?php

namespace App\GraphQL\Mutations;

use App\Exceptions\GraphQLException;
use App\Models\Profile;
use App\Models\User;
use App\Services\NotificationService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

final class UserMutator
{
    protected $userService;
    protected $notificationService;

    public function __construct()
    {
        $this->userService = new UserService();
        $this->notificationService = new NotificationService();
    }

    public function updateProfile($_, array $args)
    {
        if (isset($args['username'])) {
            $user = User::where('username', $args['username'])->first();

            if ($user) {
                throw new GraphQLException('Username is not available');
            } else {
                $user = Auth::user();

                $user->update([
                    'username' => $args['username'],
                ]);
            }
        }

        if (isset($args['name'])) {
            $user = Auth::user();
            $user->update([
                'name' => $args['name'],
            ]);
        }

        $mediaUrl = null;

        if (isset($args['photo_url'])) {
            $userService = new UserService();
            $request = new Request();
            $request->files->set('attachment', $args['photo_url']);
            $mediaUrl = $userService->uploadFile($request, false);
        }

        $this->userService->createOrUpdateProfile(new Request([
            'photo_url' => $mediaUrl,
            'bio' => isset($args['bio']) ? $args['bio'] : null,
            'school' => isset($args['school']) ? $args['school'] : null,
            'student_number' => isset($args['student_number']) ? $args['student_number'] : null,
            'year_of_enrollment' => isset($args['year_of_enrollment']) ? $args['year_of_enrollment'] : null,
            'type' => isset($args['type']) ? $args['type'] : null,
            'push_notification_enabled' => isset($args['push_notification_enabled']) ? $args['push_notification_enabled'] : null,
            'user_id' => Auth::user()->id,
        ]));

        return Profile::where('user_id', Auth::user()->id)->first();
    }

    public function startConversation($_, array $args)
    {
        return $this->userService->createOrUpdateConversation(new Request([
            'user_uuid' => Auth::user()->uuid,
            'associated_users_uuid' => $args['associated_users_uuid'],
            'user_id' => Auth::user()->id,
        ]));
    }

    public function joinConversation($_, array $args)
    {
        return $this->userService->createOrUpdateConversation(new Request([
            'conversation_uuid' => $args['conversation_uuid'],
            'associated_users_uuid' => $args['associated_users_uuid'],
        ]));
    }

    public function saveConversationMessage($_, array $args)
    {
        $conversationMessage = $this->userService->saveConversationMessage(new Request([
            'type' => $args['type'],
            'user_id' => Auth::user()->id,
            'conversation_id' => $args['conversation_id'],
            'content' => $args['content'],
            'media' => $args['media'],
        ]));

        // broadcast message to other users

        return $conversationMessage;
    }

    public function markNotificationsAsRead($_, array $args)
    {
        $this->notificationService->markNotificationsAsRead($args['notification_uuids']);

        return true;
    }
}
