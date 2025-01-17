<?php
session_start();
require_once 'dbconfig.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

$admin_id = $_SESSION['admin_id'];
$success = $error = '';

// Fetch admin details first
try {
    $stmt = $conn->prepare("SELECT * FROM tbladmin WHERE id = ?");
    $stmt->execute([$admin_id]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$admin) {
        header("Location: admin_logout.php");
        exit();
    }
} catch (PDOException $e) {
    error_log("Error fetching admin profile: " . $e->getMessage());
    $error = "Failed to fetch profile details";
}

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $admin_name = trim($_POST['admin_name']);
    $email = trim($_POST['email']);
    $mobile = trim($_POST['mobile']);
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    try {
        // Start transaction
        $conn->beginTransaction();
        
        // Verify current password
        if (!password_verify($current_password, $admin['password'])) {
            throw new Exception("Current password is incorrect");
        }

        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid email format");
        }

        // Validate mobile number
        if (!preg_match("/^[0-9]{11}$/", $mobile)) {
            throw new Exception("Invalid mobile number format. Please enter 11 digits");
        }
        
        // Update basic info with correct column names
        $stmt = $conn->prepare("UPDATE tbladmin SET admin_name = ?, email = ?, mobile_number = ? WHERE id = ?");
        $stmt->execute([$admin_name, $email, $mobile, $admin_id]);
        
        // Update password if provided
        if (!empty($new_password)) {
            if (strlen($new_password) < 8) {
                throw new Exception("New password must be at least 8 characters long");
            }
            
            if ($new_password !== $confirm_password) {
                throw new Exception("New passwords do not match");
            }
            
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE tbladmin SET password = ? WHERE id = ?");
            $stmt->execute([$hashed_password, $admin_id]);
        }
        
        $conn->commit();
        $_SESSION['admin_name'] = $admin_name;
        $_SESSION['admin_email'] = $email;
        
        // Refresh admin data
        $stmt = $conn->prepare("SELECT * FROM tbladmin WHERE id = ?");
        $stmt->execute([$admin_id]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $success = "Profile updated successfully";
        
    } catch (Exception $e) {
        $conn->rollBack();
        $error = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Profile</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Base styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #b5dcb3;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Header styles */
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

        /* Container and Sidebar styles */
        .container {
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
            display: block;
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
            background-color: #1d991f;
            transform: translateX(5px);
        }

        .sidebar a i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }

        .logout-btn {
            background: #dc3545;
            color: white;
            padding: 8px 15px;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .logout-btn:hover {
            background-color: #c82333;
        }

        @media (max-width: 768px) {
            .container {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
                padding: 10px;
            }

            .header {
                flex-direction: column;
                text-align: center;
                gap: 10px;
            }
        }

        /* Keep all existing profile-specific styles */
        :root {
            --primary-color: #1d991f;
            --secondary-color: #28a745;
            --accent-color: #ffc107;
            --danger-color: #dc3545;
            --text-dark: #2c3e50;
            --text-light: #ffffff;
            --background-light: #f8f9fa;
            --border-radius: 15px;
            --box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .main-content {
            flex: 1;
            padding: 20px;
            background: #d4edda;
        }

        .panel-title {
            background: white;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
            color: #004d00;
            font-size: 24px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .profile-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 20px;
        }

        .profile-card h2 {
            color: #004d00;
            margin-top: 0;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #1d991f;
            font-size: 20px;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            margin-bottom: 10px;
            color: var(--text-dark);
            font-weight: 600;
            font-size: 0.95em;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .form-group label .required {
            color: var(--danger-color);
            font-size: 1.2em;
            line-height: 1;
        }

        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: var(--border-radius);
            font-size: 16px;
            transition: all 0.3s ease;
            background: white;
            box-sizing: border-box;
        }

        .form-group input:focus {
            border-color: var(--primary-color);
            outline: none;
            box-shadow: 0 0 0 3px rgba(29, 153, 31, 0.1);
        }

        .form-group input:required {
            background-image: radial-gradient(circle at 50% 50%, var(--danger-color) 2px, transparent 3px);
            background-repeat: no-repeat;
            background-position: right 15px center;
        }

        .form-group input:required:valid {
            background-image: radial-gradient(circle at 50% 50%, var(--primary-color) 2px, transparent 3px);
        }

        .password-section {
            margin-top: 40px;
            padding-top: 30px;
            border-top: 2px solid #e0e0e0;
        }

        .password-section h3 {
            color: var(--primary-color);
            margin-top: 0;
            margin-bottom: 25px;
            font-size: 20px;
        }

        .submit-btn {
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: var(--border-radius);
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            box-shadow: var(--box-shadow);
            width: 100%;
            margin-top: 20px;
        }

        .submit-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(29, 153, 31, 0.4);
        }

        .alert {
            padding: 15px 20px;
            border-radius: var(--border-radius);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 15px;
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from {
                transform: translateY(-20px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .alert i {
            font-size: 20px;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .current-info {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .info-item {
            background: var(--background-light);
            padding: 20px;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            transition: transform 0.3s ease;
        }

        .info-item:hover {
            transform: translateY(-5px);
        }

        .info-item label {
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--primary-color);
            font-weight: 600;
            margin-bottom: 10px;
            font-size: 0.9em;
            text-transform: uppercase;
        }

        .info-item span {
            display: block;
            color: var(--text-dark);
            font-size: 1.1em;
            padding: 8px;
            background: white;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
        }

        .profile-card + .profile-card {
            margin-top: 30px;
        }

        .status-badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.8em;
            letter-spacing: 1px;
        }

        .status-badge.active {
            background-color: var(--secondary-color);
            color: white;
        }

        .status-badge.inactive {
            background-color: var(--danger-color);
            color: white;
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
            <img src="admin.png" alt="Admin">
            <span><?php echo htmlspecialchars($_SESSION['admin_email']); ?></span>
            <a href="admin_logout.php" class="logout-btn">Logout</a>
        </div>
    </div>

    <div class="container">
        <div class="sidebar">
            <a href="admin_dashboard.php">
                <i class="fas fa-home"></i> Home
            </a>
            <a href="admin_profile.php" class="active">
                <i class="fas fa-user"></i> View Profile
            </a>
            <a href="pending_requests.php">
                <i class="fas fa-clock"></i> Pending Request
            </a>
            <a href="admin_search_user.php">
                <i class="fas fa-users"></i> View User's Profile
            </a>
            <a href="admin_view_birth1.php">
                <i class="fas fa-certificate"></i> Birth Certificate Status
            </a>
            <a href="admin_view_death1.php">
                <i class="fas fa-scroll"></i> Death Certificate Status
            </a>
        </div>

        <div class="main-content">
            <div class="panel-title">Admin Profile</div>

            <?php if ($success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <div class="profile-card">
                <h2><i class="fas fa-user-circle"></i> Current Profile Information</h2>
                <div class="current-info">
                    <div class="info-item">
                        <label><i class="fas fa-id-badge"></i> Admin ID</label>
                        <span><?php echo htmlspecialchars($admin['id']); ?></span>
                    </div>
                    <div class="info-item">
                        <label><i class="fas fa-user"></i> Full Name</label>
                        <span><?php echo htmlspecialchars($admin['admin_name']); ?></span>
                    </div>
                    <div class="info-item">
                        <label><i class="fas fa-user-tag"></i> Username</label>
                        <span><?php echo htmlspecialchars($admin['username']); ?></span>
                    </div>
                    <div class="info-item">
                        <label><i class="fas fa-envelope"></i> Email Address</label>
                        <span><?php echo htmlspecialchars($admin['email']); ?></span>
                    </div>
                    <div class="info-item">
                        <label><i class="fas fa-phone"></i> Mobile Number</label>
                        <span><?php echo htmlspecialchars($admin['mobile_number']); ?></span>
                    </div>
                    <div class="info-item">
                        <label><i class="fas fa-calendar-alt"></i> Registration Date</label>
                        <span><?php echo htmlspecialchars(date('F d, Y', strtotime($admin['created_at']))); ?></span>
                    </div>
                    <div class="info-item">
                        <label><i class="fas fa-clock"></i> Last Login</label>
                        <span><?php echo $admin['last_login'] ? htmlspecialchars(date('F d, Y H:i:s', strtotime($admin['last_login']))) : 'Never'; ?></span>
                    </div>
                    <div class="info-item">
                        <label><i class="fas fa-toggle-on"></i> Status</label>
                        <span class="status-badge <?php echo $admin['status'] === 'active' ? 'active' : 'inactive'; ?>">
                            <?php echo ucfirst(htmlspecialchars($admin['status'])); ?>
                        </span>
                    </div>
                </div>
            </div>

            <div class="profile-card">
                <h2><i class="fas fa-user-edit"></i> Edit Profile Information</h2>
                <form method="POST" id="profileForm">
                    <div class="form-group">
                        <label for="admin_name">
                            <i class="fas fa-user"></i> Full Name
                            <span class="required">*</span>
                        </label>
                        <input type="text" id="admin_name" name="admin_name" 
                               value="<?php echo htmlspecialchars($admin['admin_name']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="email">
                            <i class="fas fa-envelope"></i> Email Address
                            <span class="required">*</span>
                        </label>
                        <input type="email" id="email" name="email" 
                               value="<?php echo htmlspecialchars($admin['email']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="mobile">
                            <i class="fas fa-phone"></i> Mobile Number
                            <span class="required">*</span>
                        </label>
                        <input type="tel" id="mobile" name="mobile" 
                               value="<?php echo htmlspecialchars($admin['mobile_number']); ?>" 
                               pattern="[0-9]{11}" title="Please enter 11 digits" required>
                    </div>

                    <div class="password-section">
                        <h3><i class="fas fa-lock"></i> Change Password</h3>
                        <div class="form-group">
                            <label for="current_password">
                                Current Password
                                <span class="required">*</span>
                            </label>
                            <input type="password" id="current_password" name="current_password" required>
                        </div>

                        <div class="form-group">
                            <label for="new_password">New Password (leave blank to keep current)</label>
                            <input type="password" id="new_password" name="new_password" 
                                   minlength="8" placeholder="Minimum 8 characters">
                        </div>

                        <div class="form-group">
                            <label for="confirm_password">Confirm New Password</label>
                            <input type="password" id="confirm_password" name="confirm_password">
                        </div>
                    </div>

                    <button type="submit" class="submit-btn">
                        <i class="fas fa-save"></i> Update Profile
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Form validation
        document.getElementById('profileForm').addEventListener('submit', function(e) {
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            const email = document.getElementById('email').value;
            const mobile = document.getElementById('mobile').value;
            
            // Password validation
            if (newPassword) {
                if (newPassword.length < 8) {
                    e.preventDefault();
                    alert('New password must be at least 8 characters long');
                    return;
                }
                
                if (newPassword !== confirmPassword) {
                    e.preventDefault();
                    alert('Passwords do not match');
                    return;
                }
            }
            
            // Email validation
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailPattern.test(email)) {
                e.preventDefault();
                alert('Please enter a valid email address');
                return;
            }
            
            // Mobile number validation
            const mobilePattern = /^[0-9]{11}$/;
            if (!mobilePattern.test(mobile)) {
                e.preventDefault();
                alert('Please enter a valid 11-digit mobile number');
                return;
            }
        });

        // Real-time password confirmation validation
        document.getElementById('confirm_password').addEventListener('input', function() {
            const newPassword = document.getElementById('new_password').value;
            if (newPassword && this.value !== newPassword) {
                this.setCustomValidity('Passwords do not match');
            } else {
                this.setCustomValidity('');
            }
        });

        // Clear password mismatch error when typing in new password
        document.getElementById('new_password').addEventListener('input', function() {
            const confirmPassword = document.getElementById('confirm_password');
            if (confirmPassword.value) {
                if (this.value !== confirmPassword.value) {
                    confirmPassword.setCustomValidity('Passwords do not match');
                } else {
                    confirmPassword.setCustomValidity('');
                }
            }
        });
    </script>
</body>
</html>
