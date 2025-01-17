<?php
session_start();
require_once 'dbconfig.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Check if ID is provided
if (!isset($_GET['id'])) {
    header("Location: admin_view_death1.php");
    exit();
}

$id = $_GET['id'];

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['status'])) {
    $new_status = $_POST['status'];
    $remark = isset($_POST['remark']) ? trim($_POST['remark']) : '';
    
    try {
        // Update application status
        $stmt = $conn->prepare("UPDATE tbldeathapplications SET status = ? WHERE id = ?");
        $result = $stmt->execute([$new_status, $id]);
        
        if (!$result) {
            error_log("Failed to update status. Error info: " . print_r($stmt->errorInfo(), true));
            throw new PDOException("Failed to update status");
        }
        
        // Log the status change
        $admin_id = $_SESSION['admin_id'];
        $stmt = $conn->prepare("INSERT INTO status_logs (application_id, admin_id, old_status, new_status, remark, application_type) 
                               SELECT ?, ?, status, ?, ?, 'death' FROM tbldeathapplications WHERE id = ?");
        $result = $stmt->execute([$id, $admin_id, $new_status, $remark, $id]);
        
        if (!$result) {
            error_log("Failed to log status change. Error info: " . print_r($stmt->errorInfo(), true));
            throw new PDOException("Failed to log status change");
        }
        
        $success = "Application status updated successfully";
    } catch (PDOException $e) {
        error_log("Error updating status: " . $e->getMessage());
        error_log("Stack trace: " . $e->getTraceAsString());
        $error = "Failed to update application status: " . $e->getMessage();
    }
}

// Fetch application details
try {
    // Debug: Log the query parameters
    error_log("Fetching death application with ID: " . $id);
    
    // First verify if the application exists
    $stmt = $conn->prepare("SELECT COUNT(*) FROM tbldeathapplications WHERE id = ?");
    $stmt->execute([$id]);
    $count = $stmt->fetchColumn();
    
    if ($count == 0) {
        error_log("No application found with ID: " . $id);
        header("Location: admin_view_death1.php");
        exit();
    }
    
    // Fetch the application details with user information
    $stmt = $conn->prepare("
        SELECT 
            a.*,
            u.FirstName as UserFirstName,
            u.LastName as UserLastName,
            u.Email as UserEmail,
            u.MobileNumber as UserMobileNumber
        FROM tbldeathapplications a
        LEFT JOIN tbluser u ON a.user_id = u.ID
        WHERE a.id = ?
    ");
    
    if (!$stmt) {
        error_log("Failed to prepare statement: " . print_r($conn->errorInfo(), true));
        throw new PDOException("Failed to prepare statement");
    }
    
    $result = $stmt->execute([$id]);
    if (!$result) {
        error_log("Failed to execute statement: " . print_r($stmt->errorInfo(), true));
        throw new PDOException("Failed to execute statement");
    }
    
    $application = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Debug: Log the fetched data
    error_log("Fetched application data: " . print_r($application, true));
    
    if (!$application) {
        error_log("Failed to fetch application details after confirming existence");
        throw new PDOException("Failed to fetch application details");
    }
    
    // Fetch status history
    $stmt = $conn->prepare("
        SELECT 
            sl.*,
            a.admin_name as AdminName
                           FROM status_logs sl
        LEFT JOIN tbladmin a ON sl.admin_id = a.id
        WHERE sl.application_id = ? 
        AND sl.application_type = 'death'
        ORDER BY sl.created_at DESC
    ");
    
    if (!$stmt) {
        error_log("Failed to prepare status history statement: " . print_r($conn->errorInfo(), true));
        throw new PDOException("Failed to prepare status history statement");
    }
    
    $result = $stmt->execute([$id]);
    if (!$result) {
        error_log("Failed to execute status history statement: " . print_r($stmt->errorInfo(), true));
        throw new PDOException("Failed to execute status history statement");
    }
    
    $status_history = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Debug: Log the status history
    error_log("Fetched status history: " . print_r($status_history, true));
    
} catch (PDOException $e) {
    error_log("Database error in admin_view_death_application.php: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    $error = "Failed to fetch application details. Error: " . $e->getMessage();
}

// Debug: Output HTML comment with application data for debugging
echo "<!-- Debug: Application data\n";
echo "ID: " . $id . "\n";
echo "Application: " . print_r($application, true) . "\n";
echo "-->";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Death Application</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f0f8f0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .header {
            background: #81c784;
            padding: 15px 30px;
            color: white;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
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
            margin: 0;
            color: #004d00;
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

        .page-container {
            display: flex;
            margin-top: 70px;
            min-height: calc(100vh - 70px);
        }

        .sidebar {
            width: 250px;
            background-color: #004d00;
            padding: 20px;
            position: fixed;
            left: 0;
            top: 70px;
            bottom: 0;
            overflow-y: auto;
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

        .sidebar a:hover, .sidebar a.active {
            background-color: #1d991f;
            transform: translateX(5px);
        }

        .sidebar a i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }

        .main-content {
            flex: 1;
            margin-left: 250px;
            padding: 20px 30px;
        }

        .back-button {
            margin-bottom: 20px;
        }

        .card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }

        .card-header {
            background: #1d991f;
            color: white;
            padding: 15px 20px;
            border-radius: 10px 10px 0 0;
        }

        .card-body {
            padding: 20px;
        }

        .logout-btn {
            background: #dc3545;
            color: white;
            padding: 8px 15px;
            border-radius: 5px;
            text-decoration: none;
            transition: background-color 0.3s;
        }

        .logout-btn:hover {
            background-color: #c82333;
            color: white;
        }

        @media (max-width: 768px) {
            .header {
                padding: 15px;
            }

            .page-container {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
                position: static;
                margin-top: 0;
            }

            .main-content {
                margin-left: 0;
                padding: 15px;
            }

            .logo-section h1 {
                font-size: 20px;
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

    <div class="page-container">
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
            <a href="admin_view_birth1.php">
                <i class="fas fa-certificate"></i> Birth Certificate Status
            </a>
            <a href="admin_view_death1.php" class="active">
                <i class="fas fa-scroll"></i> Death Certificate Status
            </a>
        </div>

        <div class="main-content">
            <div class="back-button">
                <a href="admin_view_death1.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Applications
        </a>
            </div>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <?php if (isset($application)): ?>
            <div class="card">
                <div class="card-header">
                        <h3 class="mb-0">Death Application Details</h3>
                    </div>
                <div class="card-body">
                    <!-- Application Details -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h4>Application Details</h4>
                                <div>
                                    <a href="edit_death_application.php?id=<?php echo $application['id']; ?>" class="btn btn-primary">
                                        <i class="fas fa-edit"></i> Edit Application
                                    </a>
                                    <?php if ($application['status'] !== 'Approved'): ?>
                                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#approveModal">
                                            <i class="fas fa-check"></i> Approve
                                        </button>
                                    <?php endif; ?>
                                    <?php if ($application['status'] !== 'Rejected'): ?>
                                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
                                            <i class="fas fa-times"></i> Reject
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <form method="POST" class="mb-3">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="status" class="form-label">Status:</label>
                                        <select name="status" id="status" class="form-select" required>
                                        <option value="">Select Status</option>
                                        <option value="Pending" <?php echo $application['status'] == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                        <option value="In Progress" <?php echo $application['status'] == 'In Progress' ? 'selected' : ''; ?>>In Progress</option>
                                        <option value="Approved" <?php echo $application['status'] == 'Approved' ? 'selected' : ''; ?>>Approved</option>
                                        <option value="Rejected" <?php echo $application['status'] == 'Rejected' ? 'selected' : ''; ?>>Rejected</option>
                                    </select>
                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="remark" class="form-label">Remark:</label>
                                        <textarea name="remark" id="remark" class="form-control" rows="1"></textarea>
                                    </div>
                    </div>
                                <button type="submit" class="btn btn-primary">Update Status</button>
                            </form>
                </div>
            </div>

                    <hr>

                    <!-- Basic Application Info -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h4 class="mb-3">Basic Information</h4>
                            <table class="table table-bordered">
                                <tr>
                                    <th class="w-25">Registration No:</th>
                                    <td><?php echo htmlspecialchars($application['registration_no']); ?></td>
                                    <th class="w-25">Current Status:</th>
                                    <td>
                                        <span class="badge bg-<?php 
                                            echo match($application['status']) {
                                                'Pending' => 'warning',
                                                'In Progress' => 'info',
                                                'Approved' => 'success',
                                                'Rejected' => 'danger',
                                                default => 'secondary'
                                            };
                                        ?>">
                                            <?php echo htmlspecialchars($application['status']); ?>
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Application Date:</th>
                                    <td><?php echo htmlspecialchars($application['application_date']); ?></td>
                                    <th>Applicant Relationship:</th>
                                    <td><?php echo htmlspecialchars($application['relationship']); ?></td>
                                </tr>
                            </table>
                </div>
            </div>

                    <!-- Deceased Person Details -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h4 class="mb-3">Deceased Person Details</h4>
                            <table class="table table-bordered">
                                <tr>
                                    <th class="w-25">Name:</th>
                                    <td><?php echo htmlspecialchars($application['name']); ?></td>
                                    <th class="w-25">Date of Birth:</th>
                                    <td><?php echo htmlspecialchars($application['date_of_birth']); ?></td>
                                </tr>
                                <tr>
                                    <th>Date of Death:</th>
                                    <td><?php echo htmlspecialchars($application['date_of_death']); ?></td>
                                    <th>Place of Death:</th>
                                    <td><?php echo htmlspecialchars($application['place_of_death']); ?></td>
                                </tr>
                                <tr>
                                    <th>Age at Death:</th>
                                    <td><?php echo htmlspecialchars($application['age_at_death']); ?></td>
                                    <th>Cause of Death:</th>
                                    <td><?php echo htmlspecialchars($application['cause_of_death']); ?></td>
                                </tr>
                                <tr>
                                    <th>Gender:</th>
                                    <td><?php echo htmlspecialchars($application['gender']); ?></td>
                                    <th>Occupation:</th>
                                    <td><?php echo htmlspecialchars($application['occupation']); ?></td>
                                </tr>
                                <tr>
                                    <th>Nationality:</th>
                                    <td><?php echo htmlspecialchars($application['nationality']); ?></td>
                                    <th>Blood Group:</th>
                                    <td><?php echo htmlspecialchars($application['blood_group']); ?></td>
                                </tr>
                                <tr>
                                    <th>Marital Status:</th>
                                    <td colspan="3"><?php echo htmlspecialchars($application['marital_status']); ?></td>
                                </tr>
                            </table>
                </div>
            </div>

                    <!-- Father's Details -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h4 class="mb-3">Father's Details</h4>
                            <table class="table table-bordered">
                                <tr>
                                    <th class="w-25">Name:</th>
                                    <td><?php echo htmlspecialchars($application['father_name']); ?></td>
                                    <th class="w-25">BRN:</th>
                                    <td><?php echo htmlspecialchars($application['father_brn']); ?></td>
                                </tr>
                                <tr>
                                    <th>NID:</th>
                                    <td><?php echo htmlspecialchars($application['father_nid']); ?></td>
                                    <th>Occupation:</th>
                                    <td><?php echo htmlspecialchars($application['father_occupation']); ?></td>
                                </tr>
                            </table>
                </div>
            </div>

                    <!-- Mother's Details -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h4 class="mb-3">Mother's Details</h4>
                            <table class="table table-bordered">
                                <tr>
                                    <th class="w-25">Name:</th>
                                    <td><?php echo htmlspecialchars($application['mother_name']); ?></td>
                                    <th class="w-25">BRN:</th>
                                    <td><?php echo htmlspecialchars($application['mother_brn']); ?></td>
                                </tr>
                                <tr>
                                    <th>NID:</th>
                                    <td><?php echo htmlspecialchars($application['mother_nid']); ?></td>
                                    <th>Occupation:</th>
                                    <td><?php echo htmlspecialchars($application['mother_occupation']); ?></td>
                                </tr>
                            </table>
            </div>
        </div>

                    <!-- Applicant Details -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h4 class="mb-3">Applicant Details</h4>
                            <table class="table table-bordered">
                                <tr>
                                    <th class="w-25">Name:</th>
                                    <td><?php echo htmlspecialchars($application['UserFirstName'] . ' ' . $application['UserLastName']); ?></td>
                                    <th class="w-25">Email:</th>
                                    <td><?php echo htmlspecialchars($application['UserEmail']); ?></td>
                                </tr>
                                <tr>
                                    <th>Mobile Number:</th>
                                    <td><?php echo htmlspecialchars($application['UserMobileNumber']); ?></td>
                                    <th>NID Number:</th>
                                    <td><?php echo htmlspecialchars($application['nid_number']); ?></td>
                                </tr>
                                <tr>
                                    <th>Permanent Address:</th>
                                    <td colspan="3"><?php echo htmlspecialchars($application['permanent_address']); ?></td>
                                </tr>
                            </table>
                        </div>
                </div>

                    <!-- Location Details -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <h4>Location Details</h4>
                            <table class="table table-bordered">
                                <tr>
                                    <th class="w-25">Division:</th>
                                    <td><?php echo htmlspecialchars($application['division']); ?></td>
                                </tr>
                                <tr>
                                    <th class="w-25">District:</th>
                                    <td><?php echo htmlspecialchars($application['district']); ?></td>
                                </tr>
                                <tr>
                                    <th class="w-25">Upazila:</th>
                                    <td><?php echo htmlspecialchars($application['upazila']); ?></td>
                                </tr>
                                <tr>
                                    <th class="w-25">Union/Pouroshova:</th>
                                    <td><?php echo htmlspecialchars($application['pouroshova']); ?></td>
                                </tr>
                                <tr>
                                    <th class="w-25">Permanent Address:</th>
                                    <td><?php echo nl2br(htmlspecialchars($application['permanent_address'])); ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Documents -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h4 class="mb-3">Supporting Documents</h4>
                            <table class="table table-bordered">
                                <tr>
                                    <th class="w-25">Hospital Paper:</th>
                                    <td colspan="3">
                                        <?php if (!empty($application['hospital_paper'])): ?>
                                            <?php
                                            $file_path = $application['hospital_paper'];
                                            // Check if file exists in multiple possible locations
                                            $exists = file_exists($file_path) || 
                                                     file_exists('uploads/death_documents/' . basename($file_path)) || 
                                                     file_exists('uploads/death/' . basename($file_path));
                                            
                                            if ($exists):
                                            ?>
                                                <a href="download.php?file=<?php echo urlencode(basename($application['hospital_paper'])); ?>&type=death" 
                                                   class="btn btn-sm btn-primary" target="_blank">
                                                    <i class="fas fa-file-medical"></i> View Hospital Paper
                                                </a>
                                                <small class="text-muted ms-2">File: <?php echo htmlspecialchars(basename($application['hospital_paper'])); ?></small>
                                            <?php else: ?>
                                                <span class="text-danger">
                                                    <i class="fas fa-exclamation-circle"></i> File not found
                                                </span>
                                                <small class="text-muted ms-2">Path: <?php echo htmlspecialchars($application['hospital_paper']); ?></small>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="text-muted">No document uploaded</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th class="w-25">NID Document:</th>
                                    <td colspan="3">
                                        <?php if (!empty($application['nid_document'])): ?>
                                            <?php
                                            $file_path = $application['nid_document'];
                                            // Check if file exists in multiple possible locations
                                            $exists = file_exists($file_path) || 
                                                     file_exists('uploads/death_documents/' . basename($file_path)) || 
                                                     file_exists('uploads/death/' . basename($file_path));
                                            
                                            if ($exists):
                                            ?>
                                                <a href="download.php?file=<?php echo urlencode(basename($application['nid_document'])); ?>&type=death" 
                                                   class="btn btn-sm btn-primary" target="_blank">
                                                    <i class="fas fa-id-card"></i> View NID Document
                                                </a>
                                                <small class="text-muted ms-2">File: <?php echo htmlspecialchars(basename($application['nid_document'])); ?></small>
                                            <?php else: ?>
                                                <span class="text-danger">
                                                    <i class="fas fa-exclamation-circle"></i> File not found
                                                </span>
                                                <small class="text-muted ms-2">Path: <?php echo htmlspecialchars($application['nid_document']); ?></small>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="text-muted">No document uploaded</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Status History -->
                    <div class="row">
                        <div class="col-md-12">
                            <h4 class="mb-3">Status History</h4>
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Date</th>
                                        <th>Admin</th>
                                        <th>Old Status</th>
                                        <th>New Status</th>
                                        <th>Remark</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($status_history)): ?>
                                        <tr>
                                            <td colspan="5" class="text-center">No status history available</td>
                                        </tr>
                                    <?php else: ?>
            <?php foreach ($status_history as $history): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($history['created_at']); ?></td>
                                        <td><?php echo htmlspecialchars($history['AdminName']); ?></td>
                                                <td>
                                                    <span class="badge bg-<?php 
                                                        echo match($history['old_status']) {
                                                            'Pending' => 'warning',
                                                            'In Progress' => 'info',
                                                            'Approved' => 'success',
                                                            'Rejected' => 'danger',
                                                            default => 'secondary'
                                                        };
                                                    ?>">
                            <?php echo htmlspecialchars($history['old_status']); ?>
                        </span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-<?php 
                                                        echo match($history['new_status']) {
                                                            'Pending' => 'warning',
                                                            'In Progress' => 'info',
                                                            'Approved' => 'success',
                                                            'Rejected' => 'danger',
                                                            default => 'secondary'
                                                        };
                                                    ?>">
                            <?php echo htmlspecialchars($history['new_status']); ?>
                        </span>
                                                </td>
                                        <td><?php echo htmlspecialchars($history['remark']); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    </div>
                        </div>
                    <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>