<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Race
 *
 * @property int $name_en
 * @property int $name_ru
 * @property int $is_default
 * @package App\Models
 */
class Race extends Model
{
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
}
