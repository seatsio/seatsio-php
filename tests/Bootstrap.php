<?php

loadLibs();
loadTestUtilities();


function loadTestUtilities()
{
    loadDirectoryContents(dirname(__FILE__) . '/util');
}

function loadDirectoryContents($directoryPath)
{
    $directoryContents = getDirContents($directoryPath);
    foreach ($directoryContents as $filename) {
        require $filename;
    }
}

function getDirContents($dir, &$results = array())
{
    $files = scandir($dir);
    foreach ($files as $key => $value) {
        $path = realpath($dir . DIRECTORY_SEPARATOR . $value);
        if (is_file($path)) {
            $results[] = $path;
        } else if (is_dir($path) && $value != "." && $value != "..") {
            getDirContents($path, $results);
        }
    }

    return $results;
}

function loadLibs()
{
    require dirname(__FILE__) . '/../vendor/autoload.php';
}
