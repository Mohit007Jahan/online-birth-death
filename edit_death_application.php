<?php
session_start();
require_once 'dbconfig.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit();
}

// Check if application ID is provided
if (!isset($_GET['id'])) {
    header('Location: admin_view_death1.php');
    exit();
}

$application_id = $_GET['id'];
$error = '';
$success = '';

// Fetch application details
try {
    $stmt = $conn->prepare("SELECT * FROM tbldeathapplications WHERE id = ?");
    $stmt->execute([$application_id]);
    $application = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$application) {
        header('Location: admin_view_death1.php');
        exit();
    }

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        try {
            // Start transaction
            $conn->beginTransaction();

            // Validate and sanitize inputs
            $name = trim($_POST['name']);
            $date_of_birth = trim($_POST['date_of_birth']);
            $date_of_death = trim($_POST['date_of_death']);
            $age_at_death = trim($_POST['age_at_death']);
            $gender = trim($_POST['gender']);
            $occupation = trim($_POST['occupation']);
            $father_name = trim($_POST['father_name']);
            $father_brn = trim($_POST['father_brn']);
            $father_nid = trim($_POST['father_nid']);
            $father_occupation = trim($_POST['father_occupation']);
            $mother_name = trim($_POST['mother_name']);
            $mother_brn = trim($_POST['mother_brn']);
            $mother_nid = trim($_POST['mother_nid']);
            $mother_occupation = trim($_POST['mother_occupation']);
            $nationality = trim($_POST['nationality']);
            $blood_group = trim($_POST['blood_group']);
            $division = trim($_POST['division']);
            $district = trim($_POST['district']);
            $upazila = trim($_POST['upazila']);
            $pouroshova = trim($_POST['pouroshova']);
            $permanent_address = trim($_POST['permanent_address']);
            $marital_status = trim($_POST['marital_status']);

            // Debug log
            error_log("Processing death application update for ID: " . $application_id);
            error_log("Form data received: " . print_r($_POST, true));

            // Handle file uploads
            $nid_document = $application['nid_document'];
            $hospital_paper = $application['hospital_paper'];

            // Handle NID document upload
            if (isset($_FILES['nid_document']) && $_FILES['nid_document']['error'] === UPLOAD_ERR_OK) {
                $upload_dir = 'uploads/death_documents/';
                
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
                $mime_type = finfo_file($finfo, $_FILES['nid_document']['tmp_name']);
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
                if ($_FILES['nid_document']['size'] > $max_size) {
                    error_log("File too large: " . $_FILES['nid_document']['size']);
                    throw new Exception("File size must be less than 5MB");
                }

                // Delete old file if it exists
                if (!empty($application['nid_document'])) {
                    $old_file = $application['nid_document'];
                    if (file_exists($old_file)) {
                        unlink($old_file);
                    } else if (file_exists('uploads/death_documents/' . basename($old_file))) {
                        unlink('uploads/death_documents/' . basename($old_file));
                    } else if (file_exists('uploads/death/' . basename($old_file))) {
                        unlink('uploads/death/' . basename($old_file));
                    }
                }

                // Generate unique filename
                $new_filename = 'nid_' . uniqid() . '_' . time() . '.' . $file_extension;
                $upload_path = $upload_dir . $new_filename;

                // Move uploaded file
                if (!move_uploaded_file($_FILES['nid_document']['tmp_name'], $upload_path)) {
                    error_log("Failed to move uploaded file to: " . $upload_path);
                    throw new Exception("Failed to save uploaded file");
                }

                // Set file permissions
                chmod($upload_path, 0644);
                
                $nid_document = $upload_path;
            } elseif (isset($_FILES['nid_document']) && $_FILES['nid_document']['error'] !== UPLOAD_ERR_NO_FILE) {
                // Handle other upload errors
                $upload_errors = [
                    UPLOAD_ERR_INI_SIZE => "The uploaded file exceeds the upload_max_filesize directive in php.ini",
                    UPLOAD_ERR_FORM_SIZE => "The uploaded file exceeds the MAX_FILE_SIZE directive in the HTML form",
                    UPLOAD_ERR_PARTIAL => "The uploaded file was only partially uploaded",
                    UPLOAD_ERR_NO_TMP_DIR => "Missing a temporary folder",
                    UPLOAD_ERR_CANT_WRITE => "Failed to write file to disk",
                    UPLOAD_ERR_EXTENSION => "A PHP extension stopped the file upload"
                ];
                $error_message = isset($upload_errors[$_FILES['nid_document']['error']]) 
                    ? $upload_errors[$_FILES['nid_document']['error']] 
                    : "Unknown upload error";
                error_log("File upload error: " . $error_message);
                throw new Exception("File upload failed: " . $error_message);
            }

            // Handle hospital paper upload
            if (isset($_FILES['hospital_paper']) && $_FILES['hospital_paper']['error'] === UPLOAD_ERR_OK) {
                $upload_dir = 'uploads/death_documents/';
                
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
                    $old_file = $application['hospital_paper'];
                    if (file_exists($old_file)) {
                        unlink($old_file);
                    } else if (file_exists('uploads/death_documents/' . basename($old_file))) {
                        unlink('uploads/death_documents/' . basename($old_file));
                    } else if (file_exists('uploads/death/' . basename($old_file))) {
                        unlink('uploads/death/' . basename($old_file));
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
                
                $hospital_paper = $upload_path;
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
            $sql = "UPDATE tbldeathapplications SET 
                name = ?, date_of_birth = ?, date_of_death = ?, age_at_death = ?, gender = ?,
                father_name = ?, father_brn = ?, father_nid = ?, father_occupation = ?, 
                mother_name = ?, mother_brn = ?, mother_nid = ?, mother_occupation = ?, 
                nationality = ?, blood_group = ?, division = ?, district = ?, 
                upazila = ?, pouroshova = ?, permanent_address = ?,
                nid_document = ?, hospital_paper = ?, occupation = ?, marital_status = ?
                WHERE id = ?";

            $stmt = $conn->prepare($sql);
            
            $params = [
                $name, $date_of_birth, $date_of_death, $age_at_death, $gender,
                $father_name, $father_brn, $father_nid, $father_occupation, 
                $mother_name, $mother_brn, $mother_nid, $mother_occupation, 
                $nationality, $blood_group, $division, $district, 
                $upazila, $pouroshova, $permanent_address,
                $nid_document, $hospital_paper, $occupation, $marital_status,
                $application_id
            ];

            error_log("Executing update with parameters: " . print_r($params, true));

            if ($stmt->execute($params)) {
                // Commit transaction
                $conn->commit();
                $_SESSION['success'] = "Application updated successfully!";
                error_log("Death application updated successfully for ID: " . $application_id);
                // Redirect to prevent form resubmission
                header("Location: admin_view_death_application.php?id=" . $application_id);
                exit();
            } else {
                $conn->rollBack();
                $errorInfo = $stmt->errorInfo();
                error_log("SQL Error: " . print_r($errorInfo, true));
                $_SESSION['error'] = "Error updating application. SQL Error: " . $errorInfo[2];
            }
        } catch (PDOException $e) {
            $conn->rollBack();
            error_log("Database error: " . $e->getMessage());
            $_SESSION['error'] = "A database error occurred. Please try again later.";
        } catch (Exception $e) {
            $conn->rollBack();
            error_log("General error: " . $e->getMessage());
            $_SESSION['error'] = $e->getMessage();
        }
    }
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    $error = "A database error occurred. Please try again later.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Death Application</title>
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
            <a href="admin_view_birth1.php">
                <i class="fas fa-certificate"></i> Birth Certificate Status
            </a>
            <a href="admin_view_death1.php" class="active">
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
        <div class="card">
            <div class="card-header">
                <h3>Edit Death Application</h3>
            </div>
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                    <!-- Deceased Details -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <h4 class="mb-3">Deceased Details</h4>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label">Deceased's Name:</label>
                                    <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($application['name']); ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="date_of_birth" class="form-label">Date of Birth:</label>
                                    <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" value="<?php echo htmlspecialchars($application['date_of_birth']); ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="date_of_death" class="form-label">Date of Death:</label>
                                    <input type="date" class="form-control" id="date_of_death" name="date_of_death" value="<?php echo htmlspecialchars($application['date_of_death']); ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="age_at_death" class="form-label">Age at Death:</label>
                                    <input type="number" class="form-control" id="age_at_death" name="age_at_death" value="<?php echo htmlspecialchars($application['age_at_death']); ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="gender" class="form-label">Gender:</label>
                                    <select class="form-select" id="gender" name="gender" required>
                                        <option value="">Select Gender</option>
                                        <option value="Male" <?php echo $application['gender'] === 'Male' ? 'selected' : ''; ?>>Male</option>
                                        <option value="Female" <?php echo $application['gender'] === 'Female' ? 'selected' : ''; ?>>Female</option>
                                        <option value="Other" <?php echo $application['gender'] === 'Other' ? 'selected' : ''; ?>>Other</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="occupation" class="form-label">Occupation:</label>
                                    <input type="text" class="form-control" id="occupation" name="occupation" value="<?php echo htmlspecialchars($application['occupation']); ?>" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Father's Details -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <h4 class="mb-3">Father's Details</h4>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="father_name" class="form-label">Father's Name:</label>
                                    <input type="text" class="form-control" id="father_name" name="father_name" value="<?php echo htmlspecialchars($application['father_name']); ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="father_brn" class="form-label">Father's BRN:</label>
                                    <input type="text" class="form-control" id="father_brn" name="father_brn" value="<?php echo htmlspecialchars($application['father_brn']); ?>" pattern="[0-9]{17}" title="BRN must be 17 digits">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="father_nid" class="form-label">Father's NID:</label>
                                    <input type="text" class="form-control" id="father_nid" name="father_nid" value="<?php echo htmlspecialchars($application['father_nid']); ?>" pattern="[0-9]{10,17}" title="NID must be between 10 and 17 digits">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="father_occupation" class="form-label">Father's Occupation:</label>
                                    <input type="text" class="form-control" id="father_occupation" name="father_occupation" value="<?php echo htmlspecialchars($application['father_occupation']); ?>" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Mother's Details -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <h4 class="mb-3">Mother's Details</h4>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="mother_name" class="form-label">Mother's Name:</label>
                                    <input type="text" class="form-control" id="mother_name" name="mother_name" value="<?php echo htmlspecialchars($application['mother_name']); ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="mother_brn" class="form-label">Mother's BRN:</label>
                                    <input type="text" class="form-control" id="mother_brn" name="mother_brn" value="<?php echo htmlspecialchars($application['mother_brn']); ?>" pattern="[0-9]{17}" title="BRN must be 17 digits">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="mother_nid" class="form-label">Mother's NID:</label>
                                    <input type="text" class="form-control" id="mother_nid" name="mother_nid" value="<?php echo htmlspecialchars($application['mother_nid']); ?>" pattern="[0-9]{10,17}" title="NID must be between 10 and 17 digits">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="mother_occupation" class="form-label">Mother's Occupation:</label>
                                    <input type="text" class="form-control" id="mother_occupation" name="mother_occupation" value="<?php echo htmlspecialchars($application['mother_occupation']); ?>" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Details -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <h4 class="mb-3">Additional Details</h4>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="nationality" class="form-label">Nationality:</label>
                                    <input type="text" class="form-control" id="nationality" name="nationality" value="<?php echo htmlspecialchars($application['nationality']); ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="blood_group" class="form-label">Blood Group:</label>
                                    <select class="form-select" id="blood_group" name="blood_group" required>
                                        <option value="">Select Blood Group</option>
                                        <option value="A+" <?php echo $application['blood_group'] === 'A+' ? 'selected' : ''; ?>>A+</option>
                                        <option value="A-" <?php echo $application['blood_group'] === 'A-' ? 'selected' : ''; ?>>A-</option>
                                        <option value="B+" <?php echo $application['blood_group'] === 'B+' ? 'selected' : ''; ?>>B+</option>
                                        <option value="B-" <?php echo $application['blood_group'] === 'B-' ? 'selected' : ''; ?>>B-</option>
                                        <option value="AB+" <?php echo $application['blood_group'] === 'AB+' ? 'selected' : ''; ?>>AB+</option>
                                        <option value="AB-" <?php echo $application['blood_group'] === 'AB-' ? 'selected' : ''; ?>>AB-</option>
                                        <option value="O+" <?php echo $application['blood_group'] === 'O+' ? 'selected' : ''; ?>>O+</option>
                                        <option value="O-" <?php echo $application['blood_group'] === 'O-' ? 'selected' : ''; ?>>O-</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="marital_status" class="form-label">Marital Status:</label>
                                    <select class="form-select" id="marital_status" name="marital_status" required>
                                        <option value="">Select Marital Status</option>
                                        <option value="Single" <?php echo $application['marital_status'] === 'Single' ? 'selected' : ''; ?>>Single</option>
                                        <option value="Married" <?php echo $application['marital_status'] === 'Married' ? 'selected' : ''; ?>>Married</option>
                                        <option value="Divorced" <?php echo $application['marital_status'] === 'Divorced' ? 'selected' : ''; ?>>Divorced</option>
                                        <option value="Widowed" <?php echo $application['marital_status'] === 'Widowed' ? 'selected' : ''; ?>>Widowed</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Location Details -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <h4 class="mb-3">Location Details</h4>
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
                                    <label for="permanent_address" class="form-label">Permanent Address:</label>
                                    <textarea class="form-control" id="permanent_address" name="permanent_address" rows="3" required><?php echo htmlspecialchars($application['permanent_address']); ?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Documents -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <h4 class="mb-3">Documents</h4>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="nid_document" class="form-label">NID Document:</label>
                                    <?php if (!empty($application['nid_document'])): ?>
                                        <div class="mb-2">
                                            <a href="download.php?file=<?php echo urlencode($application['nid_document']); ?>&type=death" 
                                               class="btn btn-sm btn-info" target="_blank">
                                                <i class="fas fa-file-alt"></i> View Current Document
                                            </a>
                                        </div>
                                        <small class="text-muted d-block">File path: <?php echo htmlspecialchars($application['nid_document']); ?></small>
                                        <?php
                                        $file_path = $application['nid_document'];
                                        // Check if file exists in either old or new location
                                        $exists = file_exists($file_path) || 
                                                 file_exists('uploads/death_documents/' . basename($file_path)) || 
                                                 file_exists('uploads/death/' . basename($file_path));
                                        
                                        if ($exists) {
                                            echo '<small class="text-success d-block">File exists</small>';
                                        } else {
                                            echo '<small class="text-danger d-block">File not found</small>';
                                        }
                                        ?>
                                    <?php endif; ?>
                                    <input type="file" class="form-control" id="nid_document" name="nid_document" accept=".pdf,.jpg,.jpeg,.png">
                                    <small class="text-muted">Upload new file only if you want to change the current document. Accepted formats: PDF, JPG, JPEG, PNG (Max size: 5MB)</small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="hospital_paper" class="form-label">Hospital Paper:</label>
                                    <?php if (!empty($application['hospital_paper'])): ?>
                                        <div class="mb-2">
                                            <a href="download.php?file=<?php echo urlencode($application['hospital_paper']); ?>&type=death" 
                                               class="btn btn-sm btn-info" target="_blank">
                                                <i class="fas fa-file-alt"></i> View Current Document
                                            </a>
                                        </div>
                                        <small class="text-muted d-block">File path: <?php echo htmlspecialchars($application['hospital_paper']); ?></small>
                                        <?php
                                        $file_path = $application['hospital_paper'];
                                        // Check if file exists in either old or new location
                                        $exists = file_exists($file_path) || 
                                                 file_exists('uploads/death_documents/' . basename($file_path)) || 
                                                 file_exists('uploads/death/' . basename($file_path));
                                        
                                        if ($exists) {
                                            echo '<small class="text-success d-block">File exists</small>';
                                        } else {
                                            echo '<small class="text-danger d-block">File not found</small>';
                                        }
                                        ?>
                                    <?php endif; ?>
                                    <input type="file" class="form-control" id="hospital_paper" name="hospital_paper" accept=".pdf,.jpg,.jpeg,.png">
                                    <small class="text-muted">Upload new file only if you want to change the current document. Accepted formats: PDF, JPG, JPEG, PNG (Max size: 5MB)</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save Changes
                        </button>
                    </div>
                </form>
            </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="assets/js/locations.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
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

            // Validate date of birth and death
            const dobField = document.getElementById('date_of_birth');
            const dodField = document.getElementById('date_of_death');
            const dob = new Date(dobField.value);
            const dod = new Date(dodField.value);
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

            if (dod > today) {
                isValid = false;
                dodField.classList.add('is-invalid');
                const feedback = document.createElement('div');
                feedback.className = 'error-feedback';
                feedback.textContent = 'Date of death cannot be in the future';
                dodField.parentNode.appendChild(feedback);
                if (!firstInvalid) firstInvalid = dodField;
            }

            if (dod < dob) {
                isValid = false;
                dodField.classList.add('is-invalid');
                const feedback = document.createElement('div');
                feedback.className = 'error-feedback';
                feedback.textContent = 'Date of death cannot be before date of birth';
                dodField.parentNode.appendChild(feedback);
                if (!firstInvalid) firstInvalid = dodField;
            }

            // Validate age at death
            const ageField = document.getElementById('age_at_death');
            if (ageField.value && (!Number.isInteger(Number(ageField.value)) || Number(ageField.value) < 0)) {
                isValid = false;
                ageField.classList.add('is-invalid');
                const feedback = document.createElement('div');
                feedback.className = 'error-feedback';
                feedback.textContent = 'Age must be a non-negative integer';
                ageField.parentNode.appendChild(feedback);
                if (!firstInvalid) firstInvalid = ageField;
            }

            // Validate file uploads
            ['nid_document', 'hospital_paper'].forEach(id => {
                const fileInput = document.getElementById(id);
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
            });

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

        // Initialize location dropdowns
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
        const initialDivision = '<?php echo addslashes($application['division']); ?>';
        const initialDistrict = '<?php echo addslashes($application['district']); ?>';
        const initialUpazila = '<?php echo addslashes($application['upazila']); ?>';
        const initialUnion = '<?php echo addslashes($application['pouroshova']); ?>';

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
                if (Array.isArray(unions)) {
                    unions.sort().forEach(union => {
                        const option = new Option(union, union);
                        unionSelect.add(option);
                        if (union === initialUnion && selectedUpazila === initialUpazila && selectedDistrict === initialDistrict && selectedDivision === initialDivision) {
                            option.selected = true;
                        }
                    });
                }
            } else {
                unionSelect.disabled = true;
            }
        }

        // Initialize dropdowns if initial division exists
        if (initialDivision) {
            loadDistricts();
        }
    });
    </script>
</body>
</html> 