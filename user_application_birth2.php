<?php
session_start();
require_once 'dbconfig.php';

// Check if user is logged in
if (!isset($_SESSION['uid'])) {
    header("Location: user_login.php");
    exit();
}

// Get the relationship from previous page or set default
$relationship = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['relationship'])) {
        $relationship = $_POST['relationship'];
    }
    
    // Only process form if there's actual form data
    if (isset($_POST['name'])) {
        try {
            // Debug: Log all POST and FILES data
            error_log("POST data received: " . print_r($_POST, true));
            error_log("FILES data received: " . print_r($_FILES, true));

            // Validate required fields
            $required_fields = ['name', 'nationality', 'blood_group', 'marital_status', 
                              'occupation', 'date_of_birth', 'place_of_birth', 'gender',
                              'father_name', 'father_brn', 'father_nid', 'father_occupation',
                              'mother_name', 'mother_brn', 'mother_nid', 'mother_occupation',
                              'permanent_address', 'relationship', 'order_of_child'];

            foreach ($required_fields as $field) {
                if (!isset($_POST[$field]) || trim($_POST[$field]) === '') {
                    throw new Exception("Required field '$field' is missing or empty");
                }
            }

            // Handle file upload
            $upload_dir = 'uploads/hospital_papers/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $hospital_paper = null;
            if (isset($_FILES['hospital_paper']) && $_FILES['hospital_paper']['error'] === UPLOAD_ERR_OK) {
                $file_tmp = $_FILES['hospital_paper']['tmp_name'];
                $file_name = time() . '_' . $_FILES['hospital_paper']['name'];
                $file_path = $upload_dir . $file_name;

                // Check if it's a PDF
                $file_type = mime_content_type($file_tmp);
                if ($file_type !== 'application/pdf') {
                    throw new Exception("Only PDF files are allowed");
                }

                // Move the uploaded file
                if (move_uploaded_file($file_tmp, $file_path)) {
                    $hospital_paper = $file_path;
                } else {
                    throw new Exception("Failed to upload file");
                }
            }

        // Generate a unique registration number
        $registration_no = 'BIRTH' . date('Ymd') . rand(1000, 9999);
        
        // Get user details
        $stmt = $conn->prepare("SELECT FirstName, LastName, Email, MobileNumber FROM tbluser WHERE ID = ?");
        $stmt->execute([$_SESSION['uid']]);
        $user = $stmt->fetch();
        
        // Prepare the SQL statement
        $stmt = $conn->prepare("
            INSERT INTO tblbirthapplications (
                user_id, registration_no, name, relationship,
                    father_name, father_brn, father_nid, father_occupation,
                    mother_name, mother_brn, mother_nid, mother_occupation,
                date_of_birth, place_of_birth, gender,
                permanent_address, nationality, blood_group,
                marital_status, order_of_child, occupation,
                    hospital_paper, application_date, status
            ) VALUES (
                :user_id, :registration_no, :name, :relationship,
                    :father_name, :father_brn, :father_nid, :father_occupation,
                    :mother_name, :mother_brn, :mother_nid, :mother_occupation,
                :date_of_birth, :place_of_birth, :gender,
                :permanent_address, :nationality, :blood_group,
                :marital_status, :order_of_child, :occupation,
                    :hospital_paper, NOW(), 'pending'
            )
        ");

        // Execute the statement with form data
        $result = $stmt->execute([
            'user_id' => $_SESSION['uid'],
            'registration_no' => $registration_no,
            'name' => $_POST['name'],
            'relationship' => $_POST['relationship'],
            'father_name' => $_POST['father_name'],
            'father_brn' => $_POST['father_brn'],
                'father_nid' => $_POST['father_nid'],
            'father_occupation' => $_POST['father_occupation'],
            'mother_name' => $_POST['mother_name'],
            'mother_brn' => $_POST['mother_brn'],
                'mother_nid' => $_POST['mother_nid'],
            'mother_occupation' => $_POST['mother_occupation'],
            'date_of_birth' => $_POST['date_of_birth'],
            'place_of_birth' => $_POST['place_of_birth'],
            'gender' => $_POST['gender'],
            'permanent_address' => $_POST['permanent_address'],
            'nationality' => $_POST['nationality'],
            'blood_group' => $_POST['blood_group'],
            'marital_status' => $_POST['marital_status'],
            'order_of_child' => $_POST['order_of_child'],
                'occupation' => $_POST['occupation'],
                'hospital_paper' => $hospital_paper
        ]);

        if ($result) {
            // Redirect to the list of applications
            header("Location: user_added_birth.php");
            exit();
        } else {
            throw new Exception("Failed to insert application");
        }
    } catch (Exception $e) {
        error_log("Error in birth application: " . $e->getMessage());
        $error = "An error occurred while submitting your application. Please try again.";
    }
}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Birth Certificate Application</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #2c662c;
            --secondary-color: #4CAF50;
            --accent-color-1: #ff6b6b;
            --accent-color-2: #4e54c8;
            --accent-color-3: #f39c12;
            --accent-color-4: #e74c3c;
            --accent-color-5: #3498db;
            --background-start: #e8f5e9;
            --background-end: #c8e6c9;
            --text-dark: #1a472a;
            --text-light: #ffffff;
            --shadow-color: rgba(0, 0, 0, 0.1);
            --input-border: #ddd;
            --input-focus: #4CAF50;
            --error-color: #ff3d3d;
            --success-color: #00c853;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: linear-gradient(135deg, var(--background-start) 0%, var(--background-end) 100%);
            margin: 0;
            min-height: 100vh;
        }

        .main-wrapper {
            display: flex;
            min-height: calc(100vh - 70px);
        }

        .header {
            background: #81c784;
            padding: 15px 20px;
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
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
            position: fixed;
            top: 70px;
            bottom: 0;
            left: 0;
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

        .content-wrapper {
            flex: 1;
            margin-left: 250px;
            margin-top: 70px;
            padding: 20px;
            background: #d4edda;
            min-height: calc(100vh - 70px);
        }

        .container {
            background: rgba(255, 255, 255, 0.95);
            padding: 40px;
            border-radius: 20px;
            width: 90%;
            max-width: 1000px;
            box-shadow: 0 15px 35px var(--shadow-color);
            margin: 20px auto;
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
            background: linear-gradient(90deg, 
                var(--accent-color-1) 0%,
                var(--accent-color-2) 25%,
                var(--accent-color-3) 50%,
                var(--accent-color-4) 75%,
                var(--accent-color-5) 100%
            );
        }

        .logo {
            margin: 20px auto;
            text-align: center;
            transition: transform 0.3s ease;
            position: relative;
        }

        .logo::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 3px;
            background: linear-gradient(90deg, var(--primary-color), var(--accent-color));
            border-radius: 2px;
        }

        .logo:hover {
            transform: scale(1.05);
        }

        .logo img {
            width: 140px;
            height: auto;
            filter: drop-shadow(0 4px 8px var(--shadow-color));
        }

        h1 {
            font-size: 32px;
            color: var(--text-dark);
            text-align: center;
            margin-bottom: 30px;
            font-weight: 600;
            text-shadow: 1px 1px 2px var(--shadow-color);
            position: relative;
            padding-bottom: 15px;
        }

        h1::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 3px;
            background: linear-gradient(90deg, var(--primary-color), var(--accent-color));
            border-radius: 2px;
        }

        .form-group {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 25px;
            position: relative;
        }

        .input-third {
            flex: 1 1 calc(33.333% - 20px);
            min-width: 250px;
        }

        .input-half {
            flex: 1 1 calc(50% - 20px);
            min-width: 300px;
        }

        .input-full {
            flex: 1 1 100%;
        }

        label {
            display: block;
            font-size: 16px;
            color: var(--primary-color);
            margin-bottom: 8px;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        input, select, textarea {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid var(--input-border);
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s ease;
            box-sizing: border-box;
            background-color: white;
        }

        input:hover, select:hover, textarea:hover {
            border-color: var(--secondary-color);
        }

        input:focus, select:focus, textarea:focus {
            border-color: var(--input-focus);
            box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.2);
            outline: none;
        }

        textarea {
            min-height: 100px;
            resize: vertical;
        }

        input[type="file"] {
            padding: 10px;
            background-color: #f8f9fa;
            border: 2px dashed var(--secondary-color);
            cursor: pointer;
        }

        input[type="file"]:hover {
            background-color: #f0f2f5;
            border-color: var(--accent-color);
        }

        .save-button {
            background: linear-gradient(145deg, var(--accent-color-2), var(--accent-color-5));
            color: var(--text-light);
            border: none;
            padding: 15px 30px;
            border-radius: 12px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            max-width: 400px;
            margin: 20px auto;
            display: block;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            position: relative;
            overflow: hidden;
        }

        .save-button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(
                90deg,
                transparent,
                rgba(255, 255, 255, 0.2),
                transparent
            );
            transition: 0.5s;
        }

        .save-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px var(--shadow-color);
            background: linear-gradient(145deg, var(--accent-color-5), var(--accent-color-2));
        }

        .save-button:hover::before {
            left: 100%;
        }

        .error-message {
            background-color: #fff2f2;
            color: var(--error-color);
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
            border-left: 5px solid var(--error-color);
            font-weight: 500;
            box-shadow: 0 2px 10px rgba(255, 61, 61, 0.1);
        }

        .section-header {
            background: linear-gradient(90deg, var(--accent-color-2), var(--accent-color-5));
            padding: 15px 20px 15px 50px;
            margin: 30px 0 20px;
            border-radius: 8px;
            color: var(--text-light);
            font-weight: 600;
            font-size: 18px;
            text-align: left;
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .section-header i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 20px;
        }

        .input-group {
            position: relative;
        }

        .input-group i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--accent-color-2);
            transition: all 0.3s ease;
        }

        .input-group input,
        .input-group select,
        .input-group textarea {
            padding-left: 45px;
        }

        .input-group:hover i {
            color: var(--accent-color-3);
        }

        .input-group input:focus + i,
        .input-group select:focus + i,
        .input-group textarea:focus + i {
            color: var(--accent-color-4);
        }

        /* Different colors for different sections */
        .basic-info { border-left: 5px solid var(--accent-color-1); }
        .personal-info { border-left: 5px solid var(--accent-color-2); }
        .father-info { border-left: 5px solid var(--accent-color-3); }
        .mother-info { border-left: 5px solid var(--accent-color-4); }
        .address-info { border-left: 5px solid var(--accent-color-5); }

        /* Loading Indicator */
        .loading {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.9);
            z-index: 1000;
            justify-content: center;
            align-items: center;
            backdrop-filter: blur(5px);
        }

        .loading.active {
            display: flex;
        }

        .spinner {
            width: 50px;
            height: 50px;
            border: 5px solid var(--background-start);
            border-top: 5px solid var(--accent-color);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Form Field Animation */
        @keyframes fieldFocus {
            0% { transform: scale(1); }
            50% { transform: scale(1.02); }
            100% { transform: scale(1); }
        }

        .form-group:focus-within {
            animation: fieldFocus 0.3s ease;
        }

        /* Responsive Design */
        @media (max-width: 1200px) {
            .container { padding: 30px; }
            .input-third { min-width: calc(50% - 20px); }
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
            }

            .logo-section {
                width: auto;
            }

            .logo-section img {
                width: 40px;
                height: 40px;
            }

            .logo-section h1 {
                font-size: 18px;
                color: white;
            }

            .user-info {
                gap: 10px;
            }

            .user-info img {
                display: none;
            }

            .user-info span {
                color: white;
                font-size: 14px;
            }

            .logout-btn {
                background: #004d00;
                padding: 5px 10px;
                font-size: 14px;
            }

            .sidebar {
                position: fixed;
                top: 60px;
                left: 0;
                right: 0;
                bottom: auto;
                width: 100%;
                height: 60px;
                padding: 10px;
                display: flex;
                overflow-x: auto;
                white-space: nowrap;
                -webkit-overflow-scrolling: touch;
                z-index: 900;
            }

            .sidebar a {
                flex: 0 0 auto;
                margin: 0 5px;
                padding: 8px 15px;
                font-size: 14px;
                display: inline-flex;
                align-items: center;
                background: rgba(255,255,255,0.1);
            }

            .sidebar a i {
                margin-right: 8px;
            }

            .content-wrapper {
                margin-left: 0;
                margin-top: 120px;
                padding: 10px;
            }

            .container {
                margin: 10px;
                padding: 20px;
                width: calc(100% - 20px);
            }
        }

        @media (max-width: 480px) {
            .header {
                height: 50px;
            }

            .logo-section img {
                width: 30px;
                height: 30px;
            }

            .logo-section h1 {
                font-size: 16px;
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
                height: 50px;
                padding: 8px;
            }

            .sidebar a {
                padding: 6px 12px;
                font-size: 12px;
            }

            .content-wrapper {
                margin-top: 100px;
            }
        }

        @media (max-width: 360px) {
            .logo-section h1 {
                font-size: 14px;
            }

            .user-info span {
                display: none;
            }

            .sidebar a {
                padding: 4px 10px;
                font-size: 11px;
            }
        }
    </style>
