<?php

namespace Pkt\StarterKit\Console;

use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Pkt\StarterKit\Casts\Encrypted;
use Pkt\StarterKit\Helpers\Crypt;

class RegenerateEncryptedDataCommand extends Command implements PromptsForMissingInput
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pkt:regenerate-encrypted-data
                    {--generate : Automatically regenerate the encryption key and iv}
                    {--key= : The encryption key}
                    {--iv= : The encryption iv}
                    {--previous-key= : The previous encryption key}
                    {--previous-iv= : The previous encryption iv}
                    {--yes : Skip confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Regenerate encrypted data in the database';

    /**
     * The new crypt key.
     *
     * @var string
     */
    private $newCryptKey;

    /**
     * The new crypt iv.
     *
     * @var string
     */
    private $newCryptIv;

    /**
     * Execute the console command.
     *
     * @return int|null
     */
    public function handle()
    {
        if (
            !config('crypt.regenerate') || 
            (config('crypt.key') === null || config('crypt.iv') === null) || 
            (!$this->option('generate') && 
                (!config('crypt.previous_key') || !config('crypt.previous_iv'))
            )
        ){
            $this->error('The encryption key and iv cannot be regenerated.');
            return 0;
        }

        if ($this->option('generate')) {
            $envKey = $this->option('key') ?? $this->ask('What is the encryption key?');
            if ($envKey !== config('crypt.key')) {
                $this->error('The encryption key is not the same as the one in your .env file.');
                return 0;
            }
    
            $envIv = $this->option('iv') ?? $this->ask('What is the encryption iv?');
            if ($envIv !== config('crypt.iv')) {
                $this->error('The encryption iv is not the same as the one in your .env file.');
                return 0;
            }
        } else {
            $envKey = $this->option('previous-key') ?? $this->ask('What is the previous encryption key?');
            if ($envKey !== config('crypt.previous_key')) {
                $this->error('The previous encryption key is not the same as the one in your .env file.');
                return 0;
            }
    
            $envIv = $this->option('previous-iv') ?? $this->ask('What is the previous encryption iv?');
            if ($envIv !== config('crypt.previous_iv')) {
                $this->error('The previous encryption iv is not the same as the one in your .env file.');
                return 0;
            }
        }

        $confirmation = $this->option('yes') || $this->confirm('Are you sure you want to regenerate the encrypted data in the database?');
        if (!$confirmation) {
            $this->info('The encrypted data in the database was not regenerated.');
            return 0;
        }

        $this->newCryptKey = Str::random(32);
        $this->newCryptIv = Str::random(16);

        $models = [];
        $this->components->task('Re-encrypt data in the database...', function () use (&$models) {
            DB::beginTransaction();
            try {
                $files = File::allFiles(app_path('Models'));
                foreach ($files as $file) {
                    $model = 'App\\Models\\' . str_replace('/', '\\', str_replace('.php', '', $file->getRelativePathname()));
                    $model = new $model;
    
                    $casts = $model->getCasts();
                    $encryptedColumns = [];
    
                    foreach ($casts as $key => $value) {
                        if (str_contains($value, Encrypted::class)) {
                            $encryptedColumns[] = $key;
                        }
                    }
                    if (count($encryptedColumns) > 0) {
                        $models[$model->getTable()] = $encryptedColumns;
                        $this->reEncryptData($model, $encryptedColumns);
                    }
                }
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
            DB::commit();
        });

        if ($this->option('generate')) {
            $this->components->task('Replace the old crypt key and iv with the new crypt key and iv...', function () {
                $this->replaceCryptKeyAndIv();
            });
        }

        $this->table(
            ['Table Name', 'Encrypted Columns'],
            collect($models)->map(function ($columns, $model) {
                return [$model, implode(', ', $columns)];
            })
        );

        $this->components->info('The encrypted data in the database has been regenerated.');

        return 1;
    }

    /**
     * Replace the old crypt key and iv with the new crypt key and iv.
     * Also, set the old crypt key and iv as previous key and iv.
     * This will change the .env file.
     *
     * @return void
     */
    private function replaceCryptKeyAndIv(): void
    {
        $envFile = base_path('.env');

        $oldCryptKey = config('crypt.key');
        $oldCryptIv = config('crypt.iv');

        $newCryptKey = $this->newCryptKey;
        $newCryptIv = $this->newCryptIv;

        $envContent = file_get_contents($envFile);
        if (str_contains($envContent, 'CRYPT_PREVIOUS_KEY')) {
            $envContent = str_replace($oldCryptKey, $newCryptKey, $envContent);
            $envContent = str_replace($oldCryptIv, $newCryptIv, $envContent);
            $envContent = preg_replace('/CRYPT_PREVIOUS_KEY=(.*)/', 'CRYPT_PREVIOUS_KEY=' . $oldCryptKey, $envContent);
            $envContent = preg_replace('/CRYPT_PREVIOUS_IV=(.*)/', 'CRYPT_PREVIOUS_IV=' . $oldCryptIv, $envContent);
        } else {
            $envCryptKey = $newCryptKey . "\nCRYPT_PREVIOUS_KEY=" . $oldCryptKey;
            $envCryptIv = $newCryptIv . "\nCRYPT_PREVIOUS_IV=" . $oldCryptIv;

            $envContent = str_replace($oldCryptKey, $envCryptKey, $envContent);
            $envContent = str_replace($oldCryptIv, $envCryptIv, $envContent);
        }

        file_put_contents($envFile, $envContent);
    }

    /**
     * Re-encrypt the data in the database.
     *
     * @param string $model
     * @param array $encryptedColumns
     *
     * @return void
     */
    private function reEncryptData($model, $encryptedColumns): void
    {
        DB::beginTransaction();
        try {
            $model::query()->lockForUpdate()->chunk(100, function ($models) use ($encryptedColumns) {
                foreach ($models as $model) {
                    foreach ($encryptedColumns as $column) {
                        $model->withCasts([$column => 'string']);
                        if ($model->{$column} === null) {
                            continue;
                        }
                        if ($this->option('generate')) {
                            $columnData = Crypt::decrypt($model->{$column}, config('crypt.cipher'), config('crypt.iv'), config('crypt.key'));
                            $model->{$column} = Crypt::encrypt($columnData, config('crypt.cipher'), $this->newCryptIv, $this->newCryptKey);
                        } else {
                            $columnData = Crypt::decrypt($model->{$column}, config('crypt.cipher'), config('crypt.previous_iv'), config('crypt.previous_key'));
                            $model->{$column} = Crypt::encrypt($columnData, config('crypt.cipher'), config('crypt.iv'), config('crypt.key'));
                        }
                    }
                    $model->updateQuietly();
                }
            });
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
        DB::commit();
    }
}
