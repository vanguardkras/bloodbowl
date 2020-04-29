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

    /**
     * Get current instance name
     *
     * @return mixed
     */
    public function name()
    {
        $nameLanguage = 'name_' . app()->getLocale();
        return $this->$nameLanguage;
    }

    /**
     * Get all records related to the selected language.
     *
     * @return Race[]|\Illuminate\Database\Eloquent\Collection
     */
    public static function getAllByLocale()
    {
        $list = self::all([
            'id',
            'name_' . app()->getLocale() . ' as name',
            'is_default',
            ]);
        return $list->sortBy('name');
    }
}
