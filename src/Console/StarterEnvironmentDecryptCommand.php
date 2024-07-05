<?php

namespace Pkt\StarterKit\Console;

use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;

use function Laravel\Prompts\textarea;

class StarterEnvironmentDecryptCommand extends Command implements PromptsForMissingInput
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pkt:env-decrypt
                    {--encrypted= : The encrypted env value}
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
        $encrypted = $this->option('encrypted');
        $key = $this->option('key');

        if (!$encrypted) {
            $encrypted = textarea(
                label: 'Enter the encrypted env value',
                hint: 'Ask your team for the encrypted value if you do not have it.',
            );
        }

        if (!$key) {
            $key = $this->secret('Enter the encryption key');
        }

        file_put_contents('.env.starter.encrypted', $encrypted);
        $this->call('env:decrypt', [
            '--key' => $key,
            '--cipher' => $this->option('cipher'),
            '--env' => 'starter',
            '--force' => $this->option('force'),
            '--filename' => '.env.starter.decrypted',
        ]);
        unlink('.env.starter.encrypted');

        return 1;
    }
}
