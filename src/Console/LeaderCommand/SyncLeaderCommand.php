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
        $numberOfUsers = 0;
        $this->components->task('Syncing users data...', function () use (&$numberOfUsers) {
            DB::beginTransaction();
            try {
                $employees = LeaderApi::getAllEmployee();
                $employees->each(function ($employee) use (&$numberOfUsers) {
                    $user = app('App\\Models\\User')::query()->where('npk', $employee->USERS_NPK)->first();
                    $dataUser = [
                        'name' => $employee->USERS_NAME,
                        'email' => $employee->USERS_EMAIL,
                        'npk' => $employee->USERS_NPK,
                        'username' => $employee->USERS_USERNAME,
                        'hierarchy_code' => $employee->USERS_HIERARCHY_CODE,
                        'position_id' => $employee->USERS_ID_POSISI,
                        'position' => $employee->USERS_POSISI,
                        'work_unit_id' => $employee->USERS_ID_UNIT_KERJA,
                        'work_unit' => $employee->USERS_UNIT_KERJA,
                        'user_flag' => $employee->USERS_FLAG,
                        'user_alias' => $employee->USERS_ALIAS,
                    ];
                    if($user){
                        $user->update($dataUser);
                    }else{
                        $dataUser['is_active'] = false;
                        $dataUser['password'] = '$2y$12$K7iSlaMTjZpgfiLEFMHbM.3O3LADzHvQYWkYaXJMQYWAIjgAF3.hy'; // Bontang123@2024
                        $user = app('App\\Models\\User')::create($dataUser);
                        $user->assignRole('Viewer');
                    }

                    $numberOfUsers++;
                });
            } catch (\Exception $e) {
                DB::rollBack();
                $this->error('Failed to get users data from PKT Leader API.');
                return 0;
            }
            DB::commit();
        });

        $this->line('');
        $this->info('Synced ' . $numberOfUsers . ' users from PKT Leader.');
        $this->info('PKT Leader sync successfully.');
        return 1;
    }
}
