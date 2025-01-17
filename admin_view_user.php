<?php
session_start();
require_once 'dbconfig.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Check if user ID is provided
if (!isset($_GET['id'])) {
    header("Location: admin_search_user.php");
    exit();
}

$user_id = $_GET['id'];

try {
    // Fetch user data from both tables
    $stmt = $conn->prepare("
        SELECT u.*, ud.*
        FROM tbluser u
        LEFT JOIN tbluserdetails ud ON u.ID = ud.user_id
        WHERE u.ID = ?
    ");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        header("Location: admin_search_user.php");
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
    $stmt->execute([$user_id]);
    $faceData = $stmt->fetch(PDO::FETCH_ASSOC);

    // Add face image to user data if available
    if ($faceData && !empty($faceData['face_image'])) {
        $user['face_image'] = $faceData['face_image'];
        error_log("Face image found for user " . $user_id);
    } else {
        error_log("No face image found for user " . $user_id);
    }

    // Get application counts
    $stmt = $conn->prepare("
        SELECT 
            (SELECT COUNT(*) FROM tblbirthapplications WHERE user_id = ?) as birth_count,
            (SELECT COUNT(*) FROM tbldeathapplications WHERE user_id = ?) as death_count
    ");
    $stmt->execute([$user_id, $user_id]);
    $counts = $stmt->fetch(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log("Error fetching user data: " . $e->getMessage());
    header("Location: admin_search_user.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile View</title>
    <style>
        :root {
            --primary-color: #1d991f;
            --secondary-color: #28a745;
            --accent-color: #ffc107;
            --text-dark: #2c3e50;
            --text-light: #ffffff;
            --background-light: #f8f9fa;
            --border-radius: 15px;
            --box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1d991f 0%, #28a745 100%);
            margin: 0;
            padding: 0;
            min-height: 100vh;
        }

        .container {
            max-width: 1200px;
            background: rgba(255, 255, 255, 0.95);
            border-radius: var(--border-radius);
            padding: 30px;
            margin: 80px auto;
            box-shadow: var(--box-shadow);
            display: flex;
            flex-wrap: wrap;
            gap: 30px;
            position: relative;
            overflow: hidden;
        }

        .container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, var(--accent-color), var(--primary-color));
        }

        .logo-container {
            position: absolute;
            top: 20px;
            left: 20px;
            z-index: 100;
        }

        .logo-container img {
            width: 120px;
            filter: drop-shadow(0 2px 5px rgba(0, 0, 0, 0.2));
        }

        .left-section {
            flex: 1 1 300px;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
            background: var(--background-light);
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
        }

        .profile-image-container {
            width: 200px;
            height: 200px;
            border-radius: 50%;
            overflow: hidden;
            border: 5px solid var(--primary-color);
            box-shadow: 0 0 20px rgba(29, 153, 31, 0.3);
            margin-bottom: 20px;
            transition: transform 0.3s ease;
        }

        .profile-image-container:hover {
            transform: scale(1.05);
        }

        .left-section h1 {
            font-size: 28px;
            margin: 15px 0;
            color: var(--primary-color);
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1);
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .right-section {
            flex: 2 1 600px;
            padding: 20px;
        }

        .right-section h2 {
            color: var(--text-dark);
            margin-bottom: 25px;
            padding-bottom: 10px;
            border-bottom: 3px solid var(--accent-color);
        }

        .profile-info {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .profile-info div {
            background: var(--background-light);
            padding: 15px;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            transition: transform 0.3s ease;
        }

        .profile-info div:hover {
            transform: translateY(-5px);
        }

        .profile-info label {
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 8px;
            display: block;
            font-size: 0.9em;
            text-transform: uppercase;
        }

        .profile-info span {
            display: block;
            padding: 10px;
            background: white;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
            color: var(--text-dark);
            font-size: 1.1em;
        }

        .stats-section {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 25px;
            border-radius: var(--border-radius);
            margin-top: 30px;
            box-shadow: var(--box-shadow);
        }

        .stats-section h3 {
            color: var(--text-dark);
            margin-bottom: 20px;
            text-align: center;
            font-size: 1.5em;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }

        .stat-item {
            background: white;
            padding: 20px;
            border-radius: var(--border-radius);
            text-align: center;
            box-shadow: var(--box-shadow);
            transition: transform 0.3s ease;
        }

        .stat-item:hover {
            transform: translateY(-5px);
        }

        .stat-item .value {
            font-size: 36px;
            font-weight: bold;
            color: var(--primary-color);
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
        }

        .stat-item .label {
            color: var(--text-dark);
            font-size: 1.1em;
            font-weight: 500;
        }

        .actions {
            display: flex;
            justify-content: center;
            margin-top: 30px;
            gap: 20px;
        }

        .actions button {
            padding: 12px 30px;
            border: none;
            border-radius: var(--border-radius);
            font-size: 1.1em;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            box-shadow: var(--box-shadow);
        }

        .actions .back-button {
            background: linear-gradient(45deg, #dc3545, #ff4d4d);
            color: white;
        }

        .actions .back-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(220, 53, 69, 0.4);
        }

        @media(max-width: 768px) {
            .container {
                margin: 20px;
                padding: 20px;
            }

            .profile-image-container {
                width: 150px;
                height: 150px;
            }

            .profile-info {
                grid-template-columns: 1fr;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .actions {
                flex-direction: column;
            }

            .actions button {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="logo-container">
        <img src="logo.png" alt="Logo">
    </div>

    <div class="container">
        <div class="left-section">
            <div class="profile-image-container">
                <img src="get_profile_image.php?user_id=<?php echo $user_id; ?>" alt="User Avatar" onerror="this.src='avatar.png';" style="width: 100%; height: 100%; object-fit: cover;">
            </div>
            <h1>User ID: <?php echo htmlspecialchars($user['ID']); ?></h1>
        </div>

        <div class="right-section">
            <h2>Detailed information of <?php echo htmlspecialchars($user['FirstName'] . ' ' . $user['LastName']); ?></h2>

            <div class="profile-info">
                <div>
                    <label>First Name</label>
                    <span><?php echo htmlspecialchars($user['FirstName']); ?></span>
                </div>
                <div>
                    <label>Last Name</label>
                    <span><?php echo htmlspecialchars($user['LastName']); ?></span>
                </div>
                <div>
                    <label>Email</label>
                    <span><?php echo htmlspecialchars($user['Email']); ?></span>
                </div>
                <div>
                    <label>Phone Number</label>
                    <span><?php echo htmlspecialchars($user['MobileNumber']); ?></span>
                </div>
                <div>
                    <label>Father's Name</label>
                    <span><?php echo htmlspecialchars($user['father_name'] ?? 'N/A'); ?></span>
                </div>
                <div>
                    <label>Father's BRN</label>
                    <span><?php echo htmlspecialchars($user['father_brn'] ?? 'N/A'); ?></span>
                </div>
                <div>
                    <label>Mother's Name</label>
                    <span><?php echo htmlspecialchars($user['mother_name'] ?? 'N/A'); ?></span>
                </div>
                <div>
                    <label>Mother's BRN</label>
                    <span><?php echo htmlspecialchars($user['mother_brn'] ?? 'N/A'); ?></span>
                </div>
                <div>
                    <label>Present Address</label>
                    <span><?php echo htmlspecialchars($user['present_address'] ?? 'N/A'); ?></span>
                </div>
                <div>
                    <label>Permanent Address</label>
                    <span><?php echo htmlspecialchars($user['permanent_address'] ?? 'N/A'); ?></span>
                </div>
                <div>
                    <label>Nationality</label>
                    <span><?php echo htmlspecialchars($user['nationality'] ?? 'N/A'); ?></span>
                </div>
                <div>
                    <label>Blood Group</label>
                    <span><?php echo htmlspecialchars($user['blood_group'] ?? 'N/A'); ?></span>
                </div>
                <div>
                    <label>Marital Status</label>
                    <span><?php echo htmlspecialchars($user['marital_status'] ?? 'N/A'); ?></span>
                </div>
                <div>
                    <label>Sex</label>
                    <span><?php echo htmlspecialchars($user['sex'] ?? 'N/A'); ?></span>
                </div>
                <div>
                    <label>Order of Child</label>
                    <span><?php echo htmlspecialchars($user['order_of_child'] ?? 'N/A'); ?></span>
                </div>
                <div>
                    <label>Occupation</label>
                    <span><?php echo htmlspecialchars($user['occupation'] ?? 'N/A'); ?></span>
                </div>
                <div>
                    <label>Registration Date</label>
                    <span><?php echo date('F d, Y', strtotime($user['RegDate'])); ?></span>
                </div>
                <div>
                    <label>Account Status</label>
                    <span><?php echo htmlspecialchars($user['status']); ?></span>
                </div>
            </div>

            <div class="stats-section">
                <h3>Application Statistics</h3>
                <div class="stats-grid">
                    <div class="stat-item">
                        <div class="value"><?php echo $counts['birth_count']; ?></div>
                        <div class="label">Birth Applications</div>
                    </div>
                    <div class="stat-item">
                        <div class="value"><?php echo $counts['death_count']; ?></div>
                        <div class="label">Death Applications</div>
                    </div>
                </div>
            </div>

            <div class="actions">
                <button class="back-button" onclick="window.location.href='admin_search_user.php'">Back</button>
            </div>
        </div>
    </div>
</body>
</html>
