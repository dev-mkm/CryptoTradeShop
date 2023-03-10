<?php

namespace App\Policies;

use App\Models\Crypto;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CryptoPolicy
{
    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->admin;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Crypto $crypto): bool
    {
        return $user->admin;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Crypto $crypto): bool
    {
        return $user->admin;
    }
}
