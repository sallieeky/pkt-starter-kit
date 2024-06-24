<?php

namespace Pkt\StarterKit\Actions;

use Illuminate\Support\Facades\File;
use Pkt\StarterKit\Casts\Encrypted;
use Illuminate\Support\Str;

class ReEncryptData
{
    public function __invoke()
    {
        $this->replaceCryptKeyAndIv();

        // $files = File::allFiles(app_path('Models'));
        // foreach ($files as $file) {
        //     $model = 'App\\Models\\' . str_replace('/', '\\', str_replace('.php', '', $file->getRelativePathname()));
        //     $model = new $model;

        //     $casts = $model->getCasts();
        //     $encryptedColumns = [];
        //     foreach ($casts as $key => $value) {
        //         if (str_contains($value, Encrypted::class)) {
        //             $encryptedColumns[] = $key;
        //         }
        //     }
        //     $data = $model->select($encryptedColumns)->get();
        //     foreach ($data as $row) {
        //         foreach ($encryptedColumns as $column) {
        //             $row->{$column} = decrypt($row->{$column});
        //         }
        //         $row->save();
        //     }
        // }
    }

    private function replaceCryptKeyAndIv()
    {
        $envFile = base_path('.env');

        $oldCryptKey = config('crypt.key');
        $oldCryptIv = config('crypt.iv');

        $newCryptKey = Str::random(32);
        $newCryptIv = Str::random(16);

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
}
