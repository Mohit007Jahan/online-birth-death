<?php
session_start();
require_once 'dbconfig.php';

// Debug logging
error_log("Accessed edit_birth_application.php");
error_log("GET parameters: " . print_r($_GET, true));
error_log("Session data: " . print_r($_SESSION, true));

// Initialize CSRF token if not exists
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    error_log("Admin not logged in, redirecting to login");
    header('Location: admin_login.php');
    exit();
}

// Check if application ID is provided
if (!isset($_GET['id'])) {
    error_log("No application ID provided, redirecting to list");
    header('Location: admin_view_birth1.php');
    exit();
}

$application_id = $_GET['id'];
error_log("Processing application ID: " . $application_id);

try {
    // Fetch application details
    $stmt = $conn->prepare("SELECT * FROM tblbirthapplications WHERE id = ?");
    $stmt->execute([$application_id]);
    $application = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$application) {
        $_SESSION['error'] = "Application not found.";
        header('Location: admin_view_birth1.php');
        exit();
    }
} catch (Exception $e) {
    error_log("Error fetching birth application: " . $e->getMessage());
    $_SESSION['error'] = "Error loading application details.";
    header('Location: admin_view_birth1.php');
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    error_log("Processing birth application update for ID: " . $application_id);
    error_log("Form data received: " . print_r($_POST, true));

    try {
        // Verify CSRF token
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            throw new Exception("Invalid form submission.");
        }

        // Regenerate CSRF token
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

        // Sanitize and validate input
        $name = trim($_POST['name']);
        if (empty($name)) {
            throw new Exception("Name is required");
        }

        $relationship = trim($_POST['relationship']);
        if (empty($relationship) || !in_array($relationship, ['Self', 'Father', 'Mother', 'Guardian'])) {
            throw new Exception("Valid relationship is required");
        }

        $date_of_birth = trim($_POST['date_of_birth']);
        if (empty($date_of_birth) || !strtotime($date_of_birth)) {
            throw new Exception("Valid date of birth is required");
        }

        $place_of_birth = trim($_POST['place_of_birth']);
        if (empty($place_of_birth)) {
            throw new Exception("Place of birth is required");
        }

        $gender = trim($_POST['gender']);
        if (!in_array($gender, ['Male', 'Female', 'Other'])) {
            throw new Exception("Valid gender is required");
        }

        $father_name = trim($_POST['father_name']);
        if (empty($father_name)) {
            throw new Exception("Father's name is required");
        }

        $father_brn = trim($_POST['father_brn']);
        if (!empty($father_brn) && !preg_match('/^[0-9]{17}$/', $father_brn)) {
            throw new Exception("Father's BRN must be 17 digits");
        }

        $father_nid = trim($_POST['father_nid']);
        if (!empty($father_nid) && !preg_match('/^[0-9]{10,17}$/', $father_nid)) {
            throw new Exception("Father's NID must be between 10 and 17 digits");
        }

        $father_occupation = trim($_POST['father_occupation']);
        if (empty($father_occupation)) {
            throw new Exception("Father's occupation is required");
        }

        $mother_name = trim($_POST['mother_name']);
        if (empty($mother_name)) {
            throw new Exception("Mother's name is required");
        }

        $mother_brn = trim($_POST['mother_brn']);
        if (!empty($mother_brn) && !preg_match('/^[0-9]{17}$/', $mother_brn)) {
            throw new Exception("Mother's BRN must be 17 digits");
        }

        $mother_nid = trim($_POST['mother_nid']);
        if (!empty($mother_nid) && !preg_match('/^[0-9]{10,17}$/', $mother_nid)) {
            throw new Exception("Mother's NID must be between 10 and 17 digits");
        }

        $mother_occupation = trim($_POST['mother_occupation']);
        if (empty($mother_occupation)) {
            throw new Exception("Mother's occupation is required");
        }

        $nationality = trim($_POST['nationality']);
        if (empty($nationality)) {
            throw new Exception("Nationality is required");
        }

        $blood_group = trim($_POST['blood_group']);
        if (!in_array($blood_group, ['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-'])) {
            throw new Exception("Valid blood group is required");
        }

        $marital_status = trim($_POST['marital_status']);
        if (!in_array($marital_status, ['Single', 'Married', 'Divorced', 'Widowed'])) {
            throw new Exception("Valid marital status is required");
        }

        $permanent_address = trim($_POST['permanent_address']);
        if (empty($permanent_address)) {
            throw new Exception("Permanent address is required");
        }

        $division = trim($_POST['division']);
        $district = trim($_POST['district']);
        $upazila = trim($_POST['upazila']);
        $pouroshova = trim($_POST['pouroshova']);

        if (empty($division) || empty($district) || empty($upazila) || empty($pouroshova)) {
            throw new Exception("All location fields are required");
        }

        $order_of_child = trim($_POST['order_of_child']);
        if (!is_numeric($order_of_child) || $order_of_child < 1) {
            throw new Exception("Valid order of child is required");
        }

        $occupation = trim($_POST['occupation']);
        if (empty($occupation)) {
            throw new Exception("Occupation is required");
        }

        // Handle hospital paper upload
        $hospital_paper = $application['hospital_paper']; // Keep existing value by default
        if (isset($_FILES['hospital_paper']) && $_FILES['hospital_paper']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = 'uploads/hospital_papers/';
            
            // Create directory if it doesn't exist
            if (!file_exists($upload_dir)) {
                if (!mkdir($upload_dir, 0777, true)) {
                    error_log("Failed to create upload directory: " . $upload_dir);
                    throw new Exception("Failed to create upload directory");
                }
                chmod($upload_dir, 0777);
            }

            // Validate file type using mime type
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime_type = finfo_file($finfo, $_FILES['hospital_paper']['tmp_name']);
            finfo_close($finfo);

            $allowed_mime_types = [
                'application/pdf',
                'image/jpeg',
                'image/png'
            ];

            if (!in_array($mime_type, $allowed_mime_types)) {
                error_log("Invalid file type uploaded: " . $mime_type);
                throw new Exception("Invalid file type. Allowed types: PDF, JPG, JPEG, PNG");
            }

            // Get file extension from mime type
            $extensions = [
                'application/pdf' => 'pdf',
                'image/jpeg' => 'jpg',
                'image/png' => 'png'
            ];
            $file_extension = $extensions[$mime_type];

            // Validate file size
            $max_size = 5 * 1024 * 1024; // 5MB
            if ($_FILES['hospital_paper']['size'] > $max_size) {
                error_log("File too large: " . $_FILES['hospital_paper']['size']);
                throw new Exception("File size must be less than 5MB");
            }

            // Delete old file if it exists
            if (!empty($application['hospital_paper'])) {
                $old_file = $upload_dir . $application['hospital_paper'];
                if (file_exists($old_file)) {
                    if (!unlink($old_file)) {
                        error_log("Failed to delete old file: " . $old_file);
                        // Don't throw exception, just log the error
                    }
                }
            }

            // Generate unique filename
            $new_filename = 'hospital_' . uniqid() . '_' . time() . '.' . $file_extension;
            $upload_path = $upload_dir . $new_filename;

            // Move uploaded file
            if (!move_uploaded_file($_FILES['hospital_paper']['tmp_name'], $upload_path)) {
                error_log("Failed to move uploaded file to: " . $upload_path);
                throw new Exception("Failed to save uploaded file");
            }

            // Set file permissions
            chmod($upload_path, 0644);
            
            $hospital_paper = $new_filename;
        } elseif (isset($_FILES['hospital_paper']) && $_FILES['hospital_paper']['error'] !== UPLOAD_ERR_NO_FILE) {
            // Handle other upload errors
            $upload_errors = [
                UPLOAD_ERR_INI_SIZE => "The uploaded file exceeds the upload_max_filesize directive in php.ini",
                UPLOAD_ERR_FORM_SIZE => "The uploaded file exceeds the MAX_FILE_SIZE directive in the HTML form",
                UPLOAD_ERR_PARTIAL => "The uploaded file was only partially uploaded",
                UPLOAD_ERR_NO_TMP_DIR => "Missing a temporary folder",
                UPLOAD_ERR_CANT_WRITE => "Failed to write file to disk",
                UPLOAD_ERR_EXTENSION => "A PHP extension stopped the file upload"
            ];
            $error_message = isset($upload_errors[$_FILES['hospital_paper']['error']]) 
                ? $upload_errors[$_FILES['hospital_paper']['error']] 
                : "Unknown upload error";
            error_log("File upload error: " . $error_message);
            throw new Exception("File upload failed: " . $error_message);
        }

        // Update application
        $sql = "UPDATE `tblbirthapplications` SET 
            `name` = ?, `relationship` = ?, `date_of_birth` = ?, `place_of_birth` = ?, `gender` = ?,
            `father_name` = ?, `father_brn` = ?, `father_nid` = ?, `father_occupation` = ?,
            `mother_name` = ?, `mother_brn` = ?, `mother_nid` = ?, `mother_occupation` = ?,
            `nationality` = ?, `blood_group` = ?, `marital_status` = ?, `permanent_address` = ?,
            `division` = ?, `district` = ?, `upazila` = ?, `pouroshova` = ?,
            `hospital_paper` = ?, `order_of_child` = ?, `occupation` = ?
            WHERE `id` = ?";

        $params = [
            $name, $relationship, $date_of_birth, $place_of_birth, $gender,
            $father_name, $father_brn, $father_nid, $father_occupation,
            $mother_name, $mother_brn, $mother_nid, $mother_occupation,
            $nationality, $blood_group, $marital_status, $permanent_address,
            $division, $district, $upazila, $pouroshova,
            $hospital_paper, $order_of_child, $occupation,
            $application_id
        ];

        // Debug logging
        error_log("SQL Query: " . $sql);
        error_log("Parameters count: " . count($params));
        error_log("All form data: " . print_r($_POST, true));
        error_log("Application data: " . print_r($application, true));
        
        try {
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            error_log("Prepare failed: " . print_r($conn->errorInfo(), true));
            throw new Exception("Database prepare error");
        }
        
        if (!$stmt->execute($params)) {
            error_log("Execute failed: " . print_r($stmt->errorInfo(), true));
                throw new Exception("Database execute error: " . implode(", ", $stmt->errorInfo()));
            }
        } catch (PDOException $e) {
            error_log("PDO Exception: " . $e->getMessage());
            throw new Exception("Database error: " . $e->getMessage());
        }

        $_SESSION['success'] = "Application updated successfully.";
        header('Location: admin_view_birth_application.php?id=' . $application_id);
        exit();
    } catch (Exception $e) {
        error_log("Error updating birth application: " . $e->getMessage());
        $_SESSION['error'] = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Birth Application</title>
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

        .loading {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }

        .loading-content {
            background: white;
            padding: 20px;
            border-radius: 5px;
            text-align: center;
        }

        .spinner-border {
            width: 3rem;
            height: 3rem;
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
            <a href="admin_view_birth1.php" class="active">
                <i class="fas fa-certificate"></i> Birth Certificate Status
            </a>
            <a href="admin_view_death1.php">
                <i class="fas fa-scroll"></i> Death Certificate Status
            </a>
        </div>

        <div class="main-content">
            <!-- Loading overlay -->
    <div class="loading">
        <div class="loading-content">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <div class="mt-2">Saving changes...</div>
        </div>
    </div>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                <?php 
                echo htmlspecialchars($_SESSION['error']);
                unset($_SESSION['error']);
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                <?php 
                echo htmlspecialchars($_SESSION['success']);
                unset($_SESSION['success']);
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (!empty($application)): ?>
        <form action="" method="POST" enctype="multipart/form-data" novalidate>
            <!-- Add CSRF token -->
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
            <!-- Child Details -->
            <div class="card mb-4">
                <div class="card-header">
                    <h4 class="mb-0">Child Details</h4>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label">Child's Name</label>
                            <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($application['name']); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="relationship" class="form-label">Relationship</label>
                            <select class="form-select" id="relationship" name="relationship" required>
                                <option value="">Select Relationship</option>
                                <option value="Self" <?php echo ($application['relationship'] === 'Self') ? 'selected' : ''; ?>>Self</option>
                                <option value="Father" <?php echo ($application['relationship'] === 'Father') ? 'selected' : ''; ?>>Father</option>
                                <option value="Mother" <?php echo ($application['relationship'] === 'Mother') ? 'selected' : ''; ?>>Mother</option>
                                <option value="Guardian" <?php echo ($application['relationship'] === 'Guardian') ? 'selected' : ''; ?>>Guardian</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="date_of_birth" class="form-label">Date of Birth</label>
                            <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" value="<?php echo htmlspecialchars($application['date_of_birth']); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="place_of_birth" class="form-label">Place of Birth</label>
                            <input type="text" class="form-control" id="place_of_birth" name="place_of_birth" value="<?php echo htmlspecialchars($application['place_of_birth']); ?>" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="gender" class="form-label">Gender</label>
                            <select class="form-select" id="gender" name="gender" required>
                                <option value="">Select Gender</option>
                                <option value="Male" <?php echo ($application['gender'] === 'Male') ? 'selected' : ''; ?>>Male</option>
                                <option value="Female" <?php echo ($application['gender'] === 'Female') ? 'selected' : ''; ?>>Female</option>
                                <option value="Other" <?php echo ($application['gender'] === 'Other') ? 'selected' : ''; ?>>Other</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="nationality" class="form-label">Nationality</label>
                            <input type="text" class="form-control" id="nationality" name="nationality" value="<?php echo htmlspecialchars($application['nationality']); ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label for="blood_group" class="form-label">Blood Group</label>
                            <select class="form-select" id="blood_group" name="blood_group" required>
                                <option value="">Select Blood Group</option>
                                <?php
                                $blood_groups = ['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-'];
                                foreach ($blood_groups as $bg) {
                                    echo '<option value="' . $bg . '"' . ($application['blood_group'] === $bg ? ' selected' : '') . '>' . $bg . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="marital_status" class="form-label">Marital Status</label>
                            <select class="form-select" id="marital_status" name="marital_status" required>
                                <option value="">Select Marital Status</option>
                                <option value="Single" <?php echo ($application['marital_status'] === 'Single') ? 'selected' : ''; ?>>Single</option>
                                <option value="Married" <?php echo ($application['marital_status'] === 'Married') ? 'selected' : ''; ?>>Married</option>
                                <option value="Divorced" <?php echo ($application['marital_status'] === 'Divorced') ? 'selected' : ''; ?>>Divorced</option>
                                <option value="Widowed" <?php echo ($application['marital_status'] === 'Widowed') ? 'selected' : ''; ?>>Widowed</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="order_of_child" class="form-label">Order of Child</label>
                            <input type="number" class="form-control" id="order_of_child" name="order_of_child" value="<?php echo htmlspecialchars($application['order_of_child']); ?>" min="1" required>
                        </div>
                        <div class="col-md-4">
                            <label for="occupation" class="form-label">Occupation</label>
                            <input type="text" class="form-control" id="occupation" name="occupation" value="<?php echo htmlspecialchars($application['occupation']); ?>" required>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Parents Details -->
            <div class="card mb-4">
                <div class="card-header">
                    <h4 class="mb-0">Parents Details</h4>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="father_name" class="form-label">Father's Name</label>
                            <input type="text" class="form-control" id="father_name" name="father_name" value="<?php echo htmlspecialchars($application['father_name']); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="father_occupation" class="form-label">Father's Occupation</label>
                            <input type="text" class="form-control" id="father_occupation" name="father_occupation" value="<?php echo htmlspecialchars($application['father_occupation']); ?>" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="father_brn" class="form-label">Father's BRN</label>
                            <input type="text" class="form-control" id="father_brn" name="father_brn" value="<?php echo htmlspecialchars($application['father_brn']); ?>" pattern="[0-9]{17}" title="BRN must be 17 digits">
                        </div>
                        <div class="col-md-6">
                            <label for="father_nid" class="form-label">Father's NID</label>
                            <input type="text" class="form-control" id="father_nid" name="father_nid" value="<?php echo htmlspecialchars($application['father_nid']); ?>" pattern="[0-9]{10,17}" title="NID must be between 10 and 17 digits">
                </div>
            </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="mother_name" class="form-label">Mother's Name</label>
                            <input type="text" class="form-control" id="mother_name" name="mother_name" value="<?php echo htmlspecialchars($application['mother_name']); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="mother_occupation" class="form-label">Mother's Occupation</label>
                            <input type="text" class="form-control" id="mother_occupation" name="mother_occupation" value="<?php echo htmlspecialchars($application['mother_occupation']); ?>" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="mother_brn" class="form-label">Mother's BRN</label>
                            <input type="text" class="form-control" id="mother_brn" name="mother_brn" value="<?php echo htmlspecialchars($application['mother_brn']); ?>" pattern="[0-9]{17}" title="BRN must be 17 digits">
                        </div>
                        <div class="col-md-6">
                            <label for="mother_nid" class="form-label">Mother's NID</label>
                            <input type="text" class="form-control" id="mother_nid" name="mother_nid" value="<?php echo htmlspecialchars($application['mother_nid']); ?>" pattern="[0-9]{10,17}" title="NID must be between 10 and 17 digits">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Location Details -->
            <div class="card mb-4">
                <div class="card-header">
                    <h4 class="mb-0">Location Details</h4>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="division" class="form-label">Division</label>
                            <select class="form-select" id="division" name="division" required>
                                <option value="">Select Division</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="district" class="form-label">District</label>
                            <select class="form-select" id="district" name="district" required disabled>
                                <option value="">Select District</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="upazila" class="form-label">Upazila</label>
                            <select class="form-select" id="upazila" name="upazila" required disabled>
                                <option value="">Select Upazila</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="pouroshova" class="form-label">Union/Pouroshova</label>
                            <select class="form-select" id="pouroshova" name="pouroshova" required disabled>
                                <option value="">Select Union/Pouroshova</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-12">
                            <label for="permanent_address" class="form-label">Permanent Address</label>
                            <textarea class="form-control" id="permanent_address" name="permanent_address" rows="3" required><?php echo htmlspecialchars($application['permanent_address']); ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Documents -->
            <div class="card mb-4">
                <div class="card-header">
                    <h4 class="mb-0">Documents</h4>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-12">
                            <label for="hospital_paper" class="form-label">Hospital Paper</label>
                            <input type="file" class="form-control" id="hospital_paper" name="hospital_paper" accept=".pdf,.jpg,.jpeg,.png">
                            <?php if (!empty($application['hospital_paper'])): ?>
                                <div class="mt-2">
                                    <p>Current file: 
                                        <a href="download.php?file=<?php echo urlencode(basename($application['hospital_paper'])); ?>&type=hospital" 
                                           class="btn btn-sm btn-info" target="_blank">
                                            <i class="fas fa-file-medical"></i> View Current Document
                                        </a>
                                    </p>
                                    <small class="text-muted d-block">File path: <?php echo htmlspecialchars($application['hospital_paper']); ?></small>
                                    <?php
                                    // Check if the file path already includes the upload directory
                                    $file_path = $application['hospital_paper'];
                                    if (strpos($file_path, 'uploads/hospital_papers/') !== 0) {
                                        $file_path = 'uploads/hospital_papers/' . $file_path;
                                    }
                                    if (file_exists($file_path)) {
                                        echo '<small class="text-success d-block">File exists at: ' . htmlspecialchars($file_path) . '</small>';
                                    } else {
                                        echo '<small class="text-danger d-block">File not found at: ' . htmlspecialchars($file_path) . '</small>';
                                    }
                                    ?>
                                </div>
                            <?php endif; ?>
                            <small class="text-muted">Upload new file only if you want to change the existing one. Accepted formats: PDF, JPG, JPEG, PNG (Max size: 5MB)</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-center mb-4">
                <a href="admin_view_birth_application.php?id=<?php echo $application_id; ?>" class="btn btn-secondary me-2">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save Changes
                </button>
            </div>
        </form>
        <?php endif; ?>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="assets/js/locations.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Check if bangladeshData is loaded
            if (typeof bangladeshData === 'undefined') {
                console.error('Location data not loaded');
                // Show error message
                const locationCards = document.querySelectorAll('.card');
                locationCards.forEach(card => {
                    if (card.querySelector('.card-header').textContent.includes('Location')) {
                        const alert = document.createElement('div');
                        alert.className = 'alert alert-danger mt-3';
                        alert.textContent = 'Error loading location data. Please refresh the page.';
                        card.querySelector('.card-body').prepend(alert);
                    }
                });
                return;
            }

            // Initialize location dropdowns
            const divisionSelect = document.getElementById('division');
            const districtSelect = document.getElementById('district');
            const upazilaSelect = document.getElementById('upazila');
            const unionSelect = document.getElementById('pouroshova');

            // Get initial values
            const initialDivision = '<?php echo htmlspecialchars($application['division']); ?>';
            const initialDistrict = '<?php echo htmlspecialchars($application['district']); ?>';
            const initialUpazila = '<?php echo htmlspecialchars($application['upazila']); ?>';
            const initialUnion = '<?php echo htmlspecialchars($application['pouroshova']); ?>';

            // Populate divisions
            for (const division in bangladeshData) {
                const option = new Option(division, division);
                divisionSelect.add(option);
                if (division === initialDivision) {
                    option.selected = true;
                }
            }

            // Event listeners
            divisionSelect.addEventListener('change', loadDistricts);
            districtSelect.addEventListener('change', loadUpazilas);
            upazilaSelect.addEventListener('change', loadUnions);

            function loadDistricts() {
                const selectedDivision = divisionSelect.value;
                districtSelect.innerHTML = '<option value="">Select District</option>';
                upazilaSelect.innerHTML = '<option value="">Select Upazila</option>';
                unionSelect.innerHTML = '<option value="">Select Union/Pouroshova</option>';

                if (selectedDivision && bangladeshData[selectedDivision]) {
                    districtSelect.disabled = false;
                    const districts = Object.keys(bangladeshData[selectedDivision].districts);
                    districts.sort().forEach(district => {
                        const option = new Option(district, district);
                        districtSelect.add(option);
                        if (district === initialDistrict && selectedDivision === initialDivision) {
                            option.selected = true;
                            loadUpazilas();
                        }
                    });
                } else {
                    districtSelect.disabled = true;
                    upazilaSelect.disabled = true;
                    unionSelect.disabled = true;
                }
            }

            function loadUpazilas() {
                const selectedDivision = divisionSelect.value;
                const selectedDistrict = districtSelect.value;
                upazilaSelect.innerHTML = '<option value="">Select Upazila</option>';
                unionSelect.innerHTML = '<option value="">Select Union/Pouroshova</option>';

                if (selectedDistrict && bangladeshData[selectedDivision].districts[selectedDistrict]) {
                    upazilaSelect.disabled = false;
                    const upazilas = Object.keys(bangladeshData[selectedDivision].districts[selectedDistrict].upazilas);
                    upazilas.sort().forEach(upazila => {
                        const option = new Option(upazila, upazila);
                        upazilaSelect.add(option);
                        if (upazila === initialUpazila && selectedDistrict === initialDistrict && selectedDivision === initialDivision) {
                            option.selected = true;
                            loadUnions();
                        }
                    });
                } else {
                    upazilaSelect.disabled = true;
                    unionSelect.disabled = true;
                }
            }

            function loadUnions() {
                const selectedDivision = divisionSelect.value;
                const selectedDistrict = districtSelect.value;
                const selectedUpazila = upazilaSelect.value;
                unionSelect.innerHTML = '<option value="">Select Union/Pouroshova</option>';

                if (selectedUpazila && bangladeshData[selectedDivision].districts[selectedDistrict].upazilas[selectedUpazila]) {
                    unionSelect.disabled = false;
                    const unions = bangladeshData[selectedDivision].districts[selectedDistrict].upazilas[selectedUpazila];
                    unions.sort().forEach(union => {
                        const option = new Option(union, union);
                        unionSelect.add(option);
                        if (union === initialUnion && selectedUpazila === initialUpazila && selectedDistrict === initialDistrict && selectedDivision === initialDivision) {
                            option.selected = true;
                        }
                    });
                } else {
                    unionSelect.disabled = true;
                }
            }

            // Initialize dropdowns if initial division exists
            if (initialDivision) {
                loadDistricts();
            }

            const form = document.querySelector('form');
            const loading = document.querySelector('.loading');
            
            form.addEventListener('submit', function(e) {
                // Always prevent the default form submission first
                e.preventDefault();

                let isValid = true;
                let firstInvalid = null;

                // Reset previous validation
                form.querySelectorAll('.is-invalid').forEach(el => {
                    el.classList.remove('is-invalid');
                });
                form.querySelectorAll('.error-feedback').forEach(el => {
                    el.remove();
                });

                // Validate required fields
                form.querySelectorAll('[required]').forEach(field => {
                    if (!field.value.trim()) {
                        isValid = false;
                        field.classList.add('is-invalid');
                        const feedback = document.createElement('div');
                        feedback.className = 'error-feedback';
                        feedback.textContent = 'This field is required';
                        field.parentNode.appendChild(feedback);
                        if (!firstInvalid) firstInvalid = field;
                    }
                });

                // Validate BRN format
                ['father_brn', 'mother_brn'].forEach(id => {
                    const field = document.getElementById(id);
                    if (field.value && !/^[0-9]{17}$/.test(field.value)) {
                        isValid = false;
                        field.classList.add('is-invalid');
                        const feedback = document.createElement('div');
                        feedback.className = 'error-feedback';
                        feedback.textContent = 'BRN must be 17 digits';
                        field.parentNode.appendChild(feedback);
                        if (!firstInvalid) firstInvalid = field;
                    }
                });

                // Validate NID format
                ['father_nid', 'mother_nid'].forEach(id => {
                    const field = document.getElementById(id);
                    if (field.value && !/^[0-9]{10,17}$/.test(field.value)) {
                        isValid = false;
                        field.classList.add('is-invalid');
                        const feedback = document.createElement('div');
                        feedback.className = 'error-feedback';
                        feedback.textContent = 'NID must be between 10 and 17 digits';
                        field.parentNode.appendChild(feedback);
                        if (!firstInvalid) firstInvalid = field;
                    }
                });

                // Validate date of birth
                const dobField = document.getElementById('date_of_birth');
                const dob = new Date(dobField.value);
                const today = new Date();
                if (dob > today) {
                    isValid = false;
                    dobField.classList.add('is-invalid');
                    const feedback = document.createElement('div');
                    feedback.className = 'error-feedback';
                    feedback.textContent = 'Date of birth cannot be in the future';
                    dobField.parentNode.appendChild(feedback);
                    if (!firstInvalid) firstInvalid = dobField;
                }

                // Validate order of child
                const orderField = document.getElementById('order_of_child');
                if (orderField.value && (!Number.isInteger(Number(orderField.value)) || Number(orderField.value) < 1)) {
                    isValid = false;
                    orderField.classList.add('is-invalid');
                    const feedback = document.createElement('div');
                    feedback.className = 'error-feedback';
                    feedback.textContent = 'Order of child must be a positive integer';
                    orderField.parentNode.appendChild(feedback);
                    if (!firstInvalid) firstInvalid = orderField;
                }

                // Validate file upload
                const fileInput = document.getElementById('hospital_paper');
                if (fileInput.files.length > 0) {
                    const file = fileInput.files[0];
                    const allowedTypes = ['application/pdf', 'image/jpeg', 'image/png'];
                    const maxSize = 5 * 1024 * 1024; // 5MB

                    if (!allowedTypes.includes(file.type)) {
                        isValid = false;
                        fileInput.classList.add('is-invalid');
                        const feedback = document.createElement('div');
                        feedback.className = 'error-feedback';
                        feedback.textContent = 'File must be PDF, JPG, or PNG';
                        fileInput.parentNode.appendChild(feedback);
                        if (!firstInvalid) firstInvalid = fileInput;
                    }

                    if (file.size > maxSize) {
                        isValid = false;
                        fileInput.classList.add('is-invalid');
                        const feedback = document.createElement('div');
                        feedback.className = 'error-feedback';
                        feedback.textContent = 'File size must be less than 5MB';
                        fileInput.parentNode.appendChild(feedback);
                        if (!firstInvalid) firstInvalid = fileInput;
                    }
                }

                if (!isValid) {
                    firstInvalid.focus();
                    window.scrollTo(0, firstInvalid.offsetTop - 100);
                    return;
                }

                // Show confirmation popup
                Swal.fire({
                    title: 'Confirm Changes',
                    text: 'Are you sure you want to save these changes?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#1d991f',
                    cancelButtonColor: '#dc3545',
                    confirmButtonText: 'Yes, save changes',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Show loading overlay
                        loading.style.display = 'flex';
                        // Submit the form
                        form.submit();
                    }
                });
            });
        });
    </script>
</body>
</html> 