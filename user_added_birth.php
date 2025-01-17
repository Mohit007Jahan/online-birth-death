<?php
session_start();
require_once 'dbconfig.php';

// Check if user is logged in
if (!isset($_SESSION['uid'])) {
    header("Location: user_login.php");
    exit();
}

try {
    // Get all birth certificate applications for the current user
    $stmt = $conn->prepare("
        SELECT * FROM tblbirthapplications 
        WHERE user_id = ? 
        ORDER BY application_date DESC
    ");
    $stmt->execute([$_SESSION['uid']]);
    $applications = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    error_log("Error fetching birth applications: " . $e->getMessage());
    $error = "An error occurred while fetching your applications.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Added Birth Certificates</title>
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
            min-height: 100vh;
        }

        .header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 70px;
            background: #4CAF50;
            padding: 15px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            z-index: 1000;
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
        }

        .logo-section h1 {
            font-size: 24px;
            color: white;
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
        }

        .user-info span {
            color: white;
        }

        .sidebar {
            position: fixed;
            top: 70px;
            left: 0;
            bottom: 0;
            width: 250px;
            background-color: #004d00;
            padding: 20px;
            overflow-y: auto;
            z-index: 900;
        }

        .content-wrapper {
            flex: 1;
            margin-left: 250px;
            margin-top: 70px;
            padding: 20px;
            background: #d4edda;
            min-height: calc(100vh - 70px);
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .applications-table {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .applications-table table {
            width: 100%;
            min-width: 800px;
            background: white;
            border-collapse: collapse;
        }

        .panel-title {
            background: white;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 25px;
            text-align: center;
            color: #004d00;
            font-size: 24px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .applications-table th {
            background: #004d00;
            color: white;
            padding: 15px 20px;
            text-align: left;
            font-size: 16px;
        }

        .applications-table td {
            padding: 15px 20px;
            border-bottom: 1px solid #e0e0e0;
            color: #666;
        }

        .sidebar a {
            display: flex;
            align-items: center;
            color: white;
            padding: 12px 15px;
            text-decoration: none;
            margin-bottom: 10px;
            border-radius: 8px;
            transition: all 0.3s ease;
            background-color: rgba(255,255,255,0.1);
        }

        .sidebar a i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }

        .sidebar a:hover, .sidebar a.active {
            background-color: #007700;
            transform: translateX(5px);
        }

        .logout-btn {
            background: #006600;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .logout-btn:hover {
            background-color: #b71c1c;
            transform: scale(1.05);
        }

        .view-btn {
            background: #006600;
            color: white;
            padding: 8px 15px;
            border-radius: 8px;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
        }

        .view-btn:hover {
            background: #004d00;
            transform: scale(1.05);
        }

        .status-badge {
            padding: 8px 15px;
            border-radius: 8px;
            font-weight: 500;
            text-transform: capitalize;
            font-size: 14px;
        }

        .status-approved {
            background-color: #e6f4ea;
            color: #1e7e34;
            border: 1px solid #c3e6cb;
        }

        .status-rejected {
            background-color: #fbe9e7;
            color: #c62828;
            border: 1px solid #ffcdd2;
        }

        .status-pending {
            background-color: #fff3e0;
            color: #f57c00;
            border: 1px solid #ffe0b2;
        }

        @media (max-width: 768px) {
            .main-wrapper {
                flex-direction: column;
            }

            .header {
                height: 60px;
                padding: 10px;
            }

            .logo-section img {
                width: 30px;
                height: 30px;
            }

            .logo-section h1 {
                font-size: 16px;
            }

            .user-info img {
                width: 30px;
                height: 30px;
            }

            .user-info span {
                display: none;
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
                width: 100%;
                height: 50px;
                padding: 5px;
                display: flex;
                overflow-x: auto;
                overflow-y: hidden;
                white-space: nowrap;
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

            .container {
                padding: 10px;
                margin: 0;
            }

            .applications-table {
                margin: 0 -5px;
            }

            .panel-title {
                font-size: 18px;
                padding: 15px;
                margin-bottom: 20px;
            }

            .applications-table th,
            .applications-table td {
                font-size: 14px;
                padding: 12px 15px;
            }
        }

        @media (max-width: 480px) {
            .header {
                height: 50px;
                padding: 8px;
            }

            .logo-section h1 {
                font-size: 14px;
            }

            .sidebar {
                top: 50px;
                height: 45px;
            }

            .sidebar a {
                padding: 6px 10px;
                font-size: 12px;
            }

            .content-wrapper {
                margin-top: 95px;
                padding: 8px;
            }

            .container {
                padding: 8px;
            }

            .panel-title {
                font-size: 16px;
                padding: 12px;
            }

            .applications-table th,
            .applications-table td {
                font-size: 12px;
                padding: 10px;
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

            .content-wrapper {
                padding: 5px;
            }

            .container {
                padding: 5px;
            }

            .panel-title {
                font-size: 15px;
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="main-wrapper">
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

        <div class="sidebar">
            <a href="user_dashboard.php"><i class="fas fa-home"></i> Home</a>
            <a href="edit_info.php"><i class="fas fa-user-edit"></i> Edit Profile</a>
            <a href="user_application_birth1.php"><i class="fas fa-certificate"></i> Register Birth Certificate</a>
            <a href="user_application_death1.php"><i class="fas fa-scroll"></i> Register Death Certificate</a>
            <a href="user_added_birth.php" class="active"><i class="fas fa-file-alt"></i> Added Birth Certificate</a>
            <a href="user_added_death.php"><i class="fas fa-file"></i> Added Death Certificate</a>
            <a href="user_reset_password.php"><i class="fas fa-key"></i> Reset Password</a>
        </div>

        <div class="content-wrapper">
            <div class="container">
                <div class="panel-title">User Panel</div>
                <div class="applications-table">
                    <table>
                        <thead>
                            <tr>
                                <th>List No.</th>
                                <th>Registration No.</th>
                                <th>Name</th>
                                <th>Relationship</th>
                                <th>Added Date</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($applications)): ?>
                                <?php foreach ($applications as $index => $app): ?>
                                    <tr>
                                        <td><?php echo $index + 1; ?></td>
                                        <td><?php echo htmlspecialchars($app['registration_no']); ?></td>
                                        <td><?php echo htmlspecialchars($app['name']); ?></td>
                                        <td><?php echo htmlspecialchars($app['relationship']); ?></td>
                                        <td><?php echo htmlspecialchars($app['application_date']); ?></td>
                                        <td>
                                            <span class="status-badge status-<?php echo strtolower($app['status']); ?>">
                                                <?php echo ucfirst(htmlspecialchars($app['status'])); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if (strtolower($app['status']) === 'approved'): ?>
                                                <a href="user_certi_birth.php?id=<?php echo $app['id']; ?>" class="view-btn">View Certificate</a>
                                            <?php else: ?>
                                                <span class="status-badge status-<?php echo strtolower($app['status']); ?>">
                                                    <?php echo strtolower($app['status']) === 'rejected' ? 'Application Rejected' : 'Under Review'; ?>
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                <td colspan="7" style="text-align: center;">No applications found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>