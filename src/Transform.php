<?php

namespace A17\ComponentTransformers;

use Exception;

class Transform
{
    public $data;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    public static function __callStatic($method, $params)
    {
        $transformers = config('component-transformers.transformers') ?? [];
        $transformer = $transformers[$method] ?? false;

        if (! $transformer) {
            throw new Exception("Transformer `$method` not set");
        }

        return $transformer::make(...$params);
    }
}
