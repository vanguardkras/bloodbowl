<?php

use App\Models\Race;

if (! function_exists('races')) {

    /**
     * Get all races list.
     *
     * @return Race[]|\Illuminate\Database\Eloquent\Collection
     */
    function races()
    {
        return Race::getAllByLocale();
    }
}

if (! function_exists('competitionTypes')) {

    /**
     * Auto discovery available competition types.
     *
     * @return array
     */
    function competitionTypes()
    {
        $files = scandir(app_path() . '/Services/CompetitionStrategy');
        $files = array_slice($files, 2);
        $results = [];
        foreach ($files as $file) {
            if ($file === 'Type.php' || $file === 'CompetitionStrategyException.php') {
                continue;
            }

            $file = substr($file, 0, -4);
            $results[] = Str::snake($file);
        }

        return $results;
    }
}
