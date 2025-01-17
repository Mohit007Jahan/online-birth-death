<?php
session_start();
require_once 'dbconfig.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

$search_results = [];
$error = '';

// Handle search
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['search'])) {
    $search = trim($_GET['search']);
    $search_type = $_GET['search_type'] ?? 'name';
    
    if (!empty($search)) {
        try {
            $query = "SELECT DISTINCT u.*, ud.*,
                      (SELECT COUNT(*) FROM tblbirthapplications WHERE user_id = u.ID) as birth_applications,
                      (SELECT COUNT(*) FROM tbldeathapplications WHERE user_id = u.ID) as death_applications
                      FROM tbluser u 
                      LEFT JOIN tbluserdetails ud ON u.ID = ud.user_id 
                      WHERE 1=1";
            $params = [];
            
            switch ($search_type) {
                case 'email':
                    $query .= " AND u.Email LIKE ?";
                    $params[] = "%$search%";
                    break;
                case 'mobile':
                    $query .= " AND u.MobileNumber LIKE ?";
                    $params[] = "%$search%";
                    break;
                case 'nationality':
                    $query .= " AND ud.nationality LIKE ?";
                    $params[] = "%$search%";
                    break;
                case 'blood_group':
                    $query .= " AND ud.blood_group LIKE ?";
                    $params[] = "%$search%";
                    break;
                default: // name
                    $query .= " AND (u.FirstName LIKE ? OR u.LastName LIKE ?)";
                    $params = array_merge($params, ["%$search%", "%$search%"]);
                    break;
            }
            
            $query .= " GROUP BY u.ID ORDER BY u.RegDate DESC LIMIT 50";
            $stmt = $conn->prepare($query);
            $stmt->execute($params);
            $search_results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Error searching users: " . $e->getMessage());
            $error = "An error occurred while searching users";
        }
    }
}

