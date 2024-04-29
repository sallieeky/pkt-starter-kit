<?php

namespace Pkt\StarterKit\Console\MakeBlankPageCommand;

use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;

class MakeVueBlankPageCommand extends Command implements PromptsForMissingInput
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pkt:create-page 
                            {name : page name/path}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create blank vue page';

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
            $this->error('Please input correctly page name without using symbol charater or whitespace');
            return 0;
        }
        copy(__DIR__.'/../../../resource-template/vue/resources/js/Pages/BlankPage.vue', resource_path('js/Pages/' . $nameArgument . '.vue'));
        return 1;
    }
}
