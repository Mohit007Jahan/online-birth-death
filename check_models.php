<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$modelFiles = [
    'tiny_face_detector_model-shard1',
    'tiny_face_detector_model-weights_manifest.json',
    'face_landmark_68_model-shard1',
    'face_landmark_68_model-weights_manifest.json',
    'face_recognition_model-shard1',
    'face_recognition_model-shard2',
    'face_recognition_model-weights_manifest.json'
];

$modelDir = __DIR__ . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR;

echo "Checking model files:\n\n";

foreach ($modelFiles as $file) {
    $path = $modelDir . $file;
    
    if (!file_exists($path)) {
        echo "❌ {$file}: File not found\n";
        continue;
    }
    
    $size = filesize($path);
    $readable = is_readable($path) ? "Yes" : "No";
    $perms = substr(sprintf('%o', fileperms($path)), -4);
    
    echo "✓ {$file}:\n";
    echo "  - Size: {$size} bytes\n";
    echo "  - Readable: {$readable}\n";
    echo "  - Permissions: {$perms}\n";
    echo "  - Web URL: http://localhost/birth%20&%20death%20beta%203/models/{$file}\n\n";
}

// Check .htaccess
$htaccess = $modelDir . '.htaccess';
if (!file_exists($htaccess)) {
    echo "Creating .htaccess file to ensure proper MIME types...\n";
    file_put_contents($htaccess, "
<IfModule mod_mime.c>
    AddType application/json .json
    AddType application/octet-stream .bin
</IfModule>

<IfModule mod_headers.c>
    Header set Access-Control-Allow-Origin \"*\"
</IfModule>
");
} 