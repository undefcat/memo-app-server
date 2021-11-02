<?php

namespace App\Policies;

use App\Models\Memo;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MemoPolicy
{
    use HandlesAuthorization;

    public function update(User $user, Memo $memo): bool
    {
        return $this->isOwner($user, $memo);
    }

    public function destroy(User $user, Memo $memo): bool
    {
        return $this->isOwner($user, $memo);
    }

    private function isOwner(User $user, Memo $memo): bool
    {
        return (string)$user->id === (string)$memo->user_id;
    }
}
