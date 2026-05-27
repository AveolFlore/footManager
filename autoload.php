<?php

$loadedClasses = [];

spl_autoload_register(function (string $class) use (&$loadedClasses): void {
    if (isset($loadedClasses[$class])) {
        return;
    }
    
    $path = str_replace('\\', '/', $class) . '.php';
    $path = lcfirst($path);
    $fullPath = __DIR__ . '/' . $path;

    if (file_exists($fullPath)) {
        require($fullPath);
        $loadedClasses[$class] = true;
        return;
    }

    // chercher dans controllers et models
    $dossiers = ['controllers', 'models'];

    foreach ($dossiers as $dossier) {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(__DIR__ . '/' . $dossier)
        );

        $fileName = basename($fullPath);

        foreach ($iterator as $file) {
            if ($file->getFilename() === $fileName) {
                require($file->getPathname());
                $loadedClasses[$class] = true;
                return;
            }
        }
    }
});
