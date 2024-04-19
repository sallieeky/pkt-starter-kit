<?php

namespace Pkt\StarterKit\Console\MakeResourceCommand;

use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;

class MakeResourceCommand extends Command implements PromptsForMissingInput
{
    use ManipulateVuePage;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pkt:make-resource {name : The name of the resource}
            {--force : Overwrite existing files}
            {--generate : Generate the resource with default values}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new resource with base template';

    /**
     * Execute the console command.
     *
     * @return int|null
     */
    public function handle()
    {
        // make sure argument name capitalized
        $nameArgument = ucfirst($this->argument('name'));
        try {
            $model = app('App\\Models\\' . $nameArgument);
        } catch (\Exception $e) {
            $this->error('Model not found: ' . $nameArgument);
            return 0;
        }

        $this->manipulateVueResource($model, $nameArgument);
        return 1;
    }


    protected function replaceContent($file, $replacements)
    {
        $content = file_get_contents($file);
        $content = str_replace(array_keys($replacements), array_values($replacements), $content);
        file_put_contents($file, $content);
    }
}
