<?php
session_start();
require_once 'dbconfig.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

try {
    // Fetch pending birth applications
    $stmt = $conn->prepare("SELECT 
        b.*, u.FirstName, u.LastName, u.Email, u.MobileNumber,
        'Birth' as application_type
        FROM tblbirthapplications b
        LEFT JOIN tbluser u ON b.user_id = u.ID
        WHERE b.status = 'Pending'
        ORDER BY b.application_date DESC");
    $stmt->execute();
    $pending_birth = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch pending death applications
    $stmt = $conn->prepare("SELECT 
        d.*, u.FirstName, u.LastName, u.Email, u.MobileNumber,
        'Death' as application_type
        FROM tbldeathapplications d
        LEFT JOIN tbluser u ON d.user_id = u.ID
        WHERE d.status = 'Pending'
        ORDER BY d.application_date DESC");
    $stmt->execute();
    $pending_death = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Combine and sort all pending applications by date
    $all_pending = array_merge($pending_birth, $pending_death);
    usort($all_pending, function($a, $b) {
        return strtotime($b['application_date']) - strtotime($a['application_date']);
    });

} catch (PDOException $e) {
    error_log("Error: " . $e->getMessage());
    $error = "Failed to fetch pending applications";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pending Requests - Admin Dashboard</title>
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
            display: flex;
            flex-direction: column;
        }

        .main-wrapper {
            display: flex;
            flex: 1;
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

        .sidebar {
            width: 250px;
            background-color: #004d00;
            padding: 20px;
            min-height: calc(100vh - 80px);
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

        .content-wrapper {
            flex: 1;
            padding: 20px;
            background: #d4edda;
            box-shadow: inset 2px 2px 5px rgba(0,0,0,0.05);
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

        .pending-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .pending-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }

        .pending-card:hover {
            transform: translateY(-5px);
        }

        .pending-card h3 {
            color: #004d00;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #81c784;
        }

        .pending-info {
            margin-bottom: 15px;
        }

        .pending-info p {
            margin: 8px 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .pending-info i {
            color: #004d00;
            width: 20px;
            text-align: center;
        }

        .badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: bold;
        }

        .badge-birth {
            background-color: #e8f5e9;
            color: #2e7d32;
        }

        .badge-death {
            background-color: #fbe9e7;
            color: #c62828;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #eee;
        }

        .btn {
            flex: 1;
            padding: 8px;
            text-align: center;
            text-decoration: none;
            border-radius: 5px;
            transition: all 0.3s ease;
            font-size: 14px;
        }

        .btn-view {
            background-color: #004d00;
            color: white;
        }

        .btn-view:hover {
            background-color: #006600;
            transform: translateY(-2px);
        }

        .empty-message {
            text-align: center;
            padding: 40px;
            background: white;
            border-radius: 8px;
            color: #666;
        }

        @media (max-width: 768px) {
            .main-wrapper {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
                min-height: auto;
            }

            .header {
                flex-direction: column;
                text-align: center;
                gap: 10px;
            }

            .pending-grid {
                grid-template-columns: 1fr;
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
            <img src="admin_profile.png" alt="Admin">
            <span><?php echo htmlspecialchars($_SESSION['admin_email'] ?? ''); ?></span>
            <a href="admin_logout.php" class="logout-btn">Logout</a>
        </div>
    </div>

    <div class="main-wrapper">
        <div class="sidebar">
            <a href="admin_dashboard.php">
                <i class="fas fa-home"></i> Home
            </a>
            <a href="admin_profile.php">
                <i class="fas fa-user"></i> View Profile
            </a>
            <a href="pending_requests.php" class="active">
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

        <div class="content-wrapper">
            <div class="panel-title">Pending Requests</div>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <?php if (empty($all_pending)): ?>
                <div class="empty-message">
                    <i class="fas fa-check-circle" style="font-size: 48px; color: #004d00; margin-bottom: 20px;"></i>
                    <h3>No Pending Requests</h3>
                    <p>All applications have been processed.</p>
                </div>
            <?php else: ?>
                <div class="pending-grid">
                    <?php foreach ($all_pending as $application): ?>
                        <div class="pending-card">
                            <h3>
                                <span class="badge badge-<?php echo strtolower($application['application_type']); ?>">
                                    <?php echo htmlspecialchars($application['application_type']); ?> Certificate
                                </span>
                            </h3>
                            <div class="pending-info">
                                <p>
                                    <i class="fas fa-user"></i>
                                    <?php echo htmlspecialchars($application['name']); ?>
                                </p>
                                <p>
                                    <i class="fas fa-user-circle"></i>
                                    <?php echo htmlspecialchars($application['FirstName'] . ' ' . $application['LastName']); ?> (Applicant)
                                </p>
                                <p>
                                    <i class="fas fa-envelope"></i>
                                    <?php echo htmlspecialchars($application['Email']); ?>
                                </p>
                                <p>
                                    <i class="fas fa-phone"></i>
                                    <?php echo htmlspecialchars($application['MobileNumber']); ?>
                                </p>
                                <p>
                                    <i class="fas fa-calendar-alt"></i>
                                    <?php echo date('M d, Y H:i', strtotime($application['application_date'])); ?>
                                </p>
                            </div>
                            <div class="action-buttons">
                                <a href="<?php echo $application['application_type'] === 'Birth' ? 'admin_view_birth_application.php' : 'admin_view_death_application.php'; ?>?id=<?php echo $application['id']; ?>" 
                                   class="btn btn-view">
                                    <i class="fas fa-eye"></i> View Details
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html> 