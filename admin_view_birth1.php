<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once 'dbconfig.php';

// Test database connection and table
try {
    // Test connection
    $conn->query("SELECT 1");
    echo "<!-- Database connection successful -->\n";
    
    // List all tables in the database
    $tables = $conn->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    echo "<!-- Available tables in database: " . print_r($tables, true) . " -->\n";
    
    // Test if table exists
    $stmt = $conn->query("SHOW TABLES LIKE 'tblbirthapplications'");
    if ($stmt->rowCount() > 0) {
        echo "<!-- Table 'tblbirthapplications' exists -->\n";
        
        // Get table structure
        $stmt = $conn->query("DESCRIBE tblbirthapplications");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "<!-- Table structure:\n" . print_r($columns, true) . "\n-->\n";
        
        // Get record count
        $stmt = $conn->query("SELECT COUNT(*) FROM tblbirthapplications");
        $count = $stmt->fetchColumn();
        echo "<!-- Total records in table: " . $count . " -->\n";
    } else {
        echo "<!-- Table 'tblbirthapplications' does not exist. Available tables are listed above. -->\n";
    }
} catch (PDOException $e) {
    echo "<!-- Database test error: " . $e->getMessage() . " -->\n";
}

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Helper functions for database operations
function fetchOne($query, $params = []) {
    global $conn;
    try {
        $stmt = $conn->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchColumn();
    } catch (PDOException $e) {
        error_log("Database error in fetchOne: " . $e->getMessage());
        echo "Error in fetchOne: " . $e->getMessage(); // Debug output
        return 0;
    }
}

function fetchAll($query, $params = []) {
    global $conn;
    try {
        $stmt = $conn->prepare($query);
        $stmt->execute($params);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        // Debug output
        echo "<!-- Query: " . $query . " -->\n";
        echo "<!-- Params: " . print_r($params, true) . " -->\n";
        echo "<!-- Results: " . print_r($results, true) . " -->\n";
        return $results;
    } catch (PDOException $e) {
        error_log("Database error in fetchAll: " . $e->getMessage());
        echo "Error in fetchAll: " . $e->getMessage(); // Debug output
        return [];
    }
}

