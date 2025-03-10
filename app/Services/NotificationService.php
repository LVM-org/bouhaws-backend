<?php

namespace App\Services;

use App\Mail\ForgotPassword;
use App\Mail\VerifyEmail;
use App\Models\Notification;
use Illuminate\Support\Facades\Mail;

class NotificationService
{

    public function addNotification($request)
    {
        $newNotification = Notification::create([
            "title" => $request->title,
            "body" => $request->body,
            "type" => $request->type,
            "model_type" => $request->model_type,
            "user_id" => $request->user_id,
            "model_type_id" => $request->model_type_id,
            "extra_url" => isset($request->extra_url) ? $request->extra_url : '',
            "read" => false,
        ]);

        $newNotification->save();

        return $newNotification;
    }

    public function markNotificationsAsRead($notificationUuids)
    {

        $allNotifications = Notification::whereIn('uuid', $notificationUuids)->get();

        foreach ($allNotifications as $notification) {
            $notification->update([
                'read' => true,
            ]);
        }

        return true;
    }

    public function sendVerifyEmail($request)
    {
        Mail::to($request->user)->send(new VerifyEmail($request->user));
        return 'sent';
    }

    public function sendForgotPasswordEmail($request)
    {
        Mail::to($request->user)->send(new ForgotPassword($request->user));
        return 'sent';
    }

}
