<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'config.php';
require_once 'dbconfig.php';

// Add debugging
error_log("Starting register3.php");
error_log("Session data at start: " . print_r($_SESSION, true));

// Check if user has completed Stage 2
if (!isset($_SESSION['registration_user_id']) || $_SESSION['registration_stage'] != 2) {
    error_log("Session validation failed in register3.php. User ID: " . isset($_SESSION['registration_user_id']) . ", Stage: " . ($_SESSION['registration_stage'] ?? 'not set'));
    header("Location: register.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        error_log("Processing POST request in register3.php");
        error_log("POST data: " . print_r($_POST, true));

        // Sanitize and validate phone number
        $phoneNumber = trim(strip_tags($_POST['phone-number']));

        // Validate phone number format (assuming Bangladesh format: +880XXXXXXXXXX)
        if (!preg_match('/^\+?880\d{10}$/', $phoneNumber)) {
            throw new Exception("Invalid phone number format. Please use Bangladesh format: +880XXXXXXXXXX");
        }

        // Generate OTP
        $otp = sprintf("%06d", mt_rand(100000, 999999));
        error_log("Generated OTP: " . $otp);

        // Begin transaction
        $conn->beginTransaction();
        error_log("Starting database transaction");

        // Store phone number and OTP
        $_SESSION['registration_phone'] = $phoneNumber;
        $_SESSION['registration_stage'] = 3;
        error_log("Updated session with phone and stage");
        error_log("Session data after update: " . print_r($_SESSION, true));

        // Insert phone verification record
        $stmt = $conn->prepare("INSERT INTO tblphoneverification (user_id, phone_number, otp, verification_attempts) 
                               VALUES (?, ?, ?, 0)");
        
        $params = [$_SESSION['registration_user_id'], $phoneNumber, $otp];
        error_log("Executing SQL with params: " . print_r($params, true));
        
        if (!$stmt->execute($params)) {
            $error = $stmt->errorInfo();
            error_log("Database error: " . print_r($error, true));
            throw new Exception("Failed to save phone verification: " . $error[2]);
        }

        error_log("Database insert successful");

        // Commit transaction
        $conn->commit();
        error_log("Transaction committed");

        // Redirect to OTP verification
        header("Location: register4.php");
        exit();

    } catch (Exception $e) {
        error_log("Error in register3.php: " . $e->getMessage());
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
    <title>Account Registration</title>
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
            transition: transform 0.3s ease-in-out;
        }
        .container:hover {
            transform: scale(1.02); /* Scale up on hover */
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
            color: #2c662c; /* Darker green */
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
        }
        .form-control:focus {
            box-shadow: none;
            background: transparent;
        }
        .next-btn {
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
        .next-btn:hover {
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

    <!-- Phone Number Setup Form -->
    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
        <?php if (isset($error)): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
        </div>
        <?php endif; ?>

        <div class="form-group">
            <label for="phone-number">Enter Phone Number</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fas fa-phone"></i></span>
                </div>
                <input type="text" class="form-control" id="phone-number" name="phone-number" 
                       placeholder="+880XXXXXXXXXX" required>
            </div>
        </div>

        <div class="info-text">
            Enter a phone number to use for security purposes. This phone number can be used to 
            help verify it's really you signing into your account.
        </div>

        <!-- Submit Button -->
        <button type="submit" class="next-btn">Send</button>
    </form>

    <!-- Footer Information -->
    <div class="footer">
        <p>&copy; 2024 Online Registration System</p>
    </div>
</div>

</body>
</html>