</head>
<body>
    <div class="main-wrapper">
        <div class="header">
            <div class="logo-section">
                <img src="logo.png" alt="Logo">
                <h1>Birth & Death Certificate</h1>
            </div>
            <div class="user-info">
            <img src="get_profile_image.php" alt="User">
            <span><?php echo htmlspecialchars($_SESSION['email'] ?? ''); ?></span>
            <a href="user_logout.php" class="logout-btn">Logout</a>
        </div>
        </div>
        
        <div class="sidebar">
            <a href="user_dashboard.php">
                <i class="fas fa-home"></i> Home
            </a>
            <a href="edit_info.php">
                <i class="fas fa-user-edit"></i> Edit Profile
            </a>
            <a href="user_application_birth1.php" class="active">
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
    <div class="loading">
        <div class="spinner"></div>
    </div>
    <div class="container">
        <?php if (isset($error)): ?>
            <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <h1>Birth Certificate Application</h1>
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" enctype="multipart/form-data" onsubmit="return validateForm()">
            <input type="hidden" name="relationship" value="<?php echo htmlspecialchars($relationship); ?>">
            
            <div class="section-header basic-info">
                <i class="fas fa-user"></i>
                Basic Information
            </div>
            <div class="form-group">
                <div class="input-third">
                    <div class="input-group">
                    <input type="text" id="name" name="name" placeholder="Enter full name" required>
                        <i class="fas fa-user-circle"></i>
                    </div>
                </div>
                <div class="input-third">
                    <div class="input-group">
                    <input type="text" id="nationality" name="nationality" placeholder="Enter nationality" required>
                        <i class="fas fa-globe"></i>
                    </div>
                </div>
                <div class="input-third">
                    <div class="input-group">
                    <select id="gender" name="gender" required>
                        <option value="">Select Gender</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Other">Other</option>
                    </select>
                        <i class="fas fa-venus-mars"></i>
                    </div>
                </div>
            </div>

            <div class="section-header personal-info">
                <i class="fas fa-info-circle"></i>
                Personal Information
            </div>
            <!-- Additional Personal Information -->
            <div class="form-group">
                <div class="input-third">
                    <label for="blood_group">Blood Group</label>
                    <select id="blood_group" name="blood_group" required>
                        <option value="">Select Blood Group</option>
                        <option value="A+">A+</option>
                        <option value="A-">A-</option>
                        <option value="B+">B+</option>
                        <option value="B-">B-</option>
                        <option value="AB+">AB+</option>
                        <option value="AB-">AB-</option>
                        <option value="O+">O+</option>
                        <option value="O-">O-</option>
                    </select>
                </div>
                <div class="input-third">
                    <label for="marital_status">Marital Status</label>
                    <select id="marital_status" name="marital_status" required>
                        <option value="">Select Marital Status</option>
                        <option value="Single">Single</option>
                        <option value="Married">Married</option>
                        <option value="Divorced">Divorced</option>
                        <option value="Widowed">Widowed</option>
                    </select>
                </div>
                <div class="input-third">
                    <label for="occupation">Occupation</label>
                    <select id="occupation" name="occupation" required>
                        <option value="">Select Occupation</option>
                        <option value="Student">Student</option>
                        <option value="Teacher">Teacher</option>
                        <option value="Doctor">Doctor</option>
                        <option value="Engineer">Engineer</option>
                        <option value="Business">Business</option>
                        <option value="Government Service">Government Service</option>
                        <option value="Private Service">Private Service</option>
                        <option value="Self Employed">Self Employed</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
            </div>

            <!-- Birth Information -->
            <div class="form-group">
                <div class="input-third">
                    <label for="date_of_birth">Date of Birth</label>
                    <input type="date" id="date_of_birth" name="date_of_birth" required>
                </div>
                <div class="input-third">
                    <label for="place_of_birth">Place of Birth</label>
                    <input type="text" id="place_of_birth" name="place_of_birth" placeholder="Enter place of birth" required>
                </div>
                <div class="input-third">
                    <label for="order_of_child">Order of Child</label>
                    <input type="number" id="order_of_child" name="order_of_child" min="1" placeholder="Enter birth order" required>
                </div>
            </div>

            <div class="section-header father-info">
                <i class="fas fa-male"></i>
                Father's Information
            </div>

            <!-- Father's Information -->
            <div class="form-group">
                <div class="input-third">
                    <label for="father_name">Father's Name</label>
                    <input type="text" id="father_name" name="father_name" placeholder="Enter father's name" required>
                </div>
                <div class="input-third">
                    <label for="father_brn">Father's BRN</label>
                    <input type="text" id="father_brn" name="father_brn" placeholder="Enter father's BRN" required>
                </div>
                <div class="input-third">
                    <label for="father_nid">Father's NID</label>
                    <input type="text" id="father_nid" name="father_nid" placeholder="Enter father's NID" required>
                </div>
            </div>
            <div class="form-group">
                <div class="input-full">
                    <label for="father_occupation">Father's Occupation</label>
                    <select id="father_occupation" name="father_occupation" required>
                        <option value="">Select Occupation</option>
                        <option value="Farmer">Farmer</option>
                        <option value="Teacher">Teacher</option>
                        <option value="Doctor">Doctor</option>
                        <option value="Engineer">Engineer</option>
                        <option value="Business">Business</option>
                        <option value="Government Service">Government Service</option>
                        <option value="Private Service">Private Service</option>
                        <option value="Self Employed">Self Employed</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
            </div>

            <div class="section-header mother-info">
                <i class="fas fa-female"></i>
                Mother's Information
            </div>

            <!-- Mother's Information -->
            <div class="form-group">
                <div class="input-third">
                    <label for="mother_name">Mother's Name</label>
                    <input type="text" id="mother_name" name="mother_name" placeholder="Enter mother's name" required>
                </div>
                <div class="input-third">
                    <label for="mother_brn">Mother's BRN</label>
                    <input type="text" id="mother_brn" name="mother_brn" placeholder="Enter mother's BRN" required>
                </div>
                <div class="input-third">
                    <label for="mother_nid">Mother's NID</label>
                    <input type="text" id="mother_nid" name="mother_nid" placeholder="Enter mother's NID" required>
                </div>
            </div>
            <div class="form-group">
                <div class="input-full">
                    <label for="mother_occupation">Mother's Occupation</label>
                    <select id="mother_occupation" name="mother_occupation" required>
                        <option value="">Select Occupation</option>
                        <option value="Housewife">Housewife</option>
                        <option value="Teacher">Teacher</option>
                        <option value="Doctor">Doctor</option>
                        <option value="Engineer">Engineer</option>
                        <option value="Business">Business</option>
                        <option value="Government Service">Government Service</option>
                        <option value="Private Service">Private Service</option>
                        <option value="Self Employed">Self Employed</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
            </div>

            <div class="section-header address-info">
                <i class="fas fa-map-marker-alt"></i>
                Address Information
            </div>

            <!-- Address -->
            <div class="form-group">
                <div class="input-third">
                    <div class="input-group">
                        <select id="division" name="division" required onchange="loadDistricts()">
                            <option value="">Select Division</option>
                        </select>
                        <i class="fas fa-map"></i>
                    </div>
                </div>
                <div class="input-third">
                    <div class="input-group">
                        <select id="district" name="district" required onchange="loadUpazilas()" disabled>
                            <option value="">Select District</option>
                        </select>
                        <i class="fas fa-map-marker"></i>
                    </div>
                </div>
                <div class="input-third">
                    <div class="input-group">
                        <select id="upazila" name="upazila" required onchange="loadUnions()" disabled>
                            <option value="">Select Upazila</option>
                        </select>
                        <i class="fas fa-map-pin"></i>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="input-third">
                    <div class="input-group">
                        <select id="union" name="union" required disabled>
                            <option value="">Select Union/Pouroshova</option>
                        </select>
                        <i class="fas fa-building"></i>
                    </div>
                </div>
                <div class="input-full">
                    <div class="input-group">
                        <textarea id="permanent_address" name="permanent_address" placeholder="Enter permanent address" required></textarea>
                        <i class="fas fa-home"></i>
                    </div>
                </div>
            </div>

            <!-- Hospital Paper Upload -->
            <div class="form-group">
                <div class="input-full">
                    <label for="hospital_paper">Hospital Paper (PDF)</label>
                    <input type="file" id="hospital_paper" name="hospital_paper" accept=".pdf" required>
                </div>
            </div>

            <button type="submit" class="save-button">
                <i class="fas fa-paper-plane"></i>
                Submit Application
            </button>
        </form>
            </div>
        </div>
    </div>
