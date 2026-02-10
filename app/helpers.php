<?php

if (! function_exists('asset_root')) {
    function asset_root(string $path): string
    {
        return '/' . ltrim($path, '/');
    }
}
