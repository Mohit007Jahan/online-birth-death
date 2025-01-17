<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'config.php';
require_once 'dbconfig.php';

// Add debugging
error_log("Starting register.php");
error_log("Session data at start: " . print_r($_SESSION, true));

// Initialize variables
$error = '';
$success = '';
$formData = [];

// Initialize CSRF token if not exists
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        error_log("Processing POST request in register.php");
        error_log("POST data: " . print_r($_POST, true));

        // Sanitize and validate inputs
        $firstName = trim(strip_tags($_POST['first-name'] ?? ''));
        $lastName = trim(strip_tags($_POST['last-name'] ?? ''));
        $email = trim(strip_tags($_POST['email'] ?? ''));
        $mobile = trim(strip_tags($_POST['mobile'] ?? ''));
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm-password'] ?? '';

        // Store form data in case of error
        $formData = [
            'first-name' => $firstName,
            'last-name' => $lastName,
            'email' => $email,
            'mobile' => $mobile
        ];

        error_log("Sanitized inputs: " . print_r($formData, true));

        // Validate inputs
        if (empty($firstName) || empty($lastName) || empty($email) || empty($mobile) || empty($password) || empty($confirmPassword)) {
            throw new Exception("All fields are required");
        }

        // Validate name format
        if (!preg_match('/^[A-Za-z\s]{2,50}$/', $firstName) || !preg_match('/^[A-Za-z\s]{2,50}$/', $lastName)) {
            throw new Exception("Names should only contain letters and be between 2-50 characters");
        }

        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid email format");
        }

        // Check if email already exists
        $stmt = $conn->prepare("SELECT COUNT(*) FROM tbluser WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetchColumn() > 0) {
            throw new Exception("Email already registered");
        }

        // Check if mobile number already exists
        $stmt = $conn->prepare("SELECT COUNT(*) FROM tbluser WHERE mobilenumber = ?");
        $stmt->execute([$mobile]);
        if ($stmt->fetchColumn() > 0) {
            throw new Exception("Mobile number already registered");
        }

        // Validate mobile number format (10 digits)
        if (!preg_match('/^[0-9]{10}$/', $mobile)) {
            throw new Exception("Invalid mobile number format. Please enter 10 digits");
        }

        // Check if passwords match
        if ($password !== $confirmPassword) {
            throw new Exception("Passwords do not match");
        }

        // Begin transaction
        $conn->beginTransaction();
        error_log("Starting database transaction");

        // Insert basic user information
        $stmt = $conn->prepare("INSERT INTO tbluser (firstname, lastname, email, mobilenumber, password, status) 
                               VALUES (?, ?, ?, ?, ?, 'pending')");
        
        $params = [$firstName, $lastName, $email, $mobile, $password];
        error_log("Executing SQL with params: " . print_r($params, true));
        
        if (!$stmt->execute($params)) {
            $error = $stmt->errorInfo();
            error_log("Database error: " . print_r($error, true));
            throw new Exception("Registration failed: " . $error[2]);
        }

        error_log("Database insert successful");

        // Get the user ID for the next stages
        $userId = $conn->lastInsertId();
        error_log("New user ID: " . $userId);

        // Store user ID in session for next stages
        $_SESSION['registration_user_id'] = $userId;
        $_SESSION['registration_stage'] = 1;
        error_log("Session updated with user_id and stage");
        error_log("Session data after update: " . print_r($_SESSION, true));

        // Commit transaction
        $conn->commit();
        error_log("Transaction committed");

        // Redirect to Stage 2
        header("Location: register2.php");
        exit();

    } catch (Exception $e) {
        error_log("Error in register.php: " . $e->getMessage());
        // Rollback transaction if started
        if ($conn->inTransaction()) {
            $conn->rollBack();
            error_log("Transaction rolled back");
        }
        $error = $e->getMessage();
        $_SESSION['form_data'] = $formData;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Birth & Death Certificate System</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            background-color: #b5dcb3;
        }
        
        .register-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 40px;
            background-color: #b5dcb3;
            border-radius: 20px;
            box-shadow: 9px 9px 16px #88b489, -9px -9px 16px #e2ffe2;
            transition: transform 0.3s ease-in-out;
        }

        .register-container:hover {
            transform: scale(1.02);
        }

        .register-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .register-header img {
            max-width: 120px;
            margin-bottom: 20px;
        }

        .register-header h4 {
            color: #2c662c;
            font-size: 24px;
            margin: 0;
        }

        .form-group {
            margin-bottom: 25px;
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

        .input-group-prepend .input-group-text,
        .input-group-append .input-group-text {
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

        .custom-control-label {
            color: #2c662c;
        }

        .btn-register {
            width: 100%;
            padding: 14px 28px;
            font-size: 18px;
            color: white;
            background-color: #228b22;
            border: none;
            border-radius: 12px;
            box-shadow: 6px 6px 10px #88b489, -6px -6px 10px #e2ffe2;
            transition: all 0.3s ease-in-out;
        }

        .btn-register:hover {
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
        }

        .password-requirements {
            background: #b5dcb3;
            border-radius: 12px;
            padding: 12px;
            box-shadow: inset 6px 6px 10px #88b489, inset -6px -6px 10px #e2ffe2;
            color: #2c662c;
            font-size: 0.85rem;
            margin-top: 8px;
        }

        .text-center a {
            color: #2c662c;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .text-center a:hover {
            color: #196619;
        }

        @media (max-width: 768px) {
            .register-container {
                margin: 30px 15px;
                padding: 30px;
            }

            .register-header img {
                max-width: 100px;
            }

            .register-header h4 {
                font-size: 20px;
            }

            .btn-register {
                padding: 12px 24px;
                font-size: 16px;
            }
        }

        @media (max-width: 480px) {
            .register-container {
                margin: 20px 10px;
                padding: 20px;
            }

            .register-header img {
                max-width: 80px;
            }

            .register-header h4 {
                font-size: 18px;
            }

            .input-group-text {
                padding: 8px;
            }

            .form-control {
                padding: 8px;
                font-size: 14px;
            }

            .btn-register {
                padding: 10px 20px;
                font-size: 14px;
            }

            .password-requirements {
                font-size: 12px;
                padding: 10px;
            }
        }

        @media (max-width: 360px) {
            .register-container {
                margin: 15px 8px;
                padding: 15px;
            }

            .register-header img {
                max-width: 70px;
            }

            .register-header h4 {
                font-size: 16px;
            }

            .btn-register {
                padding: 8px 16px;
                font-size: 13px;
            }
        }
    </style>
</head>
<body>
<div class="container">
        <div class="register-container">
            <div class="register-header">
                <img src="logo.png" alt="Logo">
                <h4>Create Account</h4>
            </div>

    <?php if ($error): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
            </div>
            <?php endif; ?>

            <?php if ($success): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success); ?>
                <div class="mt-2">
                    <a href="user_login.php" class="btn btn-success btn-sm">
                        <i class="fas fa-sign-in-alt"></i> Login Now
                    </a>
                </div>
        </div>
    <?php endif; ?>

            <form method="POST" action="" id="registerForm">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="first-name">First Name</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                </div>
                                <input type="text" class="form-control" id="first-name" name="first-name" 
                                       value="<?php echo htmlspecialchars($formData['first-name'] ?? ''); ?>"
                                       pattern="[A-Za-z\s]{2,50}" title="Only letters allowed (2-50 characters)" required>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="last-name">Last Name</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                </div>
                                <input type="text" class="form-control" id="last-name" name="last-name" 
                                       value="<?php echo htmlspecialchars($formData['last-name'] ?? ''); ?>"
                                       pattern="[A-Za-z\s]{2,50}" title="Only letters allowed (2-50 characters)" required>
                            </div>
                        </div>
                    </div>
                </div>
        
        <div class="form-group">
                    <label for="email">Email Address</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        </div>
                        <input type="email" class="form-control" id="email" name="email" 
                               value="<?php echo htmlspecialchars($formData['email'] ?? ''); ?>" required>
                    </div>
        </div>
        
        <div class="form-group">
                    <label for="mobile">Mobile Number</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-phone"></i></span>
                        </div>
                        <input type="text" class="form-control" id="mobile" name="mobile" 
                               value="<?php echo htmlspecialchars($formData['mobile'] ?? ''); ?>" 
                               pattern="[0-9]{10}" title="Please enter 10 digits" required>
                    </div>
        </div>
        
        <div class="form-group">
            <label for="password">Password</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        </div>
                        <input type="password" class="form-control" id="password" name="password" 
                               minlength="8" required>
                        <div class="input-group-append">
                            <span class="input-group-text" onclick="togglePassword('password', 'togglePassword')">
                                <i class="fas fa-eye" id="togglePassword"></i>
                            </span>
            </div>
            </div>
                    <div class="password-requirements">
                        Password must contain at least 8 characters, including uppercase, lowercase, 
                        number and special character.
            </div>
        </div>
        
        <div class="form-group">
            <label for="confirm-password">Confirm Password</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        </div>
                        <input type="password" class="form-control" id="confirm-password" name="confirm-password" 
                               minlength="8" required>
                        <div class="input-group-append">
                            <span class="input-group-text" onclick="togglePassword('confirm-password', 'toggleConfirmPassword')">
                                <i class="fas fa-eye" id="toggleConfirmPassword"></i>
                            </span>
                        </div>
            </div>
        </div>

                <button type="submit" class="btn btn-primary btn-register">
                    <i class="fas fa-user-plus"></i> Register
                </button>

                <div class="text-center mt-3">
                    Already have an account? <a href="user_login.php">Login here</a>
                </div>
    </form>
        </div>
</div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
        function togglePassword(inputId, toggleId) {
            const input = document.getElementById(inputId);
            const toggle = document.getElementById(toggleId);
            
            if (input.type === 'password') {
                input.type = 'text';
                toggle.classList.remove('fa-eye');
                toggle.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                toggle.classList.remove('fa-eye-slash');
                toggle.classList.add('fa-eye');
            }
        }

        // Client-side validation
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            const firstName = document.getElementById('first-name').value.trim();
            const lastName = document.getElementById('last-name').value.trim();
            const email = document.getElementById('email').value.trim();
            const mobile = document.getElementById('mobile').value.trim();
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirm-password').value;
            let isValid = true;
            let errorMessage = '';

            if (!firstName || !lastName || !email || !mobile || !password || !confirmPassword) {
                errorMessage = 'All fields are required.';
                isValid = false;
            } else if (!/^[A-Za-z\s]{2,50}$/.test(firstName) || !/^[A-Za-z\s]{2,50}$/.test(lastName)) {
                errorMessage = 'Names should only contain letters (2-50 characters).';
                isValid = false;
            } else if (!/\S+@\S+\.\S+/.test(email)) {
                errorMessage = 'Please enter a valid email address.';
                isValid = false;
            } else if (!/^[0-9]{10}$/.test(mobile)) {
                errorMessage = 'Please enter a valid 10-digit mobile number.';
                isValid = false;
            } else if (password.length < 8) {
                errorMessage = 'Password must be at least 8 characters long.';
                isValid = false;
            } else if (!/[A-Z]/.test(password) || !/[a-z]/.test(password) || 
                       !/[0-9]/.test(password) || !/[^A-Za-z0-9]/.test(password)) {
                errorMessage = 'Password must contain uppercase, lowercase, number and special character.';
                isValid = false;
            } else if (password !== confirmPassword) {
                errorMessage = 'Passwords do not match.';
                isValid = false;
            }

            if (!isValid) {
                e.preventDefault();
                const alertDiv = document.createElement('div');
                alertDiv.className = 'alert alert-danger';
                alertDiv.innerHTML = `<i class="fas fa-exclamation-circle"></i> ${errorMessage}`;
                
                const existingAlert = document.querySelector('.alert');
                if (existingAlert) {
                    existingAlert.remove();
                }
                
                document.querySelector('.register-header').insertAdjacentElement('afterend', alertDiv);
    }
});
</script>
</body>
</html>
