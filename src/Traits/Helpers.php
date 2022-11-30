<?php

namespace A17\ComponentTransformers\Traits;

use Illuminate\Support\Str;

trait Helpers
{
    /**
     * Returns the full vendor_path for Blast.
     *
     * @return string
     */
    private function get_vendor_path()
    {
        $vendor_path = config('component-transformers.vendor_path');

        if (Str::startsWith($vendor_path, '/')) {
            return $vendor_path;
        }

        return base_path($vendor_path);
    }
}
