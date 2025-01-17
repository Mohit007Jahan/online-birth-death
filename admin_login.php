<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'config.php';
require_once 'dbconfig.php';

// Debug log function
function debug_log($message) {
    error_log("[Admin Login Debug] " . $message);
    if (!headers_sent()) {
        echo "<script>console.log('" . addslashes($message) . "');</script>";
    }
}

// If already logged in, redirect to dashboard
if (isset($_SESSION['admin_id'])) {
    debug_log("Already logged in, redirecting to dashboard");
    header("Location: admin_dashboard.php");
    exit();
}

// Clear any existing error messages
$error = "";

// Process login form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    debug_log("Processing login form submission");
    try {
        $username = trim(strip_tags($_POST['username'] ?? ''));
        $password = $_POST['password'] ?? '';

        debug_log("Attempting login with username: " . $username);

        if (empty($username) || empty($password)) {
            throw new Exception("Please enter both username and password.");
        }

        // Check if user exists with matching username and password
        $stmt = $conn->prepare("SELECT * FROM tbladmin WHERE username = ? AND password = ? AND status = 'active' LIMIT 1");
        $stmt->execute([$username, $password]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($admin) {
            debug_log("Login successful for user: " . $username);
            
            // Set session variables
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['username'] = $admin['username'];
            $_SESSION['admin_name'] = $admin['admin_name'];
            $_SESSION['admin_email'] = $admin['email'];
            
            debug_log("Session variables set: " . print_r($_SESSION, true));
            
            // Update last login time
            $stmt = $conn->prepare("UPDATE tbladmin SET last_login = NOW() WHERE id = ?");
            $stmt->execute([$admin['id']]);

            // Ensure no output has been sent
            if (!headers_sent($filename, $linenum)) {
                debug_log("Redirecting to dashboard (headers not sent)");
                header("Location: admin_dashboard.php");
                exit();
            } else {
                debug_log("Headers already sent in $filename on line $linenum, using JavaScript redirect");
                echo "<script>window.location.href = 'admin_dashboard.php';</script>";
                exit();
            }
        } else {
            debug_log("Invalid login attempt for username: " . $username);
            throw new Exception("Invalid username or password.");
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
        debug_log("Login error: " . $e->getMessage());
    } catch (PDOException $e) {
        debug_log("Database error: " . $e->getMessage());
        $error = "An error occurred. Please try again later.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Birth & Death Certificate System</title>
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

        .login-header p {
            color: #2c662c;
            font-size: 16px;
            margin: 5px 0 0;
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

        .back-to-home {
            text-align: center;
            margin-top: 25px;
        }

        .back-to-home a {
            color: #2c662c;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .back-to-home a:hover {
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
    </style>
</head>
<body>
    <div class="container">
        <div class="login-container">
            <div class="login-header">
                <img src="logo.png" alt="Logo">
                <h4>Admin Login</h4>
                <p>Birth & Death Certificate System</p>
            </div>

            <?php if (!empty($error)): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
            </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label for="username">Username</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                        </div>
                        <input type="text" class="form-control" id="username" name="username" required>
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

                <div class="back-to-home">
                    <a href="login.php"><i class="fas fa-arrow-left"></i> Back to Home</a>
                </div>
            </form>
        </div>
    </div>

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
    </script>
</body>
</html>
