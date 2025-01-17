<?php
session_start();
require_once 'dbconfig.php';

// Check if user is logged in
if (!isset($_SESSION['uid'])) {
    header("Location: user_login.php");
    exit();
}

$success_msg = '';
$error_msg = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current_password = trim($_POST['current_password'] ?? '');
    $new_password = trim($_POST['new_password'] ?? '');
    $confirm_password = trim($_POST['confirm_password'] ?? '');

    // Password validation regex
    $uppercase = preg_match('@[A-Z]@', $new_password);
    $lowercase = preg_match('@[a-z]@', $new_password);
    $number    = preg_match('@[0-9]@', $new_password);
    $specialChars = preg_match('@[^\w]@', $new_password);

    // Validate inputs
    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $error_msg = "All fields are required.";
    } elseif ($new_password !== $confirm_password) {
        $error_msg = "New password and confirm password do not match.";
    } elseif (strlen($new_password) < 8) {
        $error_msg = "New password must be at least 8 characters long.";
    } elseif (!$uppercase || !$lowercase || !$number || !$specialChars) {
        $error_msg = "Password must include at least one uppercase letter, one lowercase letter, one number, and one special character.";
    } else {
        try {
            // Verify current password
            $stmt = $conn->prepare("SELECT Password FROM tbluser WHERE ID = ?");
            $stmt->execute([$_SESSION['uid']]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                $error_msg = "User not found.";
            } elseif ($user['Password'] !== $current_password) {
                $error_msg = "Current password is incorrect.";
            } else {
                // Begin transaction
                $conn->beginTransaction();
                
                try {
                    // Update password
                    $stmt = $conn->prepare("UPDATE tbluser SET Password = ? WHERE ID = ?");
                    $stmt->execute([$new_password, $_SESSION['uid']]);
                    
                    $conn->commit();
                    $success_msg = "Password updated successfully!";
                    
                    // Clear the form
                    $_POST = array();
                } catch (Exception $e) {
                    $conn->rollBack();
                    error_log("Password update error: " . $e->getMessage());
                    $error_msg = "Failed to update password. Please try again.";
                }
            }
        } catch (PDOException $e) {
            error_log("Password reset error: " . $e->getMessage());
            $error_msg = "An error occurred. Please try again later.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #b5dcb3;
            min-height: 100vh;
        }

        .main-wrapper {
            display: flex;
            min-height: calc(100vh - 70px);
        }

        .sidebar {
            width: 250px;
            background-color: #004d00;
            padding: 20px;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
        }

        .sidebar a {
            display: flex;
            align-items: center;
            gap: 10px;
            color: white;
            padding: 12px 15px;
            text-decoration: none;
            margin-bottom: 10px;
            border-radius: 8px;
            transition: all 0.3s ease;
            background-color: rgba(255,255,255,0.1);
            box-shadow: 2px 2px 5px rgba(0,0,0,0.1);
        }

        .sidebar a:hover, .sidebar a.active {
            background-color: #007700;
            transform: translateX(5px);
        }

        .sidebar a i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }

        .content-wrapper {
            flex: 1;
            padding: 20px;
            background: #d4edda;
            box-shadow: inset 2px 2px 5px rgba(0,0,0,0.05);
        }

        .header {
            background: #81c784;
            padding: 15px 20px;
            color: white;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .logo-section {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .logo-section img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .logo-section h1 {
            font-size: 24px;
            margin: 0;
            color: #004d00;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
            animation: fadeIn 0.5s ease-out 0.5s both;
        }

        .user-info img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }

        .user-info img:hover {
            transform: scale(1.05);
        }

        .logout-btn {
            background: #006600;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.3s ease;
            box-shadow: 2px 2px 5px rgba(0,0,0,0.1);
        }

        .logout-btn:hover {
            background-color: #b71c1c;
            transform: scale(1.05);
        }

        .form-container {
            max-width: 600px;
            margin: 20px auto;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            animation: fadeIn 0.8s ease-out;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .form-container:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        }

        .form-container h2 {
            color: #004d00;
            margin-bottom: 30px;
            text-align: center;
            font-size: 24px;
            animation: slideDown 0.5s ease-in-out;
        }

        .form-group {
            margin-bottom: 25px;
            position: relative;
            animation: slideUp 0.5s ease-out;
            animation-fill-mode: both;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #004d00;
            font-weight: bold;
        }

        .form-group input {
            width: 100%;
            padding: 12px 35px 12px 15px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        .form-group input:focus {
            border-color: #004d00;
            box-shadow: 0 0 10px rgba(0,77,0,0.1);
            outline: none;
        }

        .password-toggle {
            position: absolute;
            right: 12px;
            top: 38px;
            color: #666;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .password-toggle:hover {
            color: #004d00;
        }

        .password-requirements {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            border: 1px solid #e9ecef;
            animation: slideUp 0.5s ease-out 0.5s both;
        }

        .password-requirements ul {
            list-style: none;
            padding: 0;
            margin: 10px 0 0;
        }

        .password-requirements li {
            margin: 8px 0;
            color: #666;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn {
            background: #004d00;
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
            font-weight: bold;
            transition: all 0.3s ease;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .btn:hover {
            background: #006600;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .success, .error {
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            animation: slideDown 0.5s ease-out;
        }

        .success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        @media (max-width: 768px) {
            .main-wrapper {
                flex-direction: column;
            }

            .header {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                z-index: 1000;
                height: 60px;
                padding: 10px;
                background: #4CAF50;
                display: flex;
                align-items: center;
                justify-content: space-between;
                flex-direction: row;
            }

            .logo-section {
                width: auto;
                display: flex;
                align-items: center;
                gap: 10px;
            }

            .logo-section img {
                width: 30px;
                height: 30px;
            }

            .logo-section h1 {
                font-size: 16px;
                color: white;
            }

            .user-info {
                gap: 10px;
                display: flex;
                align-items: center;
            }

            .user-info img {
                width: 30px;
                height: 30px;
            }

            .user-info span {
                color: white;
                font-size: 14px;
            }

            .logout-btn {
                padding: 6px 12px;
                font-size: 14px;
                background: #004d00;
            }

            .sidebar {
                position: fixed;
                top: 60px;
                left: 0;
                right: 0;
                bottom: auto;
                width: 100%;
                height: 50px;
                padding: 5px;
                display: flex;
                overflow-x: auto;
                overflow-y: hidden;
                white-space: nowrap;
                z-index: 900;
            }

            .sidebar a {
                flex: 0 0 auto;
                margin: 0 3px;
                padding: 8px 12px;
                font-size: 13px;
                border-radius: 15px;
                display: flex;
                align-items: center;
                background: rgba(255,255,255,0.1);
            }

            .sidebar a i {
                margin-right: 5px;
            }

            .content-wrapper {
                margin-left: 0;
                margin-top: 110px;
                padding: 10px;
            }

            .form-container {
                margin: 0 10px;
                padding: 20px;
            }
        }

        @media (max-width: 480px) {
            .header {
                height: 50px;
                padding: 8px;
            }

            .logo-section img {
                width: 30px;
                height: 30px;
            }

            .logo-section h1 {
                font-size: 14px;
            }

            .user-info span {
                font-size: 12px;
            }

            .logout-btn {
                padding: 4px 8px;
                font-size: 12px;
            }

            .sidebar {
                top: 50px;
                height: 45px;
                padding: 5px;
            }

            .sidebar a {
                padding: 6px 10px;
                font-size: 12px;
            }

            .content-wrapper {
                margin-top: 95px;
                padding: 8px;
            }

            .form-container {
                padding: 15px;
            }
        }

        @media (max-width: 360px) {
            .logo-section h1 {
                font-size: 13px;
            }

            .user-info span {
                display: none;
            }

            .sidebar a {
                padding: 5px 8px;
                font-size: 11px;
            }

            .content-wrapper {
                padding: 5px;
            }

            .form-container {
                padding: 10px;
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideDown {
            from {
                transform: translateY(-30px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        @keyframes slideUp {
            from {
                transform: translateY(30px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        @keyframes scaleIn {
            from {
                transform: scale(0.8);
                opacity: 0;
            }
            to {
                transform: scale(1);
                opacity: 1;
            }
        }

        @keyframes pulseGlow {
            0% {
                box-shadow: 0 0 5px rgba(0,128,0,0.1);
            }
            50% {
                box-shadow: 0 0 20px rgba(0,128,0,0.2);
            }
            100% {
                box-shadow: 0 0 5px rgba(0,128,0,0.1);
            }
        }

        /* Apply animations to elements */
        .header {
            animation: slideDown 0.5s ease-out;
        }

        .logo-section img {
            animation: scaleIn 0.5s ease-out 0.3s both;
        }

        .user-info {
            animation: fadeIn 0.5s ease-out 0.5s both;
        }

        .form-container {
            animation: fadeIn 0.8s ease-out;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .form-container:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        }

        .form-group {
            animation: slideUp 0.5s ease-out;
            animation-fill-mode: both;
        }

        .form-group:nth-child(1) { animation-delay: 0.2s; }
        .form-group:nth-child(2) { animation-delay: 0.3s; }
        .form-group:nth-child(3) { animation-delay: 0.4s; }

        .password-requirements {
            animation: slideUp 0.5s ease-out 0.5s both;
        }

        .btn {
            animation: fadeIn 0.5s ease-out 0.6s both;
            transition: all 0.3s ease;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            background: #006600;
        }

        .requirement {
            transition: all 0.3s ease;
        }

        .requirement.valid {
            animation: scaleIn 0.3s ease-out;
        }

        .password-toggle {
            transition: all 0.3s ease;
        }

        .password-toggle:hover {
            transform: scale(1.2);
            color: #004d00;
        }

        /* Enhanced hover effects */
        .form-group input:focus {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        /* Smooth scrolling */
        html {
            scroll-behavior: smooth;
        }

        /* Success/Error messages animations */
        .success, .error {
            animation: slideDown 0.5s ease-out;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo-section">
            <img src="logo.png" alt="Logo">
            <h1>Online Birth & Death Certificate System</h1>
        </div>
        <div class="user-info">
            <img src="get_profile_image.php" alt="User">
            <span><?php echo htmlspecialchars($_SESSION['email'] ?? ''); ?></span>
            <a href="user_logout.php" class="logout-btn">Logout</a>
        </div>
    </div>

    <div class="main-wrapper">
        <div class="sidebar">
            <a href="user_dashboard.php">
                <i class="fas fa-home"></i> Home
            </a>
            <a href="edit_info.php">
                <i class="fas fa-user-edit"></i> Edit Profile
            </a>
            <a href="user_application_birth1.php">
                <i class="fas fa-certificate"></i> Register Birth Certificate
            </a>
            <a href="user_application_death1.php">
                <i class="fas fa-scroll"></i> Register Death Certificate
            </a>
            <a href="user_added_birth.php">
                <i class="fas fa-file-alt"></i> Added Birth Certificate
            </a>
            <a href="user_added_death.php">
                <i class="fas fa-file"></i> Added Death Certificate
            </a>
            <a href="user_reset_password.php" class="active">
                <i class="fas fa-key"></i> Reset Password
            </a>
        </div>

        <div class="content-wrapper">
            <div class="form-container">
                <h2>Reset Password</h2>

                <?php if ($error_msg): ?>
                    <div class="error">
                        <?php echo htmlspecialchars($error_msg); ?>
                    </div>
                <?php endif; ?>

                <?php if ($success_msg): ?>
                    <div class="success">
                        <?php echo htmlspecialchars($success_msg); ?>
                    </div>
                <?php endif; ?>

                <form action="user_reset_password.php" method="post" id="resetForm">
                    <div class="form-group">
                        <label for="current_password">Current Password</label>
                        <input type="password" id="current_password" name="current_password" required>
                        <i class="password-toggle fas fa-eye" onclick="togglePassword('current_password')"></i>
                    </div>

                    <div class="form-group">
                        <label for="new_password">New Password</label>
                        <input type="password" id="new_password" name="new_password" required>
                        <i class="password-toggle fas fa-eye" onclick="togglePassword('new_password')"></i>
                    </div>

                    <div class="form-group">
                        <label for="confirm_password">Confirm New Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" required>
                        <i class="password-toggle fas fa-eye" onclick="togglePassword('confirm_password')"></i>
                    </div>

                    <div class="password-requirements">
                        <div>Password Requirements:</div>
                        <ul>
                            <li class="requirement" id="length"><i class="fas fa-circle"></i> At least 8 characters</li>
                            <li class="requirement" id="uppercase"><i class="fas fa-circle"></i> One uppercase letter</li>
                            <li class="requirement" id="lowercase"><i class="fas fa-circle"></i> One lowercase letter</li>
                            <li class="requirement" id="number"><i class="fas fa-circle"></i> One number</li>
                            <li class="requirement" id="special"><i class="fas fa-circle"></i> One special character</li>
                        </ul>
                    </div>

                    <button type="submit" class="btn">Update Password</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const icon = input.nextElementSibling;
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        // Real-time password validation
        const newPassword = document.getElementById('new_password');
        const confirmPassword = document.getElementById('confirm_password');
        const requirements = {
            length: /.{8,}/,
            uppercase: /[A-Z]/,
            lowercase: /[a-z]/,
            number: /[0-9]/,
            special: /[^A-Za-z0-9]/
        };

        newPassword.addEventListener('input', function() {
            const password = this.value;
            
            for (const [key, regex] of Object.entries(requirements)) {
                const requirement = document.getElementById(key);
                const isValid = regex.test(password);
                requirement.classList.toggle('valid', isValid);
                requirement.classList.toggle('invalid', !isValid);
                requirement.querySelector('i').className = `fas ${isValid ? 'fa-check-circle' : 'fa-circle'}`;
            }
        });

        // Form validation
        document.getElementById('resetForm').addEventListener('submit', function(e) {
            const password = newPassword.value;
            const isValid = Object.values(requirements).every(regex => regex.test(password));
            
            if (!isValid) {
                e.preventDefault();
                alert('Please meet all password requirements.');
                return;
            }

            if (newPassword.value !== confirmPassword.value) {
                e.preventDefault();
                alert('Passwords do not match.');
            }
        });

        // Password match validation
        confirmPassword.addEventListener('input', function() {
            const isMatch = this.value === newPassword.value;
            this.style.borderColor = isMatch ? '#28a745' : '#dc3545';
        });
    </script>
</body>
</html>
