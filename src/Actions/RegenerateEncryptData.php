<?php

namespace Pkt\StarterKit\Actions;

use Illuminate\Support\Facades\Artisan;

class RegenerateEncryptData
{
    /**
     * Execute the action.
     *
     * @return void
     */
    public function __invoke()
    {
        $key = config('crypt.key');
        $iv = config('crypt.iv');
        $previousKey = config('crypt.previous_key');
        $previousIv = config('crypt.previous_iv');
        Artisan::call('pkt:regenerate-encrypted-data', [
            '--generate' => true,
            '--key' => $key,
            '--iv' => $iv,
            '--previous-key' => $previousKey,
            '--previous-iv' => $previousIv,
            '--yes' => true,
        ]);
    }
}
