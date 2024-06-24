<?php

namespace Pkt\StarterKit\Actions;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Pkt\StarterKit\Casts\Encrypted;
use Illuminate\Support\Str;
use Pkt\StarterKit\Helpers\Crypt;

class ReEncryptData
{
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
     * Execute the action.
     *
     * @return void
     */
    public function __invoke()
    {
        $this->newCryptKey = Str::random(32);
        $this->newCryptIv = Str::random(16);

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
                    $this->reEncryptData($model, $encryptedColumns);
                }
            }
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
        DB::commit();
        $this->replaceCryptKeyAndIv();
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
                        $columnData = $model->{$column};
                        $model->withCasts([$column => 'string']);
                        $model->{$column} = Crypt::encrypt($columnData, config('crypt.cipher'), $this->newCryptIv, $this->newCryptKey);
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
