<?php

namespace Pkt\StarterKit\Console\LeaderCommand;

use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Support\Facades\DB;
use Pkt\StarterKit\Helpers\LeaderApi;

class SyncLeaderCommand extends Command implements PromptsForMissingInput
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pkt:leader-sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync PKT Leader sync users data';

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
        if (!env('LEADER_API_KEY') || !file_exists(database_path('migrations/2024_04_04_000000_add_leader_to_users_table.php'))) {
            $this->error('Please initialize PKT Leader first. Run php artisan pkt:leader-init');
            return 0;
        }

        // ask to confirm before proceed
        if (!$this->confirm('This command will sync users data from PKT Leader API. Do you want to proceed?')) {
            return 0;
        }

        // get all employee from Leader API
        $this->components->task('Syncing users data...', function () {
            $employees = LeaderApi::getAllEmployee();
            DB::beginTransaction();
            try {
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
                        'users_flag' => $employee->USERS_FLAG,
                        'is_active' => false,
                        'password' => '$2y$12$K7iSlaMTjZpgfiLEFMHbM.3O3LADzHvQYWkYaXJMQYWAIjgAF3.hy', // Bontang123@2024
                    ]);
                    $user->assignRole('Viewer');
                });
            } catch (\Exception $e) {
                DB::rollBack();
                $this->error('Failed to get users data from PKT Leader API.');
                return 0;
            }
            DB::commit();

            $this->info('Synced ' . $employees->count() . ' users from PKT Leader.');
        });

        $this->line('');
        $this->info('PKT Leader sync successfully.');
        return 1;
    }
}
