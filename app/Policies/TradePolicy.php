<?php

namespace App\Policies;

use App\Models\Trade;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TradePolicy
{

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Trade $trade): Response
    {
        $find = $trade->users()->find($user->id);
        return $find !== null ? Response::allow() : Response::deny("You don't own this offer");
    }
}
