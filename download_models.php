<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$models = [
    ['name' => 'tiny_face_detector_model-shard1', 'size' => 193321],
    ['name' => 'tiny_face_detector_model-weights_manifest.json', 'size' => 2953],
    ['name' => 'face_landmark_68_model-shard1', 'size' => 356840],
    ['name' => 'face_landmark_68_model-weights_manifest.json', 'size' => 7889],
    ['name' => 'face_recognition_model-shard1', 'size' => 4194304],
    ['name' => 'face_recognition_model-shard2', 'size' => 6507872],
    ['name' => 'face_recognition_model-weights_manifest.json', 'size' => 18303]
];

$baseUrl = 'https://raw.githubusercontent.com/WebDevSimplified/Face-Detection-JavaScript/master/models/';
$modelDir = __DIR__ . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR;

// First, clean up the models directory
if (file_exists($modelDir)) {
    $files = glob($modelDir . '*');
    foreach ($files as $file) {
        if (is_file($file)) {
            unlink($file);
        }
    }
} else {
    mkdir($modelDir, 0777, true);
}

$allSuccess = true;

// Download each model file
foreach ($models as $model) {
    $url = $baseUrl . $model['name'];
    $destination = $modelDir . $model['name'];
    
    echo "Downloading {$model['name']}...\n";
    
    // Use cURL for better error handling
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36');
    
    $content = curl_exec($ch);
    
    if (curl_errno($ch)) {
        echo "Error downloading {$model['name']}: " . curl_error($ch) . "\n";
        $allSuccess = false;
        curl_close($ch);
        continue;
    }
    
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if ($httpCode !== 200) {
        echo "Error downloading {$model['name']}: HTTP code {$httpCode}\n";
        $allSuccess = false;
        curl_close($ch);
        continue;
    }
    
    curl_close($ch);
    
    if (file_put_contents($destination, $content) === false) {
        echo "Error saving {$model['name']}\n";
        $allSuccess = false;
        continue;
    }
    
    // Verify file was saved and has content
    if (!file_exists($destination)) {
        echo "Error: File {$model['name']} was not saved\n";
        $allSuccess = false;
        continue;
    }
    
    $fileSize = filesize($destination);
    if ($fileSize === 0) {
        echo "Error: File {$model['name']} is empty\n";
        $allSuccess = false;
        continue;
    }
    
    echo "Successfully downloaded {$model['name']} ({$fileSize} bytes)\n";
}

if ($allSuccess) {
    echo "\nAll models downloaded successfully!\n";
    echo "Models directory: " . realpath($modelDir) . "\n";
    
    // List all downloaded files
    echo "\nDownloaded files:\n";
    foreach (glob($modelDir . '*') as $file) {
        echo basename($file) . " - " . filesize($file) . " bytes\n";
    }
} else {
    echo "\nSome models failed to download. Please check the errors above and try again.\n";
    exit(1);
} 