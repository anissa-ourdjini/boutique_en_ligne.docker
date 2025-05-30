<?php

spl_autoload_register(function ($class) {
    // Convert namespace to full file path
    $path = __DIR__ . '/' . str_replace('\\', '/', $class) . '.php';

    if (file_exists($path)) {
        require_once $path;
    }
});