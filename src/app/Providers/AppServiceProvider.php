<?php

namespace App\Providers;

use App\Interfaces\Messages\SmsInterface;
use App\Services\TwilioSMSService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // $this->app->bind(SmsInterface::class, TwilioSMSService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // CreatorRequest::observe(CreatorRequestObserver::class);
        // Post::observe(PostObserver::class);
        // Comment::observe(CommentObserver::class);
        // Conversation::observe(ConversationObserver::class);
        // Message::observe(MessageObserver::class);
        // User::observe(UserObserver::class);
        // Subscription::observe(SubscriptionObserver::class);
        // Reaction::observe(ReactionObserver::class);
        // Follow::observe(FollowObserver::class);
    }
}
