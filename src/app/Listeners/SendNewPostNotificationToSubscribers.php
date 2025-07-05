<?php

namespace App\Listeners;

use App\Events\CreatorPostedInPlan;
use App\Mail\CreatePostMail;
use App\Models\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Mail;

class SendNewPostNotificationToSubscribers implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(CreatorPostedInPlan $event): void
    {
        $post = $event->post;
        $plan = $event->plan;

        if (!$plan) {
            return;
        }

        $subscriptions = $plan->subscriptions()
            ->where('status', 'completed')
            ->with('user')
            ->get();

        foreach ($subscriptions as $subscription) {
            $user = $subscription->user;
            if (!$user) {
                continue;
            }

            if ($user->canReceiveNotification('is_post_published')) {
                Mail::to($user->email)->queue(new CreatePostMail(
                    $post,
                    $plan,
                    $user,
                ));
            }

            $notification = Notification::create([
                'user_id' => $user->id,
                'actor_id' => $subscription->creator_id,
                'data' => [
                    'actor_id' => $subscription->creator_id,
                    'actor_user_identify' => $subscription->creator->user_identify,
                    'actor_name' => $subscription->creator->name,
                    'actor_avatar' => $subscription->creator->avatar_img,
                    'subject_id' => $subscription->ownerable->id,
                ],
                'type' => 'post_published',
            ]);
            event(new \App\Events\NotificationCreated($subscription->creator, $user->id, 'post_published', $subscription->ownerable->id, $notification));
        }
    }
}