// Get recent users if no search is performed
if (empty($search_results) && !isset($_GET['search'])) {
    try {
        $stmt = $conn->query("SELECT DISTINCT u.*, ud.*,
                           (SELECT COUNT(*) FROM tblbirthapplications WHERE user_id = u.ID) as birth_applications,
                           (SELECT COUNT(*) FROM tbldeathapplications WHERE user_id = u.ID) as death_applications
                           FROM tbluser u 
                           LEFT JOIN tbluserdetails ud ON u.ID = ud.user_id 
                           GROUP BY u.ID
                           ORDER BY u.RegDate DESC LIMIT 10");
        $search_results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching recent users: " . $e->getMessage());
        $error = "An error occurred while fetching recent users";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Users</title>
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

        /* Keep all existing search-specific styles */
        .main-content {
            flex-grow: 1;
            padding: 20px;
        }

        .search-section {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }

        .search-form {
            display: flex;
            gap: 15px;
            align-items: flex-end;
        }

        .form-group {
            flex-grow: 1;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #666;
            font-weight: bold;
        }

        .form-group input, .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }

        .search-btn {
            background-color: #1d991f;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }

        .search-btn:hover {
            background-color: #167c18;
        }

        .results-section {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .results-header {
            padding: 20px;
            border-bottom: 1px solid #ddd;
        }

        .results-header h2 {
            margin: 0;
            color: #1d991f;
        }

        .user-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            padding: 20px;
        }

        .user-card {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            transition: transform 0.3s;
            position: relative;
            overflow: hidden;
        }

        .user-card:hover {
            transform: translateY(-5px);
        }

        .user-card h3 {
            margin: 0 0 15px 0;
            color: #1d991f;
        }

        .user-info-card {
            margin-bottom: 15px;
        }

        .user-info-card p {
            margin: 5px 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-info-card i {
            color: #1d991f;
            width: 20px;
        }

        .user-stats {
            display: flex;
            gap: 15px;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
        }

        .user-actions {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            text-align: right;
        }

        .view-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background-color: #1d991f;
            color: white;
            padding: 8px 16px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .view-btn:hover {
            background-color: #167c18;
            transform: translateY(-2px);
        }

        .view-btn i {
            font-size: 16px;
        }

        .user-card::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background-color: #1d991f;
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }

        .user-card:hover::after {
            transform: scaleX(1);
        }

        .stat {
            flex: 1;
            text-align: center;
            padding: 10px;
            background-color: white;
            border-radius: 5px;
        }

        .stat p {
            margin: 0;
            font-size: 12px;
            color: #666;
        }

        .stat strong {
            display: block;
            font-size: 18px;
            color: #1d991f;
        }

        .alert {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        @media (max-width: 768px) {
            .search-form {
                flex-direction: column;
            }

            .search-btn {
                width: 100%;
            }

            .user-grid {
                grid-template-columns: 1fr;
            }

            .user-stats {
                flex-direction: row;
            }

            .user-actions {
                text-align: center;
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
            <a href="admin_profile.php">
                <i class="fas fa-user"></i> View Profile
            </a>
            <a href="pending_requests.php">
                <i class="fas fa-clock"></i> Pending Request
            </a>
            <a href="admin_search_user.php" class="active">
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
            <div class="search-section">
                <form method="GET" class="search-form">
                    <div class="form-group">
                        <label for="search_type">Search By</label>
                        <select name="search_type" id="search_type">
                            <option value="name" <?php echo isset($_GET['search_type']) && $_GET['search_type'] === 'name' ? 'selected' : ''; ?>>Name</option>
                            <option value="email" <?php echo isset($_GET['search_type']) && $_GET['search_type'] === 'email' ? 'selected' : ''; ?>>Email</option>
                            <option value="mobile" <?php echo isset($_GET['search_type']) && $_GET['search_type'] === 'mobile' ? 'selected' : ''; ?>>Mobile Number</option>
                            <option value="nationality" <?php echo isset($_GET['search_type']) && $_GET['search_type'] === 'nationality' ? 'selected' : ''; ?>>Nationality</option>
                            <option value="blood_group" <?php echo isset($_GET['search_type']) && $_GET['search_type'] === 'blood_group' ? 'selected' : ''; ?>>Blood Group</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="search">Search Term</label>
                        <input type="text" name="search" id="search" 
                               value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>" 
                               placeholder="Enter search term...">
                    </div>
                    <button type="submit" class="search-btn">Search</button>
                </form>
            </div>

            <?php if ($error): ?>
                <div class="alert"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <div class="results-section">
                <div class="results-header">
                    <h2><?php echo isset($_GET['search']) ? 'Search Results' : 'Recent Users'; ?></h2>
                </div>
                <div class="user-grid">
                    <?php foreach ($search_results as $user): ?>
                        <div class="user-card">
                            <h3><?php echo htmlspecialchars($user['FirstName'] . ' ' . $user['LastName']); ?></h3>
                            <div class="user-info">
                                <p>
                                    <i class="fas fa-envelope"></i>
                                    <?php echo htmlspecialchars($user['Email']); ?>
                                </p>
                                <p>
                                    <i class="fas fa-phone"></i>
                                    <?php echo htmlspecialchars($user['MobileNumber']); ?>
                                </p>
                                <?php if (!empty($user['nationality'])): ?>
                                <p>
                                    <i class="fas fa-flag"></i>
                                    <?php echo htmlspecialchars($user['nationality']); ?>
                                </p>
                                <?php endif; ?>
                                <?php if (!empty($user['blood_group'])): ?>
                                <p>
                                    <i class="fas fa-tint"></i>
                                    <?php echo htmlspecialchars($user['blood_group']); ?>
                                </p>
                                <?php endif; ?>
                                <p>
                                    <i class="fas fa-calendar"></i>
                                    Joined: <?php echo date('M d, Y', strtotime($user['RegDate'])); ?>
                                </p>
                                <p>
                                    <i class="fas fa-user-check"></i>
                                    Status: <?php echo htmlspecialchars($user['status']); ?>
                                </p>
                            </div>
                            <div class="user-stats">
                                <div class="stat">
                                    <strong><?php echo $user['birth_applications']; ?></strong>
                                    <p>Birth Applications</p>
                                </div>
                                <div class="stat">
                                    <strong><?php echo $user['death_applications']; ?></strong>
                                    <p>Death Applications</p>
                                </div>
                            </div>
                            <div class="user-actions">
                                <a href="admin_view_user.php?id=<?php echo $user['ID']; ?>" class="view-btn">
                                    <i class="fas fa-eye"></i> View Details
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <?php if (empty($search_results)): ?>
                        <div style="grid-column: 1/-1; text-align: center; padding: 40px;">
                            <p>No users found.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
