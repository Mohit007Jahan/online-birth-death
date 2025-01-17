<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'config.php';
require_once 'dbconfig.php';

// Add debugging
error_log("Starting face_authentication.php");
error_log("Session data at start: " . print_r($_SESSION, true));

// Check if user has completed Stage 4
if (!isset($_SESSION['registration_user_id']) || $_SESSION['registration_stage'] != 4) {
    error_log("Session validation failed. Redirecting to register.php");
    header("Location: register.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        error_log("Processing POST request");
        error_log("Received POST data: " . print_r($_POST, true));

        // Validate the face data
        if (empty($_POST['face_images']) || empty($_POST['face_embeddings'])) {
            throw new Exception("Face data is incomplete");
        }

        $faceImages = json_decode($_POST['face_images'], true);
        $faceEmbeddings = json_decode($_POST['face_embeddings'], true);

        if (!$faceImages || !$faceEmbeddings || !isset($faceImages[0]) || !isset($faceEmbeddings[0])) {
            throw new Exception("Face data is invalid");
        }

        // Begin transaction
        $conn->beginTransaction();
        error_log("Starting transaction");

        // Store face verification data
        $stmt = $conn->prepare("INSERT INTO tblfaceverification (user_id, face_image, face_embedding, verification_date, verification_status) 
                              VALUES (?, ?, ?, NOW(), 'verified')");
        if (!$stmt->execute([$_SESSION['registration_user_id'], $faceImages[0], json_encode($faceEmbeddings[0])])) {
            throw new Exception("Failed to save face verification data");
        }

        error_log("Face verification data saved");

        // Update user status to active
        $stmt = $conn->prepare("UPDATE tbluser SET status = 'active' WHERE ID = ?");
        if (!$stmt->execute([$_SESSION['registration_user_id']])) {
            throw new Exception("Failed to activate user account");
        }

        error_log("User status updated to active");

        // Commit transaction
        $conn->commit();
        error_log("Transaction committed");

        // Clear registration session data
        unset($_SESSION['registration_user_id']);
        unset($_SESSION['registration_stage']);
        unset($_SESSION['registration_phone']);

        error_log("Session data cleared");

        // Set success message in session
        $_SESSION['registration_success'] = true;

        // Redirect to login page
        header("Location: user_login.php?registration=success");
        exit();

    } catch (Exception $e) {
        error_log("Error: " . $e->getMessage());
        if ($conn->inTransaction()) {
            $conn->rollBack();
            error_log("Transaction rolled back");
        }
        $error = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Face Authentication</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- TensorFlow.js -->
    <script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs@3.11.0/dist/tf.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs-backend-webgl@3.11.0/dist/tf-backend-webgl.min.js"></script>
    <!-- Face API -->
    <script src="https://cdn.jsdelivr.net/npm/@vladmandic/face-api@1.7.12/dist/face-api.js"></script>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
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
            display: flex;
            flex-direction: column;
            background-color: #b5dcb3;
            border-radius: 20px;
            box-shadow: 9px 9px 16px #88b489, -9px -9px 16px #e2ffe2;
            max-width: 900px;
            width: 100%;
            padding: 30px;
            position: relative;
            transition: transform 0.3s ease-in-out;
        }
        .container:hover {
            transform: scale(1.02);
        }
        .logo {
            position: absolute;
            top: 20px;
            left: 20px;
            width: 80px;
            height: auto;
        }
        h1 {
            text-align: center;
            color: #2c662c;
            font-size: 2.5rem;
            margin-bottom: 30px;
        }
        #video-container {
            position: relative;
            width: 100%;
            max-width: 640px;
            margin: 0 auto;
            background: #b5dcb3;
            border-radius: 20px;
            padding: 15px;
            box-shadow: inset 6px 6px 10px #88b489, inset -6px -6px 10px #e2ffe2;
        }
        #video {
            width: 100%;
            border-radius: 12px;
            background-color: #000;
        }
        #canvas {
            display: none;
            width: 100%;
            border-radius: 12px;
        }
        .action-btn {
            display: block;
            width: 100%;
            padding: 14px 28px;
            background-color: #228b22;
            color: white;
            font-size: 18px;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            margin-top: 20px;
            transition: all 0.3s ease;
        }
        .action-btn:disabled {
            background-color: #cccccc;
            cursor: not-allowed;
            opacity: 0.7;
        }
        .action-btn:not(:disabled):hover {
            background-color: #196619;
            transform: translateY(-2px);
        }
        .alert {
            background: #b5dcb3;
            border: none;
            color: #721c24;
            box-shadow: inset 6px 6px 10px #88b489, inset -6px -6px 10px #e2ffe2;
            border-radius: 12px;
            padding: 15px;
            margin-bottom: 20px;
        }
        .instructions {
            background: #b5dcb3;
            border-radius: 12px;
            padding: 15px;
            box-shadow: inset 6px 6px 10px #88b489, inset -6px -6px 10px #e2ffe2;
            color: #2c662c;
            margin: 20px 0;
            font-size: 16px;
            line-height: 1.5;
        }
        .camera-error {
            text-align: center;
            color: #721c24;
            margin: 20px 0;
            padding: 20px;
            background: #b5dcb3;
            border-radius: 12px;
            box-shadow: inset 6px 6px 10px #88b489, inset -6px -6px 10px #e2ffe2;
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
        .footer {
            margin-top: 20px;
            text-align: center;
            color: #666;
            font-size: 0.9rem;
        }
        @media (max-width: 768px) {
            h1 {
                font-size: 2rem;
            }
            .action-btn {
                font-size: 1rem;
                padding: 12px;
            }
            .instructions {
                font-size: 14px;
            }
        }
        @media (max-width: 500px) {
            .container {
                padding: 15px;
            }
            .logo {
                width: 60px;
            }
            h1 {
                font-size: 1.8rem;
            }
            .action-btn {
                font-size: 0.9rem;
                padding: 10px;
            }
        }
        .capture-progress {
            display: flex;
            justify-content: space-between;
            margin: 20px 0;
            padding: 15px;
            background: #b5dcb3;
            border-radius: 12px;
            box-shadow: inset 6px 6px 10px #88b489, inset -6px -6px 10px #e2ffe2;
        }
        .capture-step {
            text-align: center;
            padding: 10px;
            border-radius: 8px;
            color: #2c662c;
            position: relative;
        }
        .capture-step.active {
            background: #228b22;
            color: white;
            box-shadow: 6px 6px 10px #88b489, -6px -6px 10px #e2ffe2;
        }
        .capture-step.completed {
            background: #196619;
            color: white;
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
        .guide-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            border: 2px dashed #00ff00;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-shadow: 1px 1px 2px black;
            font-size: 18px;
            pointer-events: none;
        }
    </style>
</head>
<body>
<div class="container">
        <div class="verification-container">
            <div class="header">
                <img src="logo.png" alt="Logo">
                <h1>Face Authentication Setup</h1>
            </div>

    <?php if (isset($error)): ?>
            <div class="alert">
        <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
    </div>
    <?php endif; ?>

    <div class="instructions">
                <i class="fas fa-info-circle"></i> Please look directly at the camera with good lighting for a clear frontal face capture.
            </div>

            <div class="capture-progress">
                <div class="capture-step active" id="step1">
                    <i class="fas fa-user"></i>
                    <div>Front View</div>
                </div>
                <div class="capture-step" id="step2">
                    <i class="fas fa-check-circle"></i>
                    <div>Complete</div>
                </div>
            </div>

            <div class="status-message" id="status-message">
                <i class="fas fa-spinner fa-spin"></i> Loading face detection models...
    </div>

    <div id="video-container">
        <video id="video" playsinline autoplay></video>
        <canvas id="canvas"></canvas>
        <div id="face-box"></div>
        <div id="quality-indicator" class="quality-indicator"></div>
        <div id="guide-overlay" class="guide-overlay"></div>
        <button id="capture-btn" class="action-btn" onclick="captureImage()">
            <i class="fas fa-camera"></i> Capture Image
        </button>
    </div>
    
            <form id="auth-form" method="POST" style="display: none;">
                <input type="hidden" name="face_embeddings" id="face-embeddings">
                <input type="hidden" name="face_images" id="face-images">
                <button type="submit" class="action-btn" id="submit-btn" disabled>
                    <i class="fas fa-check-circle"></i> Complete Setup
        </button>
    </form>
        </div>
</div>

<script>
    let model;
    let stream;
    let isCapturing = false;
    let capturedImage = null;
    let capturedEmbedding = null;

    async function loadModels() {
        try {
            console.log('Starting model loading...');
            document.getElementById('status-message').innerHTML = 
                '<i class="fas fa-spinner fa-spin"></i> Initializing TensorFlow.js...';

            // Verify TensorFlow.js is loaded
            if (!tf) {
                throw new Error('TensorFlow.js not loaded');
            }

            // Verify face-api.js is loaded
            if (!faceapi) {
                throw new Error('face-api.js not loaded');
            }

            // Initialize TensorFlow properly with fallback options
            try {
                console.log('Initializing TensorFlow backend...');
                
                // Configure TensorFlow with stable settings before backend initialization
                tf.env().set('WEBGL_VERSION', 1);
                tf.env().set('WEBGL_FORCE_F16_TEXTURES', false);
                tf.env().set('WEBGL_PACK', false);
                tf.env().set('WEBGL_CPU_FORWARD', true);
                tf.env().set('WEBGL_DELETE_TEXTURE_THRESHOLD', 0);
                tf.env().set('CHECK_COMPUTATION_FOR_ERRORS', false);
                tf.env().set('WEBGL_USE_SHAPES_UNIFORMS', false);
                
                // Try WebGL first
                await tf.setBackend('webgl');
                await tf.ready();
                
                console.log('Using TensorFlow backend:', tf.getBackend());
            } catch (e) {
                console.warn('WebGL initialization failed, falling back to CPU:', e);
                // Force CPU backend if WebGL fails
                await tf.setBackend('cpu');
                await tf.ready();
            }

            // Verify models directory exists
            const modelPath = './models';
            console.log('Loading models from:', modelPath);
            
            // Load and verify each model
            const modelLoadingTasks = [
                {
                    name: 'Face Detector',
                    load: () => faceapi.nets.tinyFaceDetector.loadFromUri(modelPath),
                    verify: () => faceapi.nets.tinyFaceDetector.isLoaded
                },
                {
                    name: 'Landmark Detector',
                    load: () => faceapi.nets.faceLandmark68Net.loadFromUri(modelPath),
                    verify: () => faceapi.nets.faceLandmark68Net.isLoaded
                },
                {
                    name: 'Recognition Model',
                    load: () => faceapi.nets.faceRecognitionNet.loadFromUri(modelPath),
                    verify: () => faceapi.nets.faceRecognitionNet.isLoaded
                }
            ];

            for (const task of modelLoadingTasks) {
                document.getElementById('status-message').innerHTML = 
                    `<i class="fas fa-spinner fa-spin"></i> Loading ${task.name}...`;
                    
                try {
                    await task.load();
                    if (!task.verify()) {
                        throw new Error(`${task.name} failed to load properly`);
                    }
                    console.log(`${task.name} loaded successfully`);
                } catch (e) {
                    throw new Error(`Failed to load ${task.name}: ${e.message}`);
                }
            }

            console.log('All models loaded and verified');
            document.getElementById('status-message').innerHTML = 
                '<i class="fas fa-check-circle"></i> All models loaded successfully';
            
            await startCamera();
        } catch (error) {
            console.error('Model loading error:', error);
            document.getElementById('status-message').innerHTML = 
                `<i class="fas fa-exclamation-circle"></i> Error: ${error.message}`;
        }
    }

    async function startCamera() {
        try {
            const constraints = {
                video: { 
                    width: { min: 640, ideal: 1280, max: 1920 },
                    height: { min: 480, ideal: 720, max: 1080 },
                    facingMode: 'user',
                    frameRate: { ideal: 24 }
                },
                audio: false
            };

            console.log('Requesting camera access...');
            stream = await navigator.mediaDevices.getUserMedia(constraints);
            console.log('Camera access granted');
            
                const video = document.getElementById('video');
                video.srcObject = stream;
            
            // Wait for video to be ready
            await new Promise((resolve) => {
                video.onloadedmetadata = () => {
                    video.play().then(() => {
                        console.log('Video playback started');
                        resolve();
                    }).catch(error => {
                        console.error('Video playback error:', error);
                        throw error;
                    });
                };
            });
            
            document.getElementById('status-message').innerHTML = 
                '<i class="fas fa-info-circle"></i> Please center your face in the frame';
            
            console.log('Starting face detection...');
                detectFace();
            } catch (error) {
            console.error('Camera initialization error:', error);
                document.getElementById('status-message').innerHTML = 
                `<i class="fas fa-exclamation-circle"></i> Camera error: ${error.message}. Please ensure camera permissions are granted.`;
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
                console.log('Face detected, processing landmarks and descriptor...');
                
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
                document.getElementById('capture-btn').disabled = !quality.isGood;
                
                const score = Math.round(detection.detection.score * 100);
                qualityIndicator.textContent = `Accuracy: ${score}%`;
                qualityIndicator.style.backgroundColor = score >= 95 ? '#00ff00' : '#ff0000';
                
                document.getElementById('status-message').innerHTML = quality.message;
                console.log('Face quality:', quality);
                    } else {
                console.log('No face detected');
                    faceBox.style.display = 'none';
                qualityIndicator.textContent = 'No Face';
                    qualityIndicator.style.backgroundColor = '#ff0000';
                document.getElementById('capture-btn').disabled = true;
                document.getElementById('status-message').innerHTML = 
                    '<i class="fas fa-search"></i> Looking for face...';
            }
        } catch (error) {
            console.error('Detection error:', error);
            document.getElementById('status-message').innerHTML = 
                `<i class="fas fa-exclamation-circle"></i> Detection error: ${error.message}`;
        }
        
                if (!isCapturing) {
                    requestAnimationFrame(detectFace);
            }
        }

        function checkFaceQuality(detection) {
            const { landmarks, detection: { box, score } } = detection;
            const video = document.getElementById('video');
            
            // Accuracy threshold check (95%)
            const accuracyScore = score * 100;
            const isAccuracyGood = accuracyScore >= 95;
            
            // Size check - more lenient size requirements
            const minSize = Math.min(video.offsetWidth, video.offsetHeight) * 0.15; // Reduced from 0.2
            const maxSize = Math.min(video.offsetWidth, video.offsetHeight) * 0.9;  // Increased from 0.8
            const isSizeOk = box.width >= minSize && box.height >= minSize && 
                             box.width <= maxSize && box.height <= maxSize;
            
            // Position check - more lenient centering
            const centerX = video.offsetWidth / 2;
            const centerY = video.offsetHeight / 2;
            const faceX = box.x + (box.width / 2);
            const faceY = box.y + (box.height / 2);
            const maxOffset = video.offsetWidth * 0.3; // Increased from 0.15 for more flexibility
            const isCentered = Math.abs(faceX - centerX) < maxOffset && 
                              Math.abs(faceY - centerY) < maxOffset;
            
            // Overall quality assessment - only check accuracy and basic size
            const isGood = isAccuracyGood && isSizeOk;
            
            let message = '';
            if (!isAccuracyGood) {
                message = '<i class="fas fa-exclamation-circle"></i> Detection accuracy too low (min 95% required)';
            } else if (!isSizeOk) {
                message = '<i class="fas fa-arrows-alt"></i> Please adjust your distance from the camera';
            } else {
                message = '<i class="fas fa-check-circle"></i> Ready to capture!';
            }
            
            return { 
                isGood,
                message,
                accuracyScore: accuracyScore.toFixed(1)
            };
        }

        function checkFaceRotation(landmarks) {
            const leftEye = landmarks.getLeftEye();
            const rightEye = landmarks.getRightEye();
            const nose = landmarks.getNose();
            const mouth = landmarks.getMouth();
            
            // Calculate angles
            const eyeDY = Math.abs(leftEye[0].y - rightEye[0].y);
            const eyeDX = Math.abs(leftEye[0].x - rightEye[0].x);
            const rollAngle = Math.atan2(eyeDY, eyeDX) * (180 / Math.PI);
            
            const eyeDistance = eyeDX;
            const normalizedEyeDistance = eyeDistance / landmarks.positions.length;
            
            const noseTop = nose[0];
            const mouthCenter = mouth[0];
            const verticalRatio = Math.abs(mouthCenter.y - noseTop.y) / landmarks.positions.length;
            
            const isRollOk = rollAngle < 15;
            const isYawOk = normalizedEyeDistance > 0.08 && normalizedEyeDistance < 0.35;
            const isPitchOk = verticalRatio > 0.08 && verticalRatio < 0.35;
            
            let message = '';
            if (!isRollOk) {
                message = '<i class="fas fa-redo"></i> Keep your head level';
            } else if (!isYawOk) {
                message = '<i class="fas fa-redo"></i> Look straight at the camera';
            } else if (!isPitchOk) {
                message = '<i class="fas fa-redo"></i> Keep your head straight';
            }
            
            return {
                isGood: isRollOk && isYawOk && isPitchOk,
                message
            };
            }

            async function captureImage() {
            if (isCapturing) return;
            isCapturing = true;
            
            const video = document.getElementById('video');
            const canvas = document.getElementById('canvas');
            const context = canvas.getContext('2d');
            const statusMessage = document.getElementById('status-message');
            
            try {
                console.log('Starting face capture process...');
                statusMessage.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing face capture...';
                
                // Set canvas size to match video dimensions exactly
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                
                // Draw the current frame to canvas
                context.drawImage(video, 0, 0);
                
                // Detect face in the captured frame
                console.log('Detecting face in captured frame...');
                const detection = await faceapi.detectSingleFace(
                    canvas,
                    new faceapi.TinyFaceDetectorOptions({
                        inputSize: 320,
                        scoreThreshold: 0.3
                    })
                ).withFaceLandmarks().withFaceDescriptor();

                if (!detection) {
                    throw new Error('No face detected in capture. Please try again.');
                }

                const score = detection.detection.score * 100;
                console.log('Face detected with score:', score);

                // Check if accuracy meets the threshold (95%)
                if (score < 95) {
                    throw new Error('Face detection accuracy too low. Please ensure good lighting and face position.');
                }

                // Get image data with high quality
                const imageData = canvas.toDataURL('image/jpeg', 1.0);
                console.log('Image captured successfully');

                // Normalize descriptor values with higher precision
                const normalizedDescriptor = Array.from(detection.descriptor).map(x => parseFloat(x.toFixed(10)));
                console.log('Face descriptor generated:', normalizedDescriptor);

                // Update form data
                document.getElementById('face-images').value = JSON.stringify([imageData]);
                document.getElementById('face-embeddings').value = JSON.stringify([normalizedDescriptor]);
                
                // Update UI to show success
                document.getElementById('step1').classList.remove('active');
                document.getElementById('step1').classList.add('completed');
                document.getElementById('step2').classList.add('active');
                
                statusMessage.innerHTML = '<i class="fas fa-check-circle"></i> Face captured successfully! Click Complete Setup to continue.';
                
                // Show the form and enable submission
                document.getElementById('auth-form').style.display = 'block';
                document.getElementById('submit-btn').disabled = false;
                document.getElementById('capture-btn').style.display = 'none';
                
                // Stop video stream
                if (stream) {
                    stream.getTracks().forEach(track => track.stop());
                }
                
                console.log('Capture process completed successfully');
                
            } catch (error) {
                console.error('Capture error:', error);
                statusMessage.innerHTML = `<i class="fas fa-exclamation-circle"></i> ${error.message}`;
                isCapturing = false;
                document.getElementById('capture-btn').disabled = false;
            }
        }

        // Add form submission handler
        document.getElementById('auth-form').addEventListener('submit', function(e) {
            const images = document.getElementById('face-images').value;
            const embeddings = document.getElementById('face-embeddings').value;
            
            if (!images || !embeddings) {
                e.preventDefault();
                document.getElementById('status-message').innerHTML = 
                    '<i class="fas fa-exclamation-circle"></i> Face data is missing. Please try again.';
                return false;
            }
        });

        // Initialize
        document.addEventListener('DOMContentLoaded', loadModels);

        // Cleanup
        window.addEventListener('beforeunload', async () => {
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
        }
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
