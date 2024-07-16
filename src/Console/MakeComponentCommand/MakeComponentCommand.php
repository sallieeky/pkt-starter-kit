<?php

namespace Pkt\StarterKit\Console\MakeComponentCommand;

use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class MakeComponentCommand extends Command implements PromptsForMissingInput
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pkt:make-component 
                    {name : component name/path}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create new frontend component';

    /**
     * Execute the console command.
     *
     * @return int|null
     */
    public function handle()
    {
        $nameArgument = ucfirst($this->argument('name'));
        $regexCheck = '/^[a-zA-Z0-9\/]+$/';
        if(!preg_match($regexCheck, $nameArgument)){
            $this->components->error('Please input correctly page name without using symbol charater or whitespace');
            return 0;
        }
        $dirName = dirname($nameArgument);
        $fileName = basename($nameArgument);
        
        (new Filesystem)->ensureDirectoryExists(resource_path('js/Components/'.$dirName));

        if(file_exists(resource_path('js/Components/' . $nameArgument . '.vue'))){
            $this->components->error('Component js/Components/' . $nameArgument . '.vue already exists.');
            return 0;
        }

        copy(__DIR__.'/../../../additional-stubs/vue/resources/js/Components/BlankComponent.vue', resource_path('js/Components/' . $nameArgument . '.vue'));
        $this->replaceContent(resource_path('js/Components/' . $nameArgument . '.vue'), [
            'ComponentName' => Str::headline($fileName),
        ]);

        $this->components->info('Component js/Components/' . $nameArgument . '.vue created successfully.');
        return 1;
    }

    
    protected function replaceContent($file, $replacements)
    {
        $content = file_get_contents($file);
        $content = str_replace(array_keys($replacements), array_values($replacements), $content);
        file_put_contents($file, $content);
     }
}
