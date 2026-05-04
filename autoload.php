<?php
 
spl_autoload_register(function (string $class): void {
    $path = str_replace('\\', '/', $class) . '.php';
    $path = lcfirst($path);
    $fullPath = __DIR__ . '/' . $path;
 
    if (file_exists($fullPath)) {
        require($fullPath);
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
                return;
            }
        }
    }
});
 