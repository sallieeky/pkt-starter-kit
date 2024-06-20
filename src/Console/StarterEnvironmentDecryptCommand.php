<?php

namespace Pkt\StarterKit\Console;

use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;

class StarterEnvironmentDecryptCommand extends Command implements PromptsForMissingInput
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pkt:env-decrypt 
                    {--key= : The encryption key}
                    {--cipher= : The encryption cipher}
                    {--force : Force the operation to replace the existing file}';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Decrypt the encrypted starter environment file';

    /**
     * Execute the console command.
     *
     * @return int|null
     */
    public function handle()
    {
        copy(__DIR__ . '/../../stubs/default/.env.starter.encrypted', '.env.starter.encrypted');
        $this->call('env:decrypt', [
            '--key' => $this->option('key'),
            '--cipher' => $this->option('cipher'),
            '--env' => 'starter',
            '--force' => $this->option('force'),
            '--filename' => '.env.starter.decrypted',
        ]);
        unlink('.env.starter.encrypted');

        return 1;
    }
}