// Get filter parameters
$status = isset($_GET['status']) ? $_GET['status'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 10;
$offset = ($page - 1) * $per_page;

// Build query - simplified version first
try {
    // Initialize arrays for conditions and parameters
    $where_conditions = [];
$params = [];

    // Add status filter if selected
if ($status) {
        $where_conditions[] = "a.status = ?";
    $params[] = $status;
        echo "<!-- Debug: Status filter applied: " . $status . " -->\n";
}

    // Add search filter if provided
if ($search) {
        $where_conditions[] = "(a.name LIKE ? OR a.registration_no LIKE ? OR u.MobileNumber LIKE ?)";
    $search_param = "%$search%";
    $params = array_merge($params, [$search_param, $search_param, $search_param]);
        echo "<!-- Debug: Search filter applied: " . $search . " -->\n";
}

// Get total count for pagination
    $count_query = "SELECT COUNT(*) FROM tblbirthapplications a LEFT JOIN tbluser u ON a.user_id = u.ID";
    if (!empty($where_conditions)) {
        $count_query .= " WHERE " . implode(" AND ", $where_conditions);
    }
    $count_stmt = $conn->prepare($count_query);
    foreach ($params as $key => $value) {
        $count_stmt->bindValue($key + 1, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
    }
    $count_stmt->execute();
    $total_records = $count_stmt->fetchColumn();
$total_pages = ceil($total_records / $per_page);

    echo "<!-- Debug: Total records found: " . $total_records . " -->\n";
    
    // Build the base query
    $query = "SELECT a.*, u.MobileNumber 
              FROM tblbirthapplications a 
              LEFT JOIN tbluser u ON a.user_id = u.ID";
    
    // Combine WHERE conditions if any exist
    if (!empty($where_conditions)) {
        $query .= " WHERE " . implode(" AND ", $where_conditions);
    }
    
    // Add ORDER BY
    $query .= " ORDER BY a.application_date DESC";
    
    // Add LIMIT and OFFSET
    $query .= " LIMIT ?, ?";
    $params[] = (int)$offset;
    $params[] = (int)$per_page;
    
    echo "<!-- Debug: Final query: " . $query . " -->\n";
    echo "<!-- Debug: Query parameters: " . print_r($params, true) . " -->\n";
    
    // Execute the query
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        throw new PDOException("Failed to prepare statement: " . print_r($conn->errorInfo(), true));
    }
    
    // Bind parameters with explicit types
    foreach ($params as $key => $value) {
        $stmt->bindValue($key + 1, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
    }
    
    $result = $stmt->execute();
    if (!$result) {
        throw new PDOException("Failed to execute statement: " . print_r($stmt->errorInfo(), true));
    }
    
    $applications = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<!-- Debug: Found " . count($applications) . " applications -->\n";
    echo "<!-- Debug: Total pages: " . $total_pages . " -->\n";
    echo "<!-- Debug: Current page: " . $page . " -->\n";
    
    if (empty($applications)) {
        // Check table structure
        $stmt = $conn->query("SHOW COLUMNS FROM tblbirthapplications");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "<!-- Debug: Table columns: " . print_r($columns, true) . " -->\n";
    }
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    echo "<!-- Error details: " . $e->getMessage() . " -->\n";
    echo "<!-- Error trace: " . $e->getTraceAsString() . " -->\n";
    $error = "An error occurred while fetching applications: " . $e->getMessage();
    $applications = [];
    $total_pages = 0;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Birth Applications</title>
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

        /* Keep all existing birth certificate view styles */
        .main-content {
            flex-grow: 1;
            padding: 20px;
        }

        .filters {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            display: flex;
            gap: 20px;
            align-items: center;
        }

        .filters select, .filters input {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }

        .filters button {
            padding: 8px 15px;
            background-color: #1d991f;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .filters button:hover {
            background-color: #167c18;
        }

        .applications-table {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #1d991f;
        }

        tr:hover {
            background-color: #f5f5f5;
        }

        .status {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 14px;
        }

        .status.pending {
            background-color: #fff3cd;
            color: #856404;
        }

        .status.approved {
            background-color: #d4edda;
            color: #155724;
        }

        .status.rejected {
            background-color: #f8d7da;
            color: #721c24;
        }

        .pagination {
            margin-top: 20px;
            display: flex;
            justify-content: center;
            gap: 10px;
        }

        .pagination a {
            padding: 8px 12px;
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 5px;
            text-decoration: none;
            color: #1d991f;
            transition: all 0.3s;
        }

        .pagination a:hover {
            background-color: #1d991f;
            color: white;
        }

        .pagination a.active {
            background-color: #1d991f;
            color: white;
            border-color: #1d991f;
        }

        .view-btn {
            padding: 5px 10px;
            background-color: #1d991f;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 14px;
            transition: background-color 0.3s;
        }

        .view-btn:hover {
            background-color: #167c18;
        }

        .status-dropdown {
            padding: 5px 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
            cursor: pointer;
            font-size: 14px;
            width: 100%;
            min-width: 120px;
        }

        .status-dropdown.pending {
            background-color: #ffd700;
            color: #000;
        }

        .status-dropdown.approved {
            background-color: #28a745;
            color: white;
        }

        .status-dropdown.rejected {
            background-color: #dc3545;
            color: white;
        }

        @media (max-width: 768px) {
            .filters {
                flex-direction: column;
                align-items: stretch;
            }

            .applications-table {
                overflow-x: auto;
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
            <a href="admin_search_user.php">
                <i class="fas fa-users"></i> View User's Profile
            </a>
            <a href="admin_view_birth1.php" class="active">
                <i class="fas fa-certificate"></i> View Birth Applications
            </a>
            <a href="admin_view_death1.php">
                <i class="fas fa-scroll"></i> View Death Applications
            </a>
        </div>

        <div class="main-content">
            <div class="filters">
                <select id="status-filter" onchange="applyFilters()">
                    <option value="">All Status</option>
                    <option value="Pending" <?php echo $status === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="Approved" <?php echo $status === 'Approved' ? 'selected' : ''; ?>>Approved</option>
                    <option value="Rejected" <?php echo $status === 'Rejected' ? 'selected' : ''; ?>>Rejected</option>
                </select>
                <input type="text" id="search" placeholder="Search by name, ID or phone" 
                       value="<?php echo htmlspecialchars($search); ?>">
                <button onclick="applyFilters()">Apply Filters</button>
            </div>

            <div class="applications-table">
                <table>
                    <thead>
                        <tr>
                            <th>Application ID</th>
                            <th>Full Name</th>
                            <th>Mobile Number</th>
                            <th>Date of Birth</th>
                            <th>Application Date</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($applications)): ?>
                        <?php foreach ($applications as $app): ?>
                            <tr>
                                    <td><?php echo htmlspecialchars($app['registration_no'] ?? $app['REGISTRATION_NO'] ?? $app['RegistrationNo'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($app['name'] ?? $app['NAME'] ?? $app['Name'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($app['MobileNumber'] ?? 'N/A'); ?></td>
                                    <td><?php echo isset($app['date_of_birth']) ? date('M d, Y', strtotime($app['date_of_birth'])) : 
                                               (isset($app['DATE_OF_BIRTH']) ? date('M d, Y', strtotime($app['DATE_OF_BIRTH'])) : 
                                               (isset($app['DateOfBirth']) ? date('M d, Y', strtotime($app['DateOfBirth'])) : 'N/A')); ?></td>
                                    <td><?php echo isset($app['application_date']) ? date('M d, Y', strtotime($app['application_date'])) : 
                                               (isset($app['APPLICATION_DATE']) ? date('M d, Y', strtotime($app['APPLICATION_DATE'])) : 
                                               (isset($app['ApplicationDate']) ? date('M d, Y', strtotime($app['ApplicationDate'])) : 'N/A')); ?></td>
                                    <td>
                                        <select class="status-dropdown" onchange="updateStatus(this, '<?php echo $app['id'] ?? $app['ID'] ?? $app['Id']; ?>')">
                                            <option value="Pending" <?php echo (strtolower($app['status'] ?? '') === 'pending') ? 'selected' : ''; ?>>Pending</option>
                                            <option value="Approved" <?php echo (strtolower($app['status'] ?? '') === 'approved') ? 'selected' : ''; ?>>Approved</option>
                                            <option value="Rejected" <?php echo (strtolower($app['status'] ?? '') === 'rejected') ? 'selected' : ''; ?>>Rejected</option>
                                        </select>
                                </td>
                                <td>
                                        <a href="admin_view_birth_application.php?id=<?php echo $app['id'] ?? $app['ID'] ?? $app['Id']; ?>" 
                                           class="view-btn">View Details</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 20px;">
                                    No birth certificate applications found.
                                    <?php if (isset($error)) echo "<br><span style='color: red;'>" . htmlspecialchars($error) . "</span>"; ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($total_pages > 1): ?>
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?php echo $page-1; ?>&status=<?php echo $status; ?>&search=<?php echo $search; ?>">
                            Previous
                        </a>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="?page=<?php echo $i; ?>&status=<?php echo $status; ?>&search=<?php echo $search; ?>" 
                           class="<?php echo $i === $page ? 'active' : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>

                    <?php if ($page < $total_pages): ?>
                        <a href="?page=<?php echo $page+1; ?>&status=<?php echo $status; ?>&search=<?php echo $search; ?>">
                            Next
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function updateStatus(selectElement, applicationId) {
            const newStatus = selectElement.value;
            
            // Show confirmation dialog
            if (!confirm('Are you sure you want to change the status to ' + newStatus + '?')) {
                // Reset to previous value if user cancels
                selectElement.value = selectElement.getAttribute('data-previous');
                return;
            }
            
            // Store new value as previous
            selectElement.setAttribute('data-previous', newStatus);
            
            // Send AJAX request to update status
            fetch('update_birth_status.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'id=' + applicationId + '&status=' + newStatus
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Change the color of the select based on status
                    selectElement.className = 'status-dropdown ' + newStatus.toLowerCase();
                    alert('Status updated successfully!');
                } else {
                    alert('Failed to update status: ' + data.message);
                    // Reset to previous value on error
                    selectElement.value = selectElement.getAttribute('data-previous');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while updating the status.');
                // Reset to previous value on error
                selectElement.value = selectElement.getAttribute('data-previous');
            });
        }

        function applyFilters() {
            const status = document.getElementById('status-filter').value;
            const search = document.getElementById('search').value;
            window.location.href = `admin_view_birth1.php?status=${status}&search=${search}`;
        }

        // Enable search on enter key
        document.getElementById('search').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                applyFilters();
            }
        });
    </script>
</body>
</html>
