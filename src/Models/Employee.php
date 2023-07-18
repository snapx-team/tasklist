<?php

namespace Xguard\Tasklist\Models;

use App\Models\User;
use Dyrynda\Database\Support\CascadeSoftDeletes;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * Employee Model
 *
 * @package Xguard\Tasklist\Models
 * @property int $id
 * @property string $role
 * @property int $user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read User $user
 * @method static Builder|Employee newModelQuery()
 * @method static Builder|Employee newQuery()
 * @method static \Illuminate\Database\Query\Builder|Employee onlyTrashed()
 * @method static Builder|Employee query()
 * @method static Builder|Employee whereCreatedAt($value)
 * @method static Builder|Employee whereDeletedAt($value)
 * @method static Builder|Employee whereId($value)
 * @method static Builder|Employee whereRole($value)
 * @method static Builder|Employee whereUpdatedAt($value)
 * @method static Builder|Employee whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|Employee withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Employee withoutTrashed()
 * @mixin Eloquent
 */
class Employee extends Model
{
    use SoftDeletes, CascadeSoftDeletes;

    protected $dates = ['deleted_at'];
    protected $table = 'tl_employees';
    protected $guarded = [];

    const USER_ID = 'user_id';
    const ROLE = 'role';
    const USER_RELATION_NAME = 'user';
    const DELETED = 'DELETED';
    const USER = 'USER';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withDefault(function ($user) {
            $user->first_name = self::DELETED;
            $user->last_name = self::USER;
        });
    }
}
