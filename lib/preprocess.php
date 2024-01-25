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

function write_log($file, $keyWord, $message, $colorCode = 'default') {
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
    $color = $colors[$colorCode];

    // End color
    $endColor = "\033[0m";

    // Define the log file path
    $logFilePath = './logs/' . $file . '.log';

    // Search the file in the logs directory and its subdirectories
    if (!file_exists($logFilePath)) {
        $foundFiles = glob('./logs/**/' . $file . '.log', GLOB_BRACE);
        if (!empty($foundFiles)) {
            $logFilePath = $foundFiles[0]; // Take the first found file
        }
    }

    // If the file doesn't exist, create it
    if (!file_exists($logFilePath)) {
        if (!is_dir(dirname($logFilePath))) {
            mkdir(dirname($logFilePath), 0777, true);
        }
        file_put_contents($logFilePath, '');
    }

    // Define the message
    $formattedMessage = $color . $keyWord . $endColor . ' (' . date('d/m/Y H:i:s') . '): ' . $message;

    // Write the message into the file
    file_put_contents($logFilePath, $formattedMessage . "\n", FILE_APPEND);
}

function convert_to_utf8($input) {
    if (is_array($input)) {
        foreach ($input as $key => $value) {
            $input[$key] = convert_to_utf8($value);
        }
    } elseif (is_string($input)) {
        return utf8_encode($input);
    }
    return $input;
}

?>