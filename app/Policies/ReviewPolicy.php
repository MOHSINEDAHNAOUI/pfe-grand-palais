<?php
namespace App\Policies;

use App\Models\User;
use App\Models\Review;

class ReviewPolicy
{
    public function delete(User $user, Review $review): bool
    {
        return $user->id === $review->user_id
            || $user->hasRole('admin');
    }
}