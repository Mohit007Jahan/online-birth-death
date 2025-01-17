<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'config.php';
require_once 'dbconfig.php';

// Add debugging
error_log("Starting register4.php");
error_log("Session data at start: " . print_r($_SESSION, true));

// Check if user has completed Stage 3
if (!isset($_SESSION['registration_user_id']) || $_SESSION['registration_stage'] != 3 || !isset($_SESSION['registration_phone'])) {
    error_log("Session validation failed. Redirecting to register.php");
    header("Location: register.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        error_log("Processing POST request");
        error_log("POST data: " . print_r($_POST, true));

        // Sanitize and validate verification code
        $verificationCode = trim(strip_tags($_POST['verification-code']));

        // Validate code format (6 digits)
        if (!preg_match('/^\d{6}$/', $verificationCode)) {
            throw new Exception("Invalid verification code format. Please enter 6 digits.");
        }

        // Begin transaction
        $conn->beginTransaction();
        error_log("Starting transaction");

        // Get the stored OTP
        $stmt = $conn->prepare("SELECT otp, verification_attempts FROM tblphoneverification 
                               WHERE user_id = ? AND phone_number = ? AND verified = 0");
        $stmt->execute([$_SESSION['registration_user_id'], $_SESSION['registration_phone']]);
        $verification = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$verification) {
            throw new Exception("Verification record not found");
        }

        error_log("Stored OTP: " . $verification['otp'] . ", Attempts: " . $verification['verification_attempts']);

        // Check attempts
        if ($verification['verification_attempts'] >= 3) {
            throw new Exception("Maximum verification attempts exceeded. Please request a new code.");
        }

        // Update attempts
        $stmt = $conn->prepare("UPDATE tblphoneverification SET verification_attempts = verification_attempts + 1 
                               WHERE user_id = ? AND phone_number = ?");
        $stmt->execute([$_SESSION['registration_user_id'], $_SESSION['registration_phone']]);

        // Verify OTP
        if ($verificationCode !== $verification['otp']) {
            throw new Exception("Invalid verification code. Please try again.");
        }

        // Mark phone as verified
        $stmt = $conn->prepare("UPDATE tblphoneverification SET verified = 1 
                               WHERE user_id = ? AND phone_number = ?");
        if (!$stmt->execute([$_SESSION['registration_user_id'], $_SESSION['registration_phone']])) {
            throw new Exception("Failed to verify phone number");
        }

        error_log("Phone verification successful");

        // Update registration stage
        $_SESSION['registration_stage'] = 4;
        error_log("Updated registration stage to 4");

        // Commit transaction
        $conn->commit();
        error_log("Transaction committed");

        // Redirect to face authentication
        header("Location: face_authentication.php");
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
    <title>Phone Number Verification</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
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
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            color: #2c662c;
            font-weight: bold;
            display: block;
            margin-bottom: 8px;
        }
        .input-group {
            background: #b5dcb3;
            border-radius: 12px;
            box-shadow: inset 6px 6px 10px #88b489, inset -6px -6px 10px #e2ffe2;
            padding: 5px;
        }
        .input-group-prepend .input-group-text {
            background: transparent;
            border: none;
            color: #2c662c;
        }
        .form-control {
            border: none;
            background: transparent;
            color: #2c662c;
            padding: 12px;
            text-align: center;
            letter-spacing: 4px;
            font-size: 18px;
        }
        .form-control:focus {
            box-shadow: none;
            background: transparent;
        }
        .verify-btn {
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
        }
        .verify-btn:hover {
            background-color: #196619;
            box-shadow: 9px 9px 16px #88b489, -9px -9px 16px #e2ffe2;
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
        .info-text {
            background: #b5dcb3;
            border-radius: 12px;
            padding: 15px;
            box-shadow: inset 6px 6px 10px #88b489, inset -6px -6px 10px #e2ffe2;
            color: #2c662c;
            margin: 20px 0;
            font-size: 16px;
            line-height: 1.5;
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
            .verify-btn {
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
            .verify-btn {
                font-size: 0.9rem;
                padding: 10px;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <img src="logo.png" alt="Logo" class="logo">
    <h1>Phone Number Verification</h1>

    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
        <?php if (isset($error)): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
        </div>
        <?php endif; ?>

        <div class="form-group">
            <label for="verification-code">Enter Verification Code</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fas fa-key"></i></span>
                </div>
                <input type="text" class="form-control" id="verification-code" name="verification-code" 
                       placeholder="Enter 6-digit code" required pattern="\d{6}" maxlength="6">
            </div>
        </div>

        <div class="info-text">
            A text message with a verification code has been sent to your registered phone number. 
            Please check your SMS and enter the code above to verify your phone number.
        </div>

        <button type="submit" class="verify-btn">Verify</button>
    </form>

    <div class="footer">
        <p>&copy; 2024 Online Registration System</p>
    </div>
</div>

</body>
</html>
