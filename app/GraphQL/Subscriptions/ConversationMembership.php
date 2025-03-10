<?php

namespace App\GraphQL\Subscriptions;

use Illuminate\Http\Request;
use Nuwave\Lighthouse\Schema\Types\GraphQLSubscription;
use Nuwave\Lighthouse\Subscriptions\Subscriber;

final class ConversationMembership extends GraphQLSubscription
{
    /**
     * Check if subscriber is allowed to listen to the subscription.
     */
    public function authorize(Subscriber $subscriber, Request $request): bool
    {
        return true;
    }

    /**
     * Filter which subscribers should receive the subscription.
     */
    public function filter(Subscriber $subscriber, mixed $root): bool
    {
        $args = $subscriber->args;

        return $root->user_uuid == $args['userUuid'];
    }
}
