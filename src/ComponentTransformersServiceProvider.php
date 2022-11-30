<?php

namespace A17\ComponentTransformers;

use A17\ComponentTransformers\Commands\Create;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class ComponentTransformersServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('component-transformers')
            ->hasCommand(Create::class)
            ->hasConfigFile();
    }
}
