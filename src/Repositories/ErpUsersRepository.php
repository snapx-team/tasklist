<?php

namespace Xguard\Tasklist\Repositories;

use Xguard\Tasklist\Entities\ErpUser;
use App\Models\User;
use Illuminate\Support\Collection;

/**
 * Class ErpUsersRepository
 * @package Xguard\Tasklist\Repositories
 */
class ErpUsersRepository
{

    /**
     * Retrieve an ERP user with a userId
     *
     * @param int $erpUserId
     * @return ErpUser|null
     */
    public static function retrieve(int $erpUserId): ?ErpUser
    {
        $erpUser = User::find($erpUserId);
        return $erpUser ? new ErpUser($erpUser->id, $erpUser->first_name, $erpUser->last_name) : null;
    }

    /**
     * Get up to 10 users that match a search term
     *
     * @param $search
     * @return Collection
     */
    public static function getSomeUsers($search): Collection
    {
        $erpUsers = User::where(function ($q) use ($search) {
            $q->where('first_name', 'like', "%{$search}%")
            ->orWhere('last_name', 'like', "%{$search}%")
            ->orWhere(User::raw('CONCAT(first_name, " ", last_name)'), 'like', "%{$search}%");
        })->orderBy('first_name')->take(10)->get();

        return $erpUsers->map(function ($erpUser) {
            return new ErpUser($erpUser->id, $erpUser->first_name, $erpUser->last_name);
        });
    }

    /**
     * Get 10 initial users
     *
     * @return Collection
     */
    public static function getAllUsers(): Collection
    {
        $erpUsers = User::orderBy('first_name')->take(10)->get();

        return $erpUsers->map(function ($erpUser) {
            return new ErpUser($erpUser->id, $erpUser->first_name, $erpUser->last_name);
        });
    }
}
