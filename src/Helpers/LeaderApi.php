<?php

namespace Pkt\StarterKit\Helpers;

use Illuminate\Support\Facades\Http;

class LeaderApi
{
    /**
     * Get all karyawan from Leader API
     *
     * @return object
     */
    public static function getAllEmployee()
    {
        $response = Http::withHeaders([
            'Api-Key' => env('LEADER_API_KEY')
        ])->get('https://leader.pupukkaltim.com/api/Api_leader/get_all_karyawan');

        // {
        //   "code": 1,
        //   "message": "Success",
        //   "data": {
        //       "API_REQUEST": "GET ALL KARYAWAN",
        //       "USERS_COUNT": 3968,
        //       "USERS_LIST": [
        //           {
        //               "USERS_NPK": "4882980",
        //               "USERS_EMAIL": "4882980@pupukkaltim.com",
        //               "USERS_NAME": "Hadi Kasiono",
        //               "USERS_USERNAME": "4882980",
        //               "USERS_HIERARCHY_CODE": "12100000LCALFN",
        //               "USERS_ID_POSISI": "50048724",
        //               "USERS_POSISI": "Staf PBP",
        //               "USERS_ID_UNIT_KERJA": "D002100000",
        //               "USERS_UNIT_KERJA": "Kompartemen Operasi I",
        //               "USERS_FLAG": "TKO"
        //           },
        //           ...
        //       ]
        //   }
        // }

        return $response->object();
    }

    /**
     * Get all unit kerja from Leader API
     *
     * @return object
     */
    public static function getAllWorkUnit()
    {
        $response = Http::withHeaders([
            'Api-Key' => env('LEADER_API_KEY')
        ])->get('https://leader.pupukkaltim.com/api/Api_leader/get_all_unit_kerja');
        
        // {
        //   "code": 1,
        //   "message": "Success",
        //   "data": {
        //       "API_REQUEST": "GET ALL UNIT KERJA",
        //       "UK_COUNT": 176,
        //       "UK_LIST": [
        //           {
        //               "KodeUnitKerja": "D001000000",
        //               "UnitKerja": "Direktorat Utama",
        //               "Keterangan": "1",
        //               "IsAktif": 1
        //           },
        //           ...
        //       ]
        //   }
        // }

        return $response->object();
    }

    /**
     * Get all plt from Leader API
     *
     * @return object
     */
    public static function getAllPlt()
    {
        $response = Http::withHeaders([
            'Api-Key' => env('LEADER_API_KEY')
        ])->get('https://leader.pupukkaltim.com/api/Api_leader/get_all_plt');

        // {
        //   "code": 1,
        //   "message": "Success",
        //   "data": {
        //       "API_REQUEST": "GET ALL PLT",
        //       "PLT_COUNT": 31,
        //       "PLT_LIST": [
        //           {
        //               "PERSONNEL_NUMBER": "4063592",
        //               "VALID_FROM": "2024-03-04",
        //               "VALID_TO": "2024-04-30",
        //               "NAMA_POSISI": "VP Hukum"
        //           },
        //           ...
        //       ]
        //   }
        // }

        return $response->object();
    }
}