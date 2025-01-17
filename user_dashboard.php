<?php
session_start();
require_once 'dbconfig.php';

// Check if user is logged in
if (!isset($_SESSION['uid'])) {
    header("Location: user_login.php");
    exit();
}

// Get user data from database
try {
    // Get user details first
    $stmt = $conn->prepare("
        SELECT u.*, ud.*
        FROM tbluser u
        LEFT JOIN tbluserdetails ud ON u.ID = ud.user_id
        WHERE u.ID = ?
    ");
    $stmt->execute([$_SESSION['uid']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        session_destroy();
        header("Location: user_login.php");
        exit();
    }

    // Get face image
    $stmt = $conn->prepare("
        SELECT face_image 
        FROM tblfaceverification 
        WHERE user_id = ? 
        AND verification_status = 'verified' 
        ORDER BY verification_date DESC 
        LIMIT 1
    ");
    $stmt->execute([$_SESSION['uid']]);
    $faceData = $stmt->fetch(PDO::FETCH_ASSOC);

    // Add face image to user data if available
    if ($faceData && !empty($faceData['face_image'])) {
        $user['face_image'] = $faceData['face_image'];
        error_log("Face image found for user " . $_SESSION['uid']);
    } else {
        error_log("No face image found for user " . $_SESSION['uid']);
    }

} catch (PDOException $e) {
    error_log("Error fetching user data: " . $e->getMessage());
    die("An error occurred. Please try again later.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard - Birth & Death Certificate System</title>
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
            background-color: #007700;
            transform: translateX(5px);
        }

        .sidebar a i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }

        .main-content {
            flex: 1;
            padding: 20px;
            background: #d4edda;
            box-shadow: inset 2px 2px 5px rgba(0,0,0,0.05);
        }

        .profile-section {
            background-color: white;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .profile-header {
            display: flex;
            align-items: center;
            gap: 25px;
            margin-bottom: 25px;
            padding-bottom: 20px;
            border-bottom: 2px solid #e0e0e0;
        }

        .profile-image-container {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            border: 3px solid #008000;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }

        .profile-image-container:hover {
            transform: scale(1.05);
        }

        .profile-info h2 {
            color: #004d00;
            margin-bottom: 10px;
            font-size: 24px;
        }

        .profile-info p {
            color: #666;
            margin: 8px 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .profile-info i {
            color: #008000;
            width: 20px;
        }

        .info-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
            margin-bottom: 25px;
        }

        .info-card {
            background-color: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }

        .info-card:hover {
            transform: translateY(-5px);
        }

        .info-card h3 {
            color: #004d00;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #008000;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .info-card h3 i {
            color: #008000;
        }

        .info-row {
            margin-bottom: 15px;
        }

        .info-label {
            font-weight: bold;
            color: #444;
            margin-bottom: 5px;
        }

        .info-value {
            color: #666;
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }

        .edit-profile-btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background-color: #008000;
            color: white;
            padding: 12px 25px;
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .edit-profile-btn:hover {
            background-color: #006400;
            transform: translateY(-2px);
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

            .user-section {
                display: flex;
                align-items: center;
                gap: 10px;
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

            .container {
                margin-left: 0;
                margin-top: 110px;
                padding: 10px;
            }

            .main-content {
                margin-left: 0;
                padding: 10px;
            }

            .profile-section {
                margin: 10px;
                padding: 15px;
            }

            .profile-header {
                flex-direction: column;
                text-align: center;
                gap: 15px;
            }

            .profile-image-container {
                margin: 0 auto;
            }

            .info-cards {
                grid-template-columns: 1fr;
                gap: 15px;
            }

            .info-card {
                margin-bottom: 15px;
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

            .container {
                margin-top: 95px;
            }

            .main-content {
                padding: 8px;
            }

            .profile-section {
                padding: 12px;
            }

            .profile-header {
                gap: 12px;
            }

            .profile-image-container {
                width: 70px;
                height: 70px;
            }

            .profile-info h2 {
                font-size: 18px;
            }

            .info-cards {
                gap: 12px;
            }

            .info-card {
                padding: 12px;
            }

            .info-card h3 {
                font-size: 16px;
            }
        }

        @media (max-width: 360px) {
            .logo-section h1 {
                font-size: 13px;
            }

            .sidebar a {
                padding: 5px 8px;
                font-size: 11px;
            }

            .main-content {
                padding: 5px;
            }

            .profile-section {
                padding: 10px;
            }

            .profile-image-container {
                width: 60px;
                height: 60px;
            }

            .profile-info h2 {
                font-size: 16px;
            }

            .info-card {
                padding: 10px;
            }

            .info-card h3 {
                font-size: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo-section">
        <img src="logo.png" alt="Logo">
            <h1>Birth & Death Certificate System</h1>
        </div>
        <div class="user-section">
            <a href="user_logout.php" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </div>

    <div class="container">
    <div class="sidebar">
            <a href="user_dashboard.php" class="active">
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
            <a href="user_reset_password.php">
                <i class="fas fa-key"></i> Reset Password
            </a>
        </div>

        <div class="main-content">
            <div class="profile-section">
                <div class="profile-header">
                    <div class="profile-image-container" style="width: 80px; height: 80px; border-radius: 50%; border: 2px solid #008000; overflow: hidden;">
                        <img src="get_profile_image.php" alt="Profile" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                    <div class="profile-info">
                        <h2><?php echo htmlspecialchars($user['FirstName'] . ' ' . $user['LastName']); ?></h2>
                        <p><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($user['Email']); ?></p>
                        <p><i class="fas fa-phone"></i> <?php echo htmlspecialchars($user['MobileNumber']); ?></p>
                    </div>
                </div>
            </div>

            <div class="info-cards">
                <div class="info-card">
                    <h3><i class="fas fa-user-circle"></i> Personal Information</h3>
                    <div class="info-row">
                        <div class="info-label">Blood Group</div>
                        <div class="info-value"><?php echo htmlspecialchars($user['blood_group'] ?? 'Not specified'); ?></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Gender</div>
                        <div class="info-value"><?php echo htmlspecialchars($user['sex'] ?? 'Not specified'); ?></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Marital Status</div>
                        <div class="info-value"><?php echo htmlspecialchars($user['marital_status'] ?? 'Not specified'); ?></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Nationality</div>
                        <div class="info-value"><?php echo htmlspecialchars($user['nationality'] ?? 'Not specified'); ?></div>
                    </div>
                </div>

                <div class="info-card">
                    <h3><i class="fas fa-home"></i> Address Information</h3>
                    <div class="info-row">
                        <div class="info-label">Present Address</div>
                        <div class="info-value"><?php echo htmlspecialchars($user['present_address'] ?? 'Not specified'); ?></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Permanent Address</div>
                        <div class="info-value"><?php echo htmlspecialchars($user['permanent_address'] ?? 'Not specified'); ?></div>
                    </div>
    </div>

                <div class="info-card">
                    <h3><i class="fas fa-users"></i> Family Information</h3>
                    <div class="info-row">
                        <div class="info-label">Father's Name</div>
                        <div class="info-value"><?php echo htmlspecialchars($user['father_name'] ?? 'Not specified'); ?></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Father's BRN</div>
                        <div class="info-value"><?php echo htmlspecialchars($user['father_brn'] ?? 'Not specified'); ?></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Mother's Name</div>
                        <div class="info-value"><?php echo htmlspecialchars($user['mother_name'] ?? 'Not specified'); ?></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Mother's BRN</div>
                        <div class="info-value"><?php echo htmlspecialchars($user['mother_brn'] ?? 'Not specified'); ?></div>
                    </div>
                </div>
            </div>

            <a href="edit_info.php" class="edit-profile-btn">
                <i class="fas fa-edit"></i> Edit Profile
            </a>
        </div>
    </div>
</body>
</html>

