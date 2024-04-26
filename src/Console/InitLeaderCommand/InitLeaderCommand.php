<?php

namespace Pkt\StarterKit\Console\InitLeaderCommand;

use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Pkt\StarterKit\Helpers\LeaderApi;

class InitLeaderCommand extends Command implements PromptsForMissingInput
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pkt:init-leader';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initialize PKT Leader with sync users data from PKT Leader API';

    /**
     * Execute the console command.
     *
     * @return int|null
     */
    public function handle()
    {
        // ask to confirm before proceed
        if (!$this->confirm('This command will sync users data from PKT Leader API. Do you want to proceed?')) {
            return 0;
        }

        // make sure the user already run pkt:install
        if (!file_exists(resource_path('js/Core/Config/SidemenuItem.js')) && !file_exists(config_path('permissions.php'))) {
            $this->error('Please run php artisan pkt:install first');
            return 0;
        }

        // make sure the user already set the LEADER_API_KEY
        if (!env('LEADER_API_KEY')) {
            $this->error('Please set the LEADER_API_KEY in .env file');
            return 0;
        }

        // check if leader already installed
        if (file_exists(database_path('migrations/2024_04_04_000000_add_leader_to_users_table.php'))) {
            $this->error('You already install the leader.');
            return 0;
        }

        // copy migration and run migrate
        copy(__DIR__.'/../../../leader-stubs/database/migrations/2024_04_04_000000_add_leader_to_users_table.php', database_path('migrations/2024_04_04_000000_add_leader_to_users_table.php'));
        $this->call('migrate');

        // get all employee from Leader API
        $employees = LeaderApi::getAllEmployee();
        $employees->each(function ($employee) {
            $this->info('Syncing '. $employee->USERS_NPK . ' - ' . $employee->USERS_NAME);

            $user = app('App\\Models\\User')::updateOrCreate([
                'npk' => $employee->USERS_NPK
            ], [
                'name' => $employee->USERS_NAME,
                'email' => $employee->USERS_EMAIL,
                'username' => $employee->USERS_USERNAME,
                'hierarchy_code' => $employee->USERS_HIERARCHY_CODE,
                'position_id' => $employee->USERS_ID_POSISI,
                'position' => $employee->USERS_POSISI,
                'work_unit_id' => $employee->USERS_ID_UNIT_KERJA,
                'work_unit' => $employee->USERS_UNIT_KERJA,
                'users_flag' => $employee->USERS_FLAG,
                'is_active' => false,
                'password' => bcrypt($employee->USERS_NPK),
            ]);

            $user->assignRole('Viewer');
        });

        return 1;
    }
}
