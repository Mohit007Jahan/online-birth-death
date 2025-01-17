<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'config.php';
require_once 'dbconfig.php';

// Check if user has completed Stage 4
if (!isset($_SESSION['registration_user_id']) || $_SESSION['registration_stage'] != 4 || !isset($_SESSION['registration_phone'])) {
    header("Location: register.php");
    exit();
}

try {
    // Verify phone verification status
    $stmt = $conn->prepare("SELECT verified FROM tblphoneverification 
                           WHERE user_id = ? AND phone_number = ? AND verified = 1");
    $stmt->execute([$_SESSION['registration_user_id'], $_SESSION['registration_phone']]);
    
    if (!$stmt->fetch()) {
        header("Location: register4.php");
        exit();
    }

    // Update registration stage
    $_SESSION['registration_stage'] = 5;

    // Get the phone number for display (masked except last 4 digits)
    $phone = $_SESSION['registration_phone'];
    $maskedPhone = substr($phone, 0, -4) . str_repeat('*', 4);

} catch (Exception $e) {
    $error = $e->getMessage();
    header("Location: register4.php");
    exit();
}

// Rest of the HTML code remains the same...
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verification Success</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Arial', sans-serif;
            background-color: #b5dcb3; /* Brighter green background */
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        .container {
            display: flex;
            flex-direction: column;
            background-color: #b5dcb3; /* Neomorphic background color */
            border-radius: 20px;
            box-shadow: 9px 9px 16px #88b489, -9px -9px 16px #e2ffe2; /* Neomorphism effect */
            max-width: 900px;
            width: 100%;
            padding: 30px;
            position: relative;
            text-align: center;
            transition: transform 0.3s ease-in-out;
        }
        .container:hover {
            transform: scale(1.05); /* Scale up on hover */
        }
        .logo {
            position: absolute;
            top: 20px;
            left: 20px;
            width: 80px;
            height: auto;
        }
        h1 {
            color: #2c662c; /* Darker green */
            font-size: 2.5rem;
            margin-bottom: 20px;
        }
        .success-message {
            color: #2c662c; /* Darker green for success message */
            font-size: 24px;
            margin: 20px 0;
        }
        .next-btn {
            width: 200px;
            padding: 15px;
            background-color: #228b22; /* Vibrant green button */
            color: white;
            font-size: 1.2rem;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s;
            margin-top: 30px;
        }
        .next-btn:hover {
            background-color: #196619; /* Darker vibrant green on hover */
        }
        @media (max-width: 768px) {
            h1 {
                font-size: 2rem;
            }
            .next-btn {
                font-size: 1rem;
                padding: 12px;
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
            .next-btn {
                font-size: 0.9rem;
                padding: 10px;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <!-- Logo at the top-left corner -->
    <img src="logo.png" alt="Logo" class="logo">

    <!-- Page Title -->
    <h1>Account Registration</h1>

    <!-- Success Message -->
    <div class="success-message">
        That's it! Your phone number <?php echo htmlspecialchars($maskedPhone); ?> was successfully verified.
    </div>

    <!-- Next Button -->
    <button class="next-btn" onclick="location.href='face_authentication.php';">Next</button>
</div>

</body>
</html>
