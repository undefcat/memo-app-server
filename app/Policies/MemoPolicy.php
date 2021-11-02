<?php

namespace App\Policies;

use App\Models\Memo;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MemoPolicy
{
    use HandlesAuthorization;

    public function destroy(User $user, Memo $memo): ?bool
    {
        if ((string)$memo->user_id === (string)$user->id) {
            return true;
        }

        return null;
    }
}
