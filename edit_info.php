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

try {
    // Fetch current user details
    $stmt = $conn->prepare("
        SELECT 
            u.ID,
            u.FirstName,
            u.LastName,
            u.Email,
            u.MobileNumber,
            u.Address,
            ud.present_address,
            ud.permanent_address,
            ud.nationality,
            ud.blood_group
        FROM tbluser u 
        LEFT JOIN tbluserdetails ud ON u.ID = ud.user_id 
        WHERE u.ID = ?
    ");
    $stmt->execute([$_SESSION['uid']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        throw new Exception("User not found");
    }

    // Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Debug log
        error_log("POST data received: " . print_r($_POST, true));
        
        // Validate input
        $firstName = trim($_POST['firstName']);
        $lastName = trim($_POST['lastName']);
        $email = trim($_POST['email']);
        $mobileNumber = trim($_POST['mobileNumber']);
        $address = trim($_POST['address']);
        $nationality = trim($_POST['nationality'] ?? '');
        $bloodGroup = trim($_POST['bloodGroup'] ?? '');

        // Debug log
        error_log("Processed form data:");
        error_log("First Name: $firstName");
        error_log("Last Name: $lastName");
        error_log("Email: $email");
        error_log("Mobile: $mobileNumber");
        error_log("Address: $address");
        error_log("Nationality: $nationality");
        error_log("Blood Group: $bloodGroup");

        if (empty($firstName) || empty($lastName) || empty($email) || empty($mobileNumber)) {
            $error_msg = "Please fill in all required fields.";
        } else {
            // Begin transaction
            $conn->beginTransaction();

            try {
                // Update tbluser
                $stmt = $conn->prepare("
                    UPDATE tbluser 
                    SET FirstName = ?, LastName = ?, Email = ?, MobileNumber = ?, Address = ?
                    WHERE ID = ?
                ");
                $stmt->execute([$firstName, $lastName, $email, $mobileNumber, $address, $_SESSION['uid']]);

                // Update tbluserdetails
                $stmt = $conn->prepare("
                    UPDATE tbluserdetails 
                    SET nationality = COALESCE(?, nationality),
                        blood_group = COALESCE(?, blood_group),
                        present_address = COALESCE(?, present_address)
                    WHERE user_id = ?
                ");
                $stmt->execute([
                    $nationality ?: null,
                    $bloodGroup ?: null,
                    $address ?: null,
                    $_SESSION['uid']
                ]);

                $conn->commit();
                $success_msg = "Profile updated successfully!";

                // Refresh user data
                $stmt = $conn->prepare("
                    SELECT 
                        u.ID,
                        u.FirstName,
                        u.LastName,
                        u.Email,
                        u.MobileNumber,
                        u.Address,
                        ud.present_address,
                        ud.permanent_address,
                        ud.nationality,
                        ud.blood_group
                    FROM tbluser u 
                    LEFT JOIN tbluserdetails ud ON u.ID = ud.user_id 
                    WHERE u.ID = ?
                ");
                $stmt->execute([$_SESSION['uid']]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

            } catch (Exception $e) {
                $conn->rollBack();
                error_log("Profile update error: " . $e->getMessage());
                $error_msg = "An error occurred while updating your profile.";
            }
        }
    }
} catch (PDOException $e) {
    error_log("Profile fetch error: " . $e->getMessage());
    $error_msg = "An error occurred while accessing the database. Please try again later.";
} catch (Exception $e) {
    error_log("Profile error: " . $e->getMessage());
    $error_msg = $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
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
        }

        .user-info img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
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
            max-width: 800px;
            margin: 20px auto;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            animation: fadeIn 0.8s ease-out;
        }

        .form-container h2 {
            color: #004d00;
            margin-bottom: 30px;
            text-align: center;
            font-size: 24px;
            font-weight: bold;
        }

        .edit-form {
            animation: fadeIn 0.8s ease-out;
        }

        .form-row {
            display: flex;
            gap: 20px;
            margin-bottom: 25px;
        }

        .form-row .form-group {
            flex: 1;
            margin-bottom: 0;
        }

        .form-group {
            margin-bottom: 25px;
            position: relative;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #004d00;
            font-weight: bold;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        .form-group input:focus,
        .form-group select:focus {
            border-color: #004d00;
            box-shadow: 0 0 10px rgba(0,77,0,0.1);
            outline: none;
        }

        .profile-image {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            margin: 0 auto 30px;
            display: block;
            object-fit: cover;
            border: 3px solid #004d00;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }

        .profile-image:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 25px rgba(0,0,0,0.2);
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
            margin-top: 20px;
        }

        .btn:hover {
            background: #006600;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .success, .error {
            padding: 15px;
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
                margin: 0;
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

            .form-row {
                flex-direction: column;
                gap: 15px;
                margin-bottom: 15px;
            }

            .form-group {
                margin-bottom: 15px;
            }

            .profile-image {
                width: 100px;
                height: 100px;
                margin-bottom: 20px;
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

            .form-group label {
                font-size: 14px;
            }

            .form-group input,
            .form-group select {
                padding: 10px;
                font-size: 14px;
            }

            .btn {
                padding: 10px 15px;
                font-size: 14px;
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

            .form-group label {
                font-size: 13px;
            }

            .form-group input,
            .form-group select {
                padding: 8px;
                font-size: 13px;
            }

            .btn {
                padding: 8px 12px;
                font-size: 13px;
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
            <a href="edit_info.php" class="active">
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
            <a href="user_reset_password.php">
                <i class="fas fa-key"></i> Reset Password
            </a>
        </div>

        <div class="content-wrapper">
            <div class="form-container">
                <h2>Edit Profile</h2>

                <?php if ($success_msg): ?>
                    <div class="success"><?php echo htmlspecialchars($success_msg); ?></div>
                <?php endif; ?>

                <?php if ($error_msg): ?>
                    <div class="error"><?php echo htmlspecialchars($error_msg); ?></div>
                <?php endif; ?>

                <div class="edit-form">
                    <img src="get_profile_image.php" alt="Profile Image" class="profile-image">
                    
                    <form method="POST" action="">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="firstName">First Name *</label>
                                <input type="text" id="firstName" name="firstName" value="<?php echo htmlspecialchars($user['FirstName'] ?? ''); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="lastName">Last Name *</label>
                                <input type="text" id="lastName" name="lastName" value="<?php echo htmlspecialchars($user['LastName'] ?? ''); ?>" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="email">Email *</label>
                                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['Email'] ?? ''); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="mobileNumber">Mobile Number *</label>
                                <input type="tel" id="mobileNumber" name="mobileNumber" value="<?php echo htmlspecialchars($user['MobileNumber'] ?? ''); ?>" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="nationality">Nationality</label>
                                <input type="text" id="nationality" name="nationality" value="<?php echo htmlspecialchars($user['nationality'] ?? ''); ?>">
                            </div>
                            <div class="form-group">
                                <label for="bloodGroup">Blood Group</label>
                                <select id="bloodGroup" name="bloodGroup">
                                    <option value="">Select Blood Group</option>
                                    <?php
                                    $bloodGroups = ['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-'];
                                    foreach ($bloodGroups as $bg) {
                                        $selected = ($user['blood_group'] ?? '') === $bg ? 'selected' : '';
                                        echo "<option value=\"$bg\" $selected>$bg</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="address">Present Address</label>
                            <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($user['Address'] ?? ''); ?>">
                        </div>

                        <button type="submit" class="btn">Update Profile</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
