<?php

namespace Pkt\StarterKit\Helpers;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class LeaderApi
{
    public static $responseData;
    /**
     * Get all karyawan from Leader API
     *
     * @return Collection<
     *  object {
     *     "USERS_NPK": string,
     *     "USERS_EMAIL": string,
     *     "USERS_NAME": string,
     *     "USERS_USERNAME": string,
     *     "USERS_HIERARCHY_CODE": string,
     *     "USERS_ID_POSISI": string,
     *     "USERS_POSISI": string,
     *     "USERS_ID_UNIT_KERJA": string,
     *     "USERS_UNIT_KERJA": string,
     *     "USERS_FLAG": string
     * >
     */
    public static function getAllEmployee(): Collection
    {
        $response = Http::withHeaders([
            'Api-Key' => env('LEADER_API_KEY')
        ])->get('https://leader.pupukkaltim.com/api/Api_leader/get_all_karyawan');

        return collect($response->object()->data->USERS_LIST);
    }

    /**
     * Get all unit kerja from Leader API
     *
     * @return Collection<
     *  object {
     *    "KodeUnitKerja": string,
     *    "UnitKerja": string,
     *    "Keterangan": string,
     *    "IsAktif": int
     * >
     */
    public static function getAllWorkUnit(): Collection
    {
        $response = Http::withHeaders([
            'Api-Key' => env('LEADER_API_KEY')
        ])->get('https://leader.pupukkaltim.com/api/Api_leader/get_all_unit_kerja');
        
        return collect($response->object()->data->UK_LIST);
    }

    /**
     * Get all plt from Leader API
     *
     * @return Collection<
     *  object {
     *   "PERSONNEL_NUMBER": string,
     *   "VALID_FROM": string,
     *   "VALID_TO": string,
     *   "NAMA_POSISI": string
     * >
     */
    public static function getAllPlt(): Collection
    {
        $response = Http::withHeaders([
            'Api-Key' => env('LEADER_API_KEY')
        ])->get('https://leader.pupukkaltim.com/api/Api_leader/get_all_plt');

        return collect($response->object()->data->PLT_LIST);
    }
}