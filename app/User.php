<?php

namespace App;

use App\Models\Competition;
use App\Models\Team;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Class User
 *
 * @property string $username
 * @property string $email
 * @property string $email_verified_at
 * @property string $password
 * @property HasMany $teams
 * @property string $is_super
 * @property string $vk_id
 *
 * @package App
 */
class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username', 'email', 'password', 'vk_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * User competitions list.
     *
     * @return HasMany
     */
    public function competitions()
    {
        return $this->hasMany(Competition::class)
            ->select('id', 'name', 'logo', 'type', 'finished', 'round')
            ->withCount('teams');
    }

    /**
     * Checks if current user has super admin rights
     *
     * @return string
     */
    public function isSuper()
    {
        return $this->is_super;
    }

    /**
     * Get current user teams.
     *
     * @return HasMany
     */
    public function teams()
    {
        return $this->hasMany(Team::class);
    }
}
