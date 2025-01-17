<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'config.php';
require_once 'dbconfig.php';

// Add debugging
error_log("Starting login_face_verify.php");
error_log("Session data at start: " . print_r($_SESSION, true));

// Check if user is attempting login
if (!isset($_SESSION['temp_uid'])) {
    error_log("No temporary user ID found. Redirecting to login page");
    header("Location: user_login.php");
    exit();
}

// Helper function to calculate Euclidean distance between face descriptors
function calculateDistance($descriptor1, $descriptor2) {
    if (!is_array($descriptor1) || !is_array($descriptor2) || count($descriptor1) !== count($descriptor2)) {
        error_log("Invalid descriptor format");
        return PHP_FLOAT_MAX;
    }
    
    $sum = 0;
    for ($i = 0; $i < count($descriptor1); $i++) {
        $diff = $descriptor1[$i] - $descriptor2[$i];
        $sum += $diff * $diff;
    }
    return sqrt($sum);
}

// Handle face verification submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        error_log("Processing face verification POST request");
        
        // Validate session
        if (!isset($_SESSION['temp_uid'])) {
            throw new Exception("Session expired. Please login again.");
        }

        // Check for too many failed attempts
        $stmt = $conn->prepare("SELECT COUNT(*) as failed_count FROM tblverification_log 
                               WHERE user_id = ? AND verification_type = 'face' 
                               AND status = 'failed' AND verification_date > DATE_SUB(NOW(), INTERVAL 30 MINUTE)");
        $stmt->execute([$_SESSION['temp_uid']]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result['failed_count'] >= 5) {
            throw new Exception("Too many failed attempts. Please try again after 30 minutes.");
        }

        // Get POST data
        $postData = file_get_contents('php://input');
        error_log("Raw POST data: " . $postData);
        
        if (empty($_POST['face_descriptor'])) {
            throw new Exception("No face data received");
        }

        // Decode and validate face descriptor
        $currentFaceDescriptor = json_decode($_POST['face_descriptor'], true);
        if (!$currentFaceDescriptor || !is_array($currentFaceDescriptor)) {
            error_log("Failed to decode face descriptor: " . json_last_error_msg());
            throw new Exception("Invalid face data format");
        }

        // Validate descriptor length
        if (count($currentFaceDescriptor) !== 128) {
            error_log("Invalid descriptor length: " . count($currentFaceDescriptor));
            throw new Exception("Invalid face descriptor length");
        }

        // Get stored face data
        $stmt = $conn->prepare("SELECT face_embedding FROM tblfaceverification WHERE user_id = ? AND verification_status = 'verified' ORDER BY verification_date DESC LIMIT 1");
        if (!$stmt) {
            error_log("Database prepare error: " . implode(" ", $conn->errorInfo()));
            throw new Exception("Database error");
        }
        
        $stmt->execute([$_SESSION['temp_uid']]);
        $storedFace = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$storedFace || empty($storedFace['face_embedding'])) {
            error_log("No stored face data found for user ID: " . $_SESSION['temp_uid']);
            throw new Exception("No stored face data found");
        }

        // Decode stored face embedding
        $storedDescriptor = json_decode($storedFace['face_embedding'], true);
            if (!$storedDescriptor || !is_array($storedDescriptor)) {
            error_log("Invalid stored face descriptor");
            throw new Exception("Invalid stored face data");
        }

        // Calculate similarity score
        $distance = calculateDistance($currentFaceDescriptor, $storedDescriptor);
        $threshold = 0.6; // Adjusted threshold
        error_log("Face comparison distance: " . $distance);

        if ($distance <= $threshold) {
            error_log("Face verification successful");
            
            try {
                $conn->beginTransaction();
                
                // Update last login
                $stmt = $conn->prepare("UPDATE tbluser SET last_login = NOW() WHERE ID = ?");
                $stmt->execute([$_SESSION['temp_uid']]);

                // Log verification
                $stmt = $conn->prepare("INSERT INTO tblverification_log (user_id, verification_type, status, distance, verification_date) VALUES (?, 'face', 'success', ?, NOW())");
                $stmt->execute([$_SESSION['temp_uid'], $distance]);

                $_SESSION['uid'] = $_SESSION['temp_uid'];
                unset($_SESSION['temp_uid']);

                $conn->commit();

                echo json_encode([
                    'success' => true,
                    'message' => 'Face verification successful',
                    'distance' => $distance,
                    'redirect_url' => 'user_dashboard.php'
                ]);
                exit();
            } catch (Exception $e) {
                $conn->rollBack();
                throw $e;
            }
        } else {
            throw new Exception("Face verification failed - Please try again");
        }

    } catch (Exception $e) {
        error_log("Face verification error: " . $e->getMessage());
        
        // Log failed verification attempt
        try {
            $stmt = $conn->prepare("INSERT INTO tblverification_log (user_id, verification_type, status, distance, verification_date) VALUES (?, 'face', 'failed', ?, NOW())");
            $stmt->execute([$_SESSION['temp_uid'], $distance ?? null]);
        } catch (Exception $logError) {
            error_log("Failed to log verification attempt: " . $logError->getMessage());
        }
        
        http_response_code(400);
        echo json_encode([
            'success' => false, 
            'message' => $e->getMessage()
        ]);
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <!-- Remove restrictive CSP and Permissions Policy -->
    <!-- <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <meta http-equiv="Permissions-Policy" content="camera=(), microphone=()"> -->
    <!-- Add permissive CSP -->
    <meta http-equiv="Content-Security-Policy" content="default-src * 'unsafe-inline' 'unsafe-eval' data: blob:;">
    <title>Face Verification - Login</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- TensorFlow.js -->
    <script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs@3.11.0/dist/tf.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs-backend-webgl@3.11.0/dist/tf-backend-webgl.min.js"></script>
    <!-- Face API -->
    <script src="https://cdn.jsdelivr.net/npm/@vladmandic/face-api@1.7.12/dist/face-api.js"></script>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #b5dcb3;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        .container {
            background-color: #b5dcb3;
            border-radius: 20px;
            box-shadow: 9px 9px 16px #88b489, -9px -9px 16px #e2ffe2;
            max-width: 800px;
            width: 100%;
            padding: 30px;
            position: relative;
            transition: transform 0.3s ease-in-out;
        }
        .container:hover {
            transform: scale(1.02);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header img {
            width: 100px;
            margin-bottom: 20px;
        }
        h1 {
            color: #2c662c;
            font-size: 2rem;
            margin-bottom: 20px;
        }
        #video-container {
            width: 100%;
            max-width: 640px;
            margin: 0 auto;
            position: relative;
            background: #b5dcb3;
            border-radius: 20px;
            box-shadow: inset 6px 6px 10px #88b489, inset -6px -6px 10px #e2ffe2;
            padding: 15px;
        }
        #video {
            width: 100%;
            border-radius: 12px;
            background-color: #000;
        }
        #face-box {
            position: absolute;
            border: 2px solid #00ff00;
            display: none;
            box-shadow: 0 0 10px rgba(0, 255, 0, 0.5);
        }
        .status-message {
            background: #b5dcb3;
            border-radius: 12px;
            padding: 12px;
            box-shadow: inset 6px 6px 10px #88b489, inset -6px -6px 10px #e2ffe2;
            color: #2c662c;
            margin: 15px 0;
            text-align: center;
            font-size: 16px;
        }
        .quality-indicator {
            position: absolute;
            top: 10px;
            right: 10px;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 12px;
            background: rgba(0,0,0,0.5);
            color: white;
        }
        .retry-button {
            display: none;
            width: 100%;
            padding: 14px 28px;
            background-color: #228b22;
            color: white;
            font-size: 18px;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            box-shadow: 6px 6px 10px #88b489, -6px -6px 10px #e2ffe2;
            transition: all 0.3s ease-in-out;
            margin-top: 20px;
        }
        .retry-button:hover {
            background-color: #196619;
            box-shadow: 9px 9px 16px #88b489, -9px -9px 16px #e2ffe2;
            transform: translateY(-2px);
        }
        
        /* Mobile-specific styles */
        @media (max-width: 768px) {
            body {
                padding: 10px;
            }
            
            .container {
                padding: 15px;
            }
            
            #video-container {
                width: 100%;
                max-width: 100%;
                margin: 0;
            }
            
            #video {
                width: 100%;
                height: auto;
                max-height: 70vh;
                object-fit: cover;
            }
            
            .status-message {
                font-size: 14px;
                padding: 8px;
            }
            
            .retry-button {
                padding: 10px 20px;
                font-size: 16px;
            }
        }
        
        /* Prevent text selection on mobile */
        * {
            -webkit-tap-highlight-color: transparent;
            -webkit-touch-callout: none;
            -webkit-user-select: none;
            user-select: none;
        }
        
        /* Force hardware acceleration */
        #video-container {
            -webkit-transform: translateZ(0);
            -moz-transform: translateZ(0);
            -ms-transform: translateZ(0);
            -o-transform: translateZ(0);
            transform: translateZ(0);
            backface-visibility: hidden;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="logo.png" alt="Logo">
            <h1>Face Verification</h1>
        </div>

        <div class="status-message" id="status-message">
            <i class="fas fa-spinner fa-spin"></i> Loading face detection models...
        </div>

        <div id="video-container">
            <video id="video" playsinline autoplay></video>
            <div id="face-box"></div>
            <div id="quality-indicator" class="quality-indicator"></div>
        </div>

        <button id="retry-button" class="retry-button" onclick="retryVerification()">
            <i class="fas fa-redo"></i> Retry Verification
        </button>
    </div>

    <script>
        let model;
        let stream;
        let isProcessing = false;
        let verificationAttempts = 0;
        const maxAttempts = 3;
        let lastDetectionTime = 0;
        const detectionInterval = 100;

        async function checkCompatibility() {
            const statusMessage = document.getElementById('status-message');
            
            // Check for basic requirements
            if (!window.navigator || !window.navigator.mediaDevices) {
                statusMessage.innerHTML = '<i class="fas fa-exclamation-circle"></i> Your browser does not support camera access. Please use a modern browser.';
                return false;
            }

            // Check for WebGL support
            let webglSupport = false;
            try {
                const canvas = document.createElement('canvas');
                webglSupport = !!(window.WebGLRenderingContext && 
                    (canvas.getContext('webgl') || canvas.getContext('experimental-webgl')));
            } catch (e) {
                webglSupport = false;
            }

            // Check for WebAssembly support
            let wasmSupport = false;
            try {
                wasmSupport = typeof WebAssembly === 'object';
            } catch (e) {
                wasmSupport = false;
            }

            // Log system information
            console.log('System compatibility check:', {
                userAgent: navigator.userAgent,
                webgl: webglSupport,
                wasm: wasmSupport,
                screen: {
                    width: window.screen.width,
                    height: window.screen.height
                }
            });

            if (!webglSupport && !wasmSupport) {
                statusMessage.innerHTML = '<i class="fas fa-exclamation-circle"></i> Your device may not support face verification. Performance might be limited.';
                return true; // Still allow to try
            }

            return true;
        }

        async function loadModels() {
            try {
                console.log('Starting model loading...');
                document.getElementById('status-message').innerHTML = 
                    '<i class="fas fa-spinner fa-spin"></i> Initializing...';

                // Verify dependencies
                if (typeof tf === 'undefined') {
                    await loadScript('https://cdn.jsdelivr.net/npm/@tensorflow/tfjs@3.11.0/dist/tf.min.js');
                }
                if (typeof faceapi === 'undefined') {
                    await loadScript('https://cdn.jsdelivr.net/npm/@vladmandic/face-api@1.7.12/dist/face-api.js');
                }

                // Configure TensorFlow for better compatibility
                await tf.ready();
                if (tf.ENV.flags.IS_BROWSER) {
                    try {
                await tf.setBackend('webgl');
                        console.log('Using WebGL backend');
                    } catch (e) {
                        console.log('WebGL not available, falling back to CPU');
                        await tf.setBackend('cpu');
                    }
                }

                // Define model paths with both local and CDN fallbacks
                const modelPaths = {
                    local: './models',
                    cdn: 'https://cdn.jsdelivr.net/npm/@vladmandic/face-api/model'
                };

                // Function to load models with fallback
                async function loadModelWithFallback(modelName, loadFn) {
                    try {
                        // Try local first
                        await loadFn(modelPaths.local);
                    } catch (localError) {
                        console.log(`Local model load failed, trying CDN for ${modelName}`);
                        try {
                            // Try CDN as fallback
                            await loadFn(modelPaths.cdn);
                        } catch (cdnError) {
                            throw new Error(`Failed to load ${modelName} from both local and CDN`);
                        }
                    }
                }

                // Load models with progress updates
                const models = [
                    {
                        name: 'Face Detector',
                        load: async (path) => await faceapi.nets.tinyFaceDetector.loadFromUri(path)
                    },
                    {
                        name: 'Landmark Detector',
                        load: async (path) => await faceapi.nets.faceLandmark68Net.loadFromUri(path)
                    },
                    {
                        name: 'Recognition Model',
                        load: async (path) => await faceapi.nets.faceRecognitionNet.loadFromUri(path)
                    }
                ];

                for (const model of models) {
                    document.getElementById('status-message').innerHTML = 
                        `<i class="fas fa-spinner fa-spin"></i> Loading ${model.name}...`;
                    await loadModelWithFallback(model.name, model.load);
                    console.log(`${model.name} loaded successfully`);
                }

                document.getElementById('status-message').innerHTML = 
                    '<i class="fas fa-check-circle"></i> Ready to start verification';
                
                await startCamera();
            } catch (error) {
                console.error('Model loading error:', error);
                    document.getElementById('status-message').innerHTML = 
                    `<i class="fas fa-exclamation-circle"></i> Error loading models. Please check your internet connection and refresh the page.`;
                document.getElementById('retry-button').style.display = 'block';
            }
        }

        // Helper function to load scripts dynamically
        function loadScript(src) {
            return new Promise((resolve, reject) => {
                const script = document.createElement('script');
                script.src = src;
                script.onload = resolve;
                script.onerror = reject;
                document.head.appendChild(script);
            });
        }

        async function startCamera() {
            try {
                // Check if running in a mobile browser
                const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
                console.log('Device type:', isMobile ? 'Mobile' : 'Desktop');

                // Basic feature detection
                if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                    throw new Error('Camera API is not supported in this browser');
                }

                // Start with basic constraints for mobile
                let constraints = {
                    audio: false,
                    video: isMobile ? true : {
                        width: { ideal: 1280 },
                        height: { ideal: 720 },
                        facingMode: 'user'
                    }
                };

                console.log('Requesting camera with constraints:', constraints);
                
                try {
                    stream = await navigator.mediaDevices.getUserMedia(constraints);
                    console.log('Camera access granted');
                } catch (initialError) {
                    console.error('Initial camera request failed:', initialError);
                    
                    // Fallback to even simpler constraints
                    constraints = {
                        audio: false,
                        video: true
                    };
                    
                    console.log('Trying fallback constraints:', constraints);
                    stream = await navigator.mediaDevices.getUserMedia(constraints);
                }

                const video = document.getElementById('video');
                
                // Set video source
                try {
                    video.srcObject = stream;
                    console.log('Video source set successfully');
                } catch (srcError) {
                    console.error('Error setting srcObject:', srcError);
                    try {
                        // Legacy fallback
                        video.src = URL.createObjectURL(stream);
                        console.log('Using legacy URL.createObjectURL');
                    } catch (urlError) {
                        throw new Error('Failed to set video source');
                    }
                }

                // Essential video element setup
                video.setAttribute('playsinline', ''); // Required for iOS
                video.setAttribute('autoplay', '');
                video.setAttribute('muted', '');
                video.style.transform = 'scaleX(-1)';
                
                // Wait for video to be ready
                await new Promise((resolve, reject) => {
                    video.onloadedmetadata = () => {
                        video.play()
                            .then(() => {
                                console.log('Video playback started');
                                resolve();
                            })
                            .catch(playError => {
                                console.error('Video play error:', playError);
                                reject(playError);
                        });
                    };
                    video.onerror = (e) => {
                        console.error('Video element error:', e);
                        reject(new Error('Video element error'));
                    };
                    
                    // Add timeout for video loading
                    setTimeout(() => {
                        reject(new Error('Video loading timeout'));
                    }, 10000); // 10 second timeout
                });

                document.getElementById('status-message').innerHTML = 
                    '<i class="fas fa-info-circle"></i> Please center your face in the frame';
                
                console.log('Starting face detection...');
                detectFace();
                
            } catch (error) {
                console.error('Camera initialization error:', error);
                let errorMessage = '';
                
                if (error.name === 'NotAllowedError' || error.name === 'PermissionDeniedError') {
                    errorMessage = 'Camera access denied. Please grant camera permission and refresh the page.';
                } else if (error.name === 'NotFoundError' || error.name === 'DevicesNotFoundError') {
                    errorMessage = 'No camera found. Please ensure your device has a working camera.';
                } else if (error.name === 'NotReadableError' || error.name === 'TrackStartError') {
                    errorMessage = 'Camera is in use by another application. Please close other apps using the camera.';
                } else if (error.name === 'OverconstrainedError' || error.name === 'ConstraintNotSatisfiedError') {
                    errorMessage = 'Camera not compatible. Please try using a different browser.';
                } else {
                    errorMessage = 'Camera error: ' + error.message;
                }
                
                document.getElementById('status-message').innerHTML = 
                    `<i class="fas fa-exclamation-circle"></i> ${errorMessage}`;
                
                // Show retry button
                document.getElementById('retry-button').style.display = 'block';
            }
        }

        async function detectFace() {
            if (!stream) {
                console.log('No stream available');
                return;
            }
            
            const video = document.getElementById('video');
                const faceBox = document.getElementById('face-box');
                const qualityIndicator = document.getElementById('quality-indicator');
                const statusMessage = document.getElementById('status-message');

            try {
                // Check if video is ready
                if (video.readyState !== 4) {
                    console.log('Video not ready yet');
                    requestAnimationFrame(detectFace);
                    return;
                }

                console.log('Attempting face detection...');
                const detections = await faceapi.detectAllFaces(
                    video, 
                    new faceapi.TinyFaceDetectorOptions({
                        inputSize: 320,
                        scoreThreshold: 0.3
                    })
                ).withFaceLandmarks().withFaceDescriptors();

                if (detections && detections.length > 0) {
                    const detection = detections[0]; // Get the first face
                    const score = Math.round(detection.detection.score * 100);
                    console.log('Face detected with score:', score);
                    
                    const displaySize = { width: video.offsetWidth, height: video.offsetHeight };
                    const resizedDetection = faceapi.resizeResults(detection, displaySize);
                    
                        // Update face box
                    const box = resizedDetection.detection.box;
                    faceBox.style.display = 'block';
                    faceBox.style.left = `${box.x}px`;
                    faceBox.style.top = `${box.y}px`;
                    faceBox.style.width = `${box.width}px`;
                    faceBox.style.height = `${box.height}px`;
                    
                    // Check face quality
                    const quality = checkFaceQuality(resizedDetection);
                    
                    qualityIndicator.textContent = `Accuracy: ${score}%`;
                    qualityIndicator.style.backgroundColor = score >= 95 ? '#00ff00' : '#ff0000';
                    
                    if (quality.isGood && score >= 95) {
                        statusMessage.innerHTML = '<i class="fas fa-check-circle"></i> Face detected, verifying...';

                        if (!isProcessing) {
                            console.log('Starting face verification...');
                            // Create high-quality descriptor
                            const canvas = document.createElement('canvas');
                            canvas.width = video.videoWidth;
                            canvas.height = video.videoHeight;
                            const context = canvas.getContext('2d');
                            context.drawImage(video, 0, 0, canvas.width, canvas.height);

                            // Get high-quality face descriptor
                            const highQualityDetection = await faceapi.detectAllFaces(
                                canvas,
                                new faceapi.TinyFaceDetectorOptions({
                                    inputSize: 320,
                                    scoreThreshold: 0.3
                                })
                            ).withFaceLandmarks().withFaceDescriptors();

                            if (highQualityDetection && highQualityDetection.length > 0) {
                                const descriptor = highQualityDetection[0].descriptor;
                                const normalizedDescriptor = Array.from(descriptor).map(x => parseFloat(x.toFixed(10)));
                                await verifyFace(normalizedDescriptor);
                            }
                        }
                    } else {
                        statusMessage.innerHTML = quality.message;
                    }
                } else {
                    console.log('No face detected');
                    faceBox.style.display = 'none';
                    qualityIndicator.textContent = 'No Face';
                    qualityIndicator.style.backgroundColor = '#ff0000';
                    statusMessage.innerHTML = '<i class="fas fa-search"></i> Looking for face...';
                }
            } catch (error) {
                console.error('Detection error:', error);
                statusMessage.innerHTML = '<i class="fas fa-exclamation-circle"></i> Detection error: ' + error.message;
            }
            
            if (!isProcessing && verificationAttempts < maxAttempts) {
                requestAnimationFrame(detectFace);
            }
        }

        function checkFaceQuality(detection) {
            const { detection: { box, score } } = detection;
            const video = document.getElementById('video');
            
            // Accuracy threshold check (95%)
            const accuracyScore = score * 100;
            const isAccuracyGood = accuracyScore >= 95;
            
            // Size check - ensure face is clearly visible
            const minSize = Math.min(video.offsetWidth, video.offsetHeight) * 0.15;
            const maxSize = Math.min(video.offsetWidth, video.offsetHeight) * 0.9;
            const isSizeOk = box.width >= minSize && box.height >= minSize && 
                             box.width <= maxSize && box.height <= maxSize;
            
            // Overall quality assessment
            const isGood = isAccuracyGood && isSizeOk;
            
            let message = '';
            if (!isAccuracyGood) {
                message = '<i class="fas fa-exclamation-circle"></i> Detection accuracy too low (min 95% required)';
            } else if (!isSizeOk) {
                message = '<i class="fas fa-arrows-alt"></i> Please adjust your distance from the camera';
            } else {
                message = '<i class="fas fa-check-circle"></i> Face detected, verifying...';
            }
            
            return { 
                isGood,
                message,
                accuracyScore: accuracyScore.toFixed(1)
            };
        }

        async function verifyFace(faceDescriptor) {
            if (verificationAttempts >= maxAttempts) {
                document.getElementById('status-message').innerHTML = 
                    '<i class="fas fa-exclamation-circle"></i> Maximum verification attempts reached';
                document.getElementById('retry-button').style.display = 'block';
                stopCamera();
                return;
            }

            isProcessing = true;
            verificationAttempts++;
            const statusMessage = document.getElementById('status-message');

            try {
                console.log('Sending verification request...');
                const formData = new FormData();
                formData.append('face_descriptor', JSON.stringify(faceDescriptor));
                
                const response = await fetch(window.location.href, {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();
                console.log('Verification result:', result);

                if (result.success) {
                    statusMessage.innerHTML = 
                        '<i class="fas fa-check-circle"></i> Face verified successfully! Redirecting...';
                    stopCamera();
                    setTimeout(() => {
                        window.location.href = result.redirect_url;
                    }, 1000);
                } else {
                    throw new Error(result.message || 'Verification failed');
                }
            } catch (error) {
                console.error('Verification error:', error);
                statusMessage.innerHTML = 
                    `<i class="fas fa-exclamation-circle"></i> ${error.message} (Attempt ${verificationAttempts}/${maxAttempts})`;
                
                if (verificationAttempts >= maxAttempts) {
                    document.getElementById('retry-button').style.display = 'block';
                    stopCamera();
                }
            } finally {
                isProcessing = false;
            }
        }

        function stopCamera() {
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
                stream = null;
            }
        }

        function retryVerification() {
            verificationAttempts = 0;
            document.getElementById('retry-button').style.display = 'none';
            document.getElementById('status-message').innerHTML = 
                '<i class="fas fa-spinner fa-spin"></i> Restarting verification...';
            startCamera();
        }

        // Initialize when page loads
        document.addEventListener('DOMContentLoaded', async () => {
            if (await checkCompatibility()) {
                await loadModels();
            }
        });

        // Cleanup on page unload
        window.addEventListener('beforeunload', async () => {
            stopCamera();
            try {
                await tf.disposeVariables();
                await tf.engine().endScope();
                await tf.engine().disposeAll();
            } catch (e) {
                console.error('Cleanup error:', e);
            }
        });
    </script>
</body>
</html>