<?php

namespace Pkt\StarterKit\Console\MakeBlankPageCommand;

use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class MakeVueBlankPageCommand extends Command implements PromptsForMissingInput
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pkt:make-page 
                    {name : page name/path}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create blank frontend page';

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
        $type = $this->choice('Select page type', ['Blank/custom page', 'Custom page with form', 'Custom page with table'], 0);
        
        (new Filesystem)->ensureDirectoryExists(resource_path('js/Pages/'.$dirName));
        if(file_exists(resource_path('js/Pages/' . $nameArgument . '.vue'))){
            $this->components->error('Page js/Pages/' . $nameArgument . '.vue already exists.');
            return 0;
        }

        if($type === 'Blank/custom page'){
            copy(__DIR__.'/../../../additional-stubs/vue/resources/js/Pages/BlankPage.vue', resource_path('js/Pages/' . $nameArgument . '.vue'));
        }else if($type === 'Custom page with form'){
            copy(__DIR__.'/../../../additional-stubs/vue/resources/js/Pages/FormPage.vue', resource_path('js/Pages/' . $nameArgument . '.vue'));
        }else if($type === 'Custom page with table'){
            copy(__DIR__.'/../../../additional-stubs/vue/resources/js/Pages/TablePage.vue', resource_path('js/Pages/' . $nameArgument . '.vue'));
        } else {
            $this->components->error('Invalid page type');
            return 0;
        }
        $this->replaceContent(resource_path('js/Pages/' . $nameArgument . '.vue'), [
            'PageTitle' => Str::headline($fileName),
        ]);

        $this->components->info('Page js/Pages/' . $nameArgument . '.vue created successfully.');
        return 1;
    }

    
    protected function replaceContent($file, $replacements)
    {
        $content = file_get_contents($file);
        $content = str_replace(array_keys($replacements), array_values($replacements), $content);
        file_put_contents($file, $content);
    }
}
