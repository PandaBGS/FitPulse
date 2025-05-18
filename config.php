<?php
// File-based storage paths
define('DATA_DIR', __DIR__ . '/data');
define('USERS_FILE', DATA_DIR . '/users.json');
define('SCHEDULES_FILE', DATA_DIR . '/schedules.json');
define('RECOMMENDATIONS_FILE', DATA_DIR . '/recommendations.json');
define('PROGRESS_FILE', DATA_DIR . '/progress.json');

// Ensure data directory exists
if (!file_exists(DATA_DIR)) {
    mkdir(DATA_DIR, 0755, true);
}

// Helper functions to read and write JSON data
function readData($file) {
    if (!file_exists($file)) {
        return [];
    }
    $json = file_get_contents($file);
    return json_decode($json, true) ?: [];
}

function writeData($file, $data) {
    $json = json_encode($data, JSON_PRETTY_PRINT);
    file_put_contents($file, $json);
}
?>
