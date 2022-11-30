<?php

namespace A17\ComponentTransformers\Commands;

use A17\ComponentTransformers\Traits\Helpers;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class Create extends Command
{
    use Helpers;

    public $signature = 'transformer:create';

    public $description = 'Create a new component transformer';

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @param  Filesystem  $filesystem
     */
    public function __construct(Filesystem $filesystem)
    {
        parent::__construct();
        $this->filesystem = $filesystem;
        $this->vendor_path = $this->get_vendor_path();
    }

    public function handle(): int
    {
        $transformers = config('component-transformers.transformers') ?? [];
        $transformer_stub = $this->filesystem->get($this->vendor_path.'/stubs/transformer.php');
        $method_stub = $this->filesystem->get($this->vendor_path.'/stubs/method.php');

        // get component name
        $component_name = $this->anticipate('What is the name of the component?', array_keys($transformers));

        if ($component_name) {
            $component_name = Str::ucfirst($component_name);
            $component_name_lower = Str::lower($component_name);
        } else {
            $this->error('No component name set. Aborting');

            exit(0);
        }

        if (Arr::has($transformers, $component_name_lower)) {
            $existing_classname = $transformers[$component_name_lower];
            $this->info('Using existing transformer - '.$existing_classname);

            $namespace = Str::beforeLast($existing_classname, '\\');
        } else {
            $namespace = $this->ask('What is the namespace you want to use?', 'App\Transformers');
        }

        $variation_name = $this->ask('What is the name of the variation?', 'primary');

        if ($variation_name) {
            $variation_name = Str::snake($variation_name);
        } else {
            $this->error('No variation name set. Aborting');

            exit;
        }

        $replacements = [
            ':namespace:' => $namespace,
            ':component:' => $component_name,
            ':variation:' => $variation_name,
        ];

        $replacements[':method:'] = str_replace(
            array_keys($replacements),
            array_values($replacements),
            $method_stub
        );

        if (Str::of($namespace)->lower()->startsWith('app')) {
            $file_path = Str::replace(['app\\', 'App\\'], '', $namespace);
        } else {
            $file_path = $namespace;
        }

        $file_path = Str::of($file_path)->replace('\\', '/');
        $file = app_path($file_path.'/'.$component_name.'.php');

        if ($this->filesystem->exists($file)) {
            $contents = $this->filesystem->get($file);

            if (Str::contains($contents, 'public function '.$variation_name)) {
                $this->error('Variation \''.$variation_name.'\' already exists. Re-run with a different variation name. Aborting');

                exit(0);
            } else {
                $method = str_replace(
                    array_keys($replacements),
                    array_values($replacements),
                    $method_stub
                );

                file_put_contents(
                    $file,
                    Str::replaceLast('}', $method."\n}", $contents)
                );

                $this->info('Variation added to existing transformer:');
                $this->info($file);

                $show_add_prompt = ! Arr::has($transformers, $component_name_lower);
            }
        } else {
            file_put_contents(
                $file,
                str_replace(
                    array_keys($replacements),
                    array_values($replacements),
                    $transformer_stub
                )
            );

            $this->info('Transformer created:');
            $this->info($file);

            $show_add_prompt = true;
        }

        if ($show_add_prompt) {
            $this->newLine();

            $this->info('Add the class to the `transformers` array in your `config/component-transformers.php`:');

            $this->newLine();
            $this->info("'transformers' => [\n    $component_name_lower => \\$namespace\\$component_name::class \n]");
        }

        return self::SUCCESS;
    }
}