</body>
<!-- Load external scripts -->
<script src="assets/js/locations.js"></script>
<script>
// All the JavaScript code here
function validateForm() {
    // Show loading indicator
    document.querySelector('.loading').classList.add('active');
    
    var requiredFields = [
        'name', 'nationality', 'blood_group', 'marital_status',
        'occupation', 'date_of_birth', 'place_of_birth', 'gender',
        'father_name', 'father_brn', 'father_nid', 'father_occupation',
        'mother_name', 'mother_brn', 'mother_nid', 'mother_occupation',
        'permanent_address', 'order_of_child',
        // Add location fields
        'division', 'district', 'upazila', 'union'
    ];

    for (var i = 0; i < requiredFields.length; i++) {
        var field = document.getElementById(requiredFields[i]);
        if (!field.value.trim()) {
            document.querySelector('.loading').classList.remove('active');
            alert('Please fill in all required fields. ' + field.name + ' is missing.');
            field.focus();
            return false;
        }
    }

    // Validate file upload
    var hospitalDoc = document.getElementById('hospital_paper');
    if (!hospitalDoc.files[0]) {
        document.querySelector('.loading').classList.remove('active');
        alert('Please upload the hospital paper');
        return false;
    }

    return true;
}

// Add input event listeners for real-time validation
document.addEventListener('DOMContentLoaded', function() {
    // Initialize location dropdowns
    initializeLocationDropdowns();
    
    var inputs = document.querySelectorAll('input, select, textarea');
    inputs.forEach(function(input) {
        input.addEventListener('input', function() {
            if (this.value.trim()) {
                this.style.borderColor = '#4CAF50';
            } else {
                this.style.borderColor = '#ddd';
            }
        });
    });
});

