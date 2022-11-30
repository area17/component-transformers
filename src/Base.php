<?php

namespace A17\ComponentTransformers;

use Illuminate\Database\Eloquent\Model;

class Base
{
    public $data;

    public static function make($data)
    {
        $instance = new static();

        $instance->data = $data;

        return $instance;
    }

    protected function hasMany($data = null): bool
    {
        if (! $data) {
            return false;
        }

        return is_array($data) || ! $data instanceof Model;
    }

    public function makeMany($component = null, $variation = null, $data = null): array
    {
        if (! $component || ! $variation) {
            throw new \Exception('Component or Variation not set');

            return false;
        }

        $data = $data ?: $this->data;

        if (! $data || empty($data)) {
            return [];
        }

        $items = collect($data)
            ->filter()
            ->map(fn ($item) => Transform::$component($item)->$variation())
            ->toArray();

        return $items ?? null;
    }
}
