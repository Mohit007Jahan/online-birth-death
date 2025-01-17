<?php
session_start();
error_reporting(0);
require_once 'dbconfig.php';

// Initialize variables
$error = '';
$success = '';
$email = '';

// Check for registration success message
if (isset($_GET['registration']) && $_GET['registration'] === 'success') {
    $success = "Registration completed successfully! Please login with your credentials.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Sanitize inputs
        $email = trim(strip_tags($_POST['email'] ?? ''));
        $password = $_POST['password'] ?? '';

        // Validate inputs
        if (empty($email) || empty($password)) {
            throw new Exception("Please fill in all fields");
        }

        // Check credentials with plain text password
        $stmt = $conn->prepare("SELECT ID, Password, status FROM tbluser WHERE Email = ? AND Password = ?");
        $stmt->execute([$email, $password]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            throw new Exception("Invalid email or password");
        }

        if ($user['status'] !== 'active') {
            throw new Exception("Your account is not active. Please complete the registration process.");
        }

        // Store user ID in session temporarily
        $_SESSION['temp_uid'] = $user['ID'];
        
        // Redirect to face verification instead of dashboard
        header("Location: login_face_verify.php");
        exit;

    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Login - Birth & Death Certificate System</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #b5dcb3;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
            padding: 20px;
        }

        .container {
            width: 100%;
            max-width: 500px;
            margin: 0 auto;
        }

        .login-container {
            background-color: #b5dcb3;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 9px 9px 16px #88b489, -9px -9px 16px #e2ffe2;
            transition: transform 0.3s ease-in-out;
        }

        .login-container:hover {
            transform: scale(1.02);
        }

        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .login-header img {
            width: 120px;
            margin-bottom: 20px;
        }

        .login-header h4 {
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

        .btn-login {
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

        .btn-login:hover {
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

        .forgot-password a,
        .register-link a {
            color: #2c662c;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .forgot-password a:hover,
        .register-link a:hover {
            color: #196619;
        }

        @media (max-width: 768px) {
            .login-container {
                padding: 30px;
            }

            .login-header img {
                width: 100px;
            }

            .login-header h4 {
                font-size: 20px;
            }

            .btn-login {
                padding: 12px 24px;
                font-size: 16px;
            }
        }

        @media (max-width: 480px) {
            body {
                padding: 15px;
            }

            .login-container {
                padding: 20px;
            }

            .login-header img {
                width: 80px;
            }

            .login-header h4 {
                font-size: 18px;
            }

            .btn-login {
                padding: 10px 20px;
                font-size: 14px;
            }
        }

        @media (max-width: 360px) {
            body {
                padding: 10px;
            }

            .login-container {
                padding: 15px;
            }

            .login-header img {
                width: 70px;
            }

            .login-header h4 {
                font-size: 16px;
            }
        }

        .alert-success {
            background-color: #d4edda;
            border-color: #c3e6cb;
            color: #155724;
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 0.25rem;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php if ($success): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success); ?>
        </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
        </div>
        <?php endif; ?>
        
        <div class="login-container">
            <div class="login-header">
                <img src="logo.png" alt="Logo">
                <h4>User Login</h4>
            </div>

            <form method="POST" action="" id="loginForm">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        </div>
                        <input type="email" class="form-control" id="email" name="email" 
                               value="<?php echo htmlspecialchars($email); ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        </div>
                        <input type="password" class="form-control" id="password" name="password" required>
                        <div class="input-group-append">
                            <span class="input-group-text" onclick="togglePassword()">
                                <i class="fas fa-eye" id="togglePassword"></i>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="remember_me" name="remember_me">
                        <label class="custom-control-label" for="remember_me">Remember me</label>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-login">
                    <i class="fas fa-sign-in-alt"></i> Login
                </button>

                <div class="forgot-password">
                    <a href="forgot_password.php">Forgot Password?</a>
                </div>

                <div class="register-link">
                    Don't have an account? <a href="register.php">Register here</a>
                </div>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function togglePassword() {
            const password = document.getElementById('password');
            const toggleIcon = document.getElementById('togglePassword');
            
            if (password.type === 'password') {
                password.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                password.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }

        // Client-side validation
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value;
            let isValid = true;
            let errorMessage = '';

            if (!email) {
                errorMessage = 'Please enter your email address.';
                isValid = false;
            } else if (!/\S+@\S+\.\S+/.test(email)) {
                errorMessage = 'Please enter a valid email address.';
                isValid = false;
            }

            if (!password) {
                errorMessage = 'Please enter your password.';
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
                
                document.querySelector('.login-header').insertAdjacentElement('afterend', alertDiv);
            }
        });
    </script>
</body>
</html>
