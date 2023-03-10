<?php

namespace App\Policies;

use App\Models\Offer;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class OfferPolicy
{
    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Offer $offer): Response
    {
        return $user->id == $offer->user_id ? Response::allow() : Response::deny("You don't own this offer");
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Offer $offer): Response
    {
        return $user->id == $offer->user_id ? Response::allow() : Response::deny("You don't own this offer");
    }
}
