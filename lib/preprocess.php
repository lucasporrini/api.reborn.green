<?php

// Define global functions

function dd($var) {
    echo '<pre>';
    var_dump($var);
    echo '</pre>';
    die();
}

function d($var) {
    echo '<pre>';
    var_dump($var);
    echo '</pre>';
}

function write_log($file, $keyWord, $message, $colorCode = null) {
    // Define colors
    $colors = [
        'red' => "\033[31m",
        'green' => "\033[32m",
        'yellow' => "\033[33m",
        'blue' => "\033[34m",
        'purple' => "\033[35m",
        'cyan' => "\033[36m",
        'white' => "\033[37m",
        'default' => "\033[0m"
    ];
    $color = $colors[$colorCode] ?? $colors['default'];

    // End color
    $endColor = "\033[0m";

    // Search the file into the logs directory or any folder in the logs directory
    $test = glob('./logs/' . $file . '.log') ?? glob('./logs/*/' . $file . '.log');
    dd($test);
    $logFile = glob('./logs/' . $file . '.log')[0] ?? glob('./logs/*/' . $file . '.log')[0];

    // If the file doesn't exist, create it
    if (!file_exists($logFile)) {
        file_put_contents($logFile, '');
    }

    // Define the message
    $message = $color . $keyWord . $endColor . ' (' . date('d/m/Y H:i:s') . '): ' . $message;

    // Write the message into the file
    file_put_contents($logFile, $message . "\n", FILE_APPEND);
}

?>