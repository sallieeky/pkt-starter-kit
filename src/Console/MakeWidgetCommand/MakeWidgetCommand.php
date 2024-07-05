<?php

namespace Pkt\StarterKit\Console\MakeWidgetCommand;

use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class MakeWidgetCommand extends Command implements PromptsForMissingInput
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pkt:make-widget 
                    {name : widget name/path}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create new frontend widget component';

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
        $dirName = dirname($nameArgument);
        $fileName = basename($nameArgument);
        
        (new Filesystem)->ensureDirectoryExists(resource_path('js/Widgets/'.$dirName));

        if(file_exists(resource_path('js/Widgets/' . $nameArgument . '.vue'))){
            $this->error('Widget js/Widgets/' . $nameArgument . '.vue already exists.');
            return 0;
        }

        $type = $this->choice('Select type of Widget', [
            'Blank Widget',
            'Table Widget',
            'Bar/Column Chart Widget',
            'Line Chart Widget',
            'Pie Chart Widget',
            'Donut Chart Widget',
        ], 0);

        if($type == 'Blank Widget'){
            copy(__DIR__.'/../../../additional-stubs/vue/resources/js/Widgets/BlankWidget.vue', resource_path('js/Widgets/' . $nameArgument . '.vue'));
        }else if($type == 'Table Widget'){
            copy(__DIR__.'/../../../additional-stubs/vue/resources/js/Widgets/TableWidget.vue', resource_path('js/Widgets/' . $nameArgument . '.vue'));
        }else if($type == 'Bar/Column Chart Widget'){
            copy(__DIR__.'/../../../additional-stubs/vue/resources/js/Widgets/ColumnChartWidget.vue', resource_path('js/Widgets/' . $nameArgument . '.vue'));
        }else if($type == 'Line Chart Widget'){
            copy(__DIR__.'/../../../additional-stubs/vue/resources/js/Widgets/LineChartWidget.vue', resource_path('js/Widgets/' . $nameArgument . '.vue'));
        }else if($type == 'Pie Chart Widget'){
            copy(__DIR__.'/../../../additional-stubs/vue/resources/js/Widgets/PieChartWidget.vue', resource_path('js/Widgets/' . $nameArgument . '.vue'));
        }else if($type == 'Donut Chart Widget'){
            copy(__DIR__.'/../../../additional-stubs/vue/resources/js/Widgets/DonutChartWidget.vue', resource_path('js/Widgets/' . $nameArgument . '.vue'));
        }

        $this->replaceContent(resource_path('js/Widgets/' . $nameArgument . '.vue'), [
            'WidgetName' => Str::headline($fileName),
        ]);

        $this->info('Widget js/Widgets/' . $nameArgument . '.vue created successfully.');
        return 1;
    }

    
    protected function replaceContent($file, $replacements)
    {
        $content = file_get_contents($file);
        $content = str_replace(array_keys($replacements), array_values($replacements), $content);
        file_put_contents($file, $content);
     }
}