function initializeLocationDropdowns() {
    // Load divisions on page load
    const divisionSelect = document.getElementById('division');
    if (!divisionSelect) return; // Safety check
    
    // Clear existing options
    divisionSelect.innerHTML = '<option value="">Select Division</option>';
    
    // Add divisions from bangladeshData
    if (typeof bangladeshData !== 'undefined') {
        Object.keys(bangladeshData).forEach(division => {
            const option = document.createElement('option');
            option.value = division;
            option.textContent = division;
            divisionSelect.appendChild(option);
        });
    } else {
        console.error('bangladeshData is not defined. Make sure locations.js is loaded properly.');
    }
}

function loadDistricts() {
    const divisionSelect = document.getElementById('division');
    const districtSelect = document.getElementById('district');
    const upazilaSelect = document.getElementById('upazila');
    const unionSelect = document.getElementById('union');
    
    // Clear and disable subsequent dropdowns
    districtSelect.innerHTML = '<option value="">Select District</option>';
    upazilaSelect.innerHTML = '<option value="">Select Upazila</option>';
    unionSelect.innerHTML = '<option value="">Select Union/Pouroshova</option>';
    
    upazilaSelect.disabled = true;
    unionSelect.disabled = true;
    
    const selectedDivision = divisionSelect.value;
    if (selectedDivision && bangladeshData[selectedDivision]?.districts) {
        districtSelect.disabled = false;
        const districts = bangladeshData[selectedDivision].districts;
        
        // Sort districts alphabetically
        const sortedDistricts = Object.keys(districts).sort();
        
        sortedDistricts.forEach(district => {
            const option = document.createElement('option');
            option.value = district;
            option.textContent = district;
            districtSelect.appendChild(option);
        });
    } else {
        districtSelect.disabled = true;
    }
}

function loadUpazilas() {
    const divisionSelect = document.getElementById('division');
    const districtSelect = document.getElementById('district');
    const upazilaSelect = document.getElementById('upazila');
    const unionSelect = document.getElementById('union');
    
    // Clear and disable subsequent dropdowns
    upazilaSelect.innerHTML = '<option value="">Select Upazila</option>';
    unionSelect.innerHTML = '<option value="">Select Union/Pouroshova</option>';
    
    unionSelect.disabled = true;
    
    const selectedDivision = divisionSelect.value;
    const selectedDistrict = districtSelect.value;
    
    if (selectedDistrict && bangladeshData[selectedDivision]?.districts?.[selectedDistrict]?.upazilas) {
        upazilaSelect.disabled = false;
        const upazilas = bangladeshData[selectedDivision].districts[selectedDistrict].upazilas;
        
        // Sort upazilas alphabetically
        const sortedUpazilas = Object.keys(upazilas).sort();
        
        sortedUpazilas.forEach(upazila => {
            const option = document.createElement('option');
            option.value = upazila;
            option.textContent = upazila;
            upazilaSelect.appendChild(option);
        });
    } else {
        upazilaSelect.disabled = true;
    }
}

function loadUnions() {
    const divisionSelect = document.getElementById('division');
    const districtSelect = document.getElementById('district');
    const upazilaSelect = document.getElementById('upazila');
    const unionSelect = document.getElementById('union');
    
    // Clear unions dropdown
    unionSelect.innerHTML = '<option value="">Select Union/Pouroshova</option>';
    
    const selectedDivision = divisionSelect.value;
    const selectedDistrict = districtSelect.value;
    const selectedUpazila = upazilaSelect.value;
    
    if (selectedUpazila && bangladeshData[selectedDivision]?.districts?.[selectedDistrict]?.upazilas?.[selectedUpazila]) {
        unionSelect.disabled = false;
        const unions = bangladeshData[selectedDivision].districts[selectedDistrict].upazilas[selectedUpazila];
        
        // Unions are already an array in the data structure
        const sortedUnions = [...unions].sort();
        
        sortedUnions.forEach(union => {
            if (typeof union === 'string') {
                const option = document.createElement('option');
                option.value = union;
                option.textContent = union;
                unionSelect.appendChild(option);
            }
        });
    } else {
        unionSelect.disabled = true;
    }
    
    // Log for debugging
    console.log('Selected Division:', selectedDivision);
    console.log('Selected District:', selectedDistrict);
    console.log('Selected Upazila:', selectedUpazila);
    console.log('Unions:', bangladeshData[selectedDivision]?.districts?.[selectedDistrict]?.upazilas?.[selectedUpazila]);
}
</script>
</html>

