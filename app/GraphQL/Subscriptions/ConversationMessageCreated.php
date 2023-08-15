<?php

namespace App\GraphQL\Subscriptions;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Nuwave\Lighthouse\Schema\Types\GraphQLSubscription;
use Nuwave\Lighthouse\Subscriptions\Subscriber;

final class ConversationMessageCreated extends GraphQLSubscription
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

        Log::debug($root);

        return in_array($root->conversation_uuid, $args['conversationList']);
    }
}
