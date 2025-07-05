<?php

namespace App\Models;

use App\Traits\Filterable;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use Filterable, HasFactory;

    protected $fillable = [
        'post_id',
        'user_id',
        'type',
        'content',
        'lft',
        'rgt',
        'depth',
    ];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reactions()
    {
        return $this->morphMany(Reaction::class, 'ownerable');
    }

    public function scopeDetail(Builder $query)
    {
        return $query->with([
            'reactions',
            'user',
        ]);
    }

    public static function createComment($postId, $content, $userId, $parentId = null)
    {
        if ($parentId) {
            $parent = self::where('post_id', $postId)->findOrFail($parentId);

            self::where('post_id', $postId)
                ->where('rgt', '>=', $parent->rgt)
                ->increment('rgt', 2);

            self::where('post_id', $postId)
                ->where('lft', '>', $parent->rgt)
                ->increment('lft', 2);

            return self::create([
                'post_id' => $postId,
                'user_id' => $userId,
                'content' => $content,
                'lft' => $parent->rgt,
                'rgt' => $parent->rgt + 1,
                'depth' => $parent->depth + 1,
            ]);
        } else {
            $maxRight = self::where('post_id', $postId)->max('rgt') ?? 0;

            return self::create([
                'post_id' => $postId,
                'user_id' => $userId,
                'content' => $content,
                'lft' => $maxRight + 1,
                'rgt' => $maxRight + 2,
                'depth' => 0,
            ]);
        }
    }
}
