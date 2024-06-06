<?php

namespace Pkt\StarterKit\Console\LeaderCommand;

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
    protected $signature = 'pkt:leader-init';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initialize PKT Leader with sync users data from PKT Leader';

    /**
     * Execute the console command.
     *
     * @return int|null
     */
    public function handle()
    {
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

        // ask to confirm before proceed
        if (!$this->confirm('This command will sync users data from PKT Leader API. Do you want to proceed?')) {
            return 0;
        }

        // check if leader already installed
        if (file_exists(database_path('migrations/2024_04_04_000000_add_leader_to_users_table.php'))) {
            $this->info('You already initialize the leader.');

            // ask to sync again
            if ($this->confirm('Do you want to sync user?')) {
                $this->call('pkt:leader-sync');
            }

            return 0;
        }

        // copy migration and run migrate
        $this->components->task('Updating users table and migrate...', function () {
            copy(__DIR__.'/../../../leader-stubs/database/migrations/2024_04_04_000000_add_leader_to_users_table.php', database_path('migrations/2024_04_04_000000_add_leader_to_users_table.php'));
            $this->call('migrate');
        });

        // get all employee from Leader API
        $this->components->task('Syncing users data...', function () {
            $employees = LeaderApi::getAllEmployee();
            $employees->each(function ($employee) {
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
                    'user_flag' => $employee->USERS_FLAG,
                    'user_alias' => $employee->USERS_ALIAS,
                    'is_active' => false,
                    'password' => '$2y$12$K7iSlaMTjZpgfiLEFMHbM.3O3LADzHvQYWkYaXJMQYWAIjgAF3.hy', // Bontang123@2024
                ]);
                $user->assignRole('Viewer');
            });

            $this->info('Synced ' . $employees->count() . ' users from PKT Leader.');
        });

        $this->line('');
        $this->info('PKT Leader init and sync successfully.');
        return 1;
    }
}
