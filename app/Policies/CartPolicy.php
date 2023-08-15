<?php

namespace App\Policies;

use App\Models\Cart;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CartPolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Cart $cart): Response
    {
        return $user->id === $cart->user_id                 
                    ? Response::allow()
                    : Response::deny('You do not own this cart.');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function create(User $user): Response
    {
        return $user->id === 'admin'                 
                    ? Response::allow()
                    : Response::deny('You do not own this cart.');
    }
}
