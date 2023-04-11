<?php

namespace App\Repositories;

use App\Prices;
use App\Seasons;
use Illuminate\Support\Facades\Cache;

class CachedRepository
{
//    const TTL_IN_MINUTES = 5 * 24 * 60;
    const TTL_IN_MINUTES = 1;

    /**
     * @param string $date
     * @return mixed
     */
    public function getSeasonType($date)
    {
        return Cache::remember('season_for_' . str_slug($date, '_'), self::TTL_IN_MINUTES, function () use ($date) {
            return Seasons::getSeasonType($date);
        });
    }

    /**
     * @param string $season
     * @param int $pax
     */
    public function getCostsFromSeason($season, $pax)
    {
        return Cache::remember("costs_from_season_{$season}_with_pax_{$pax}", self::TTL_IN_MINUTES, function () use ($season, $pax) {
            return Prices::select(['cost', 'price'])->where('season', $season)
                ->where('occupation', $pax)->get()->toArray();
        });
    }
}