<?php

namespace App\Policies;

use App\Models\Memo;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MemoPolicy
{
    use HandlesAuthorization;

    public function update(User $user, Memo $memo): ?bool
    {
        return (string)$user->id === (string)$memo->user_id;
    }

    public function destroy(User $user, Memo $memo): ?bool
    {
        if ((string)$memo->user_id === (string)$user->id) {
            return true;
        }

        return null;
    }
}
