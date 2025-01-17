<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'config.php';
require_once 'dbconfig.php';

// Add debugging
error_log("Session data: " . print_r($_SESSION, true));
error_log("POST data: " . print_r($_POST, true));

// Check if user has completed Stage 1
if (!isset($_SESSION['registration_user_id']) || $_SESSION['registration_stage'] != 1) {
    error_log("Session validation failed. User ID: " . isset($_SESSION['registration_user_id']) . ", Stage: " . ($_SESSION['registration_stage'] ?? 'not set'));
    header("Location: register.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Sanitize and validate inputs
        $fatherName = trim(strip_tags($_POST['father-name']));
        $fatherBrn = trim(strip_tags($_POST['father-brn']));
        $motherName = trim(strip_tags($_POST['mother-name']));
        $motherBrn = trim(strip_tags($_POST['mother-brn']));
        $presentAddress = trim(strip_tags($_POST['present-address']));
        $permanentAddress = trim(strip_tags($_POST['permanent-address']));
        $nationality = trim(strip_tags($_POST['nationality']));
        $bloodGroup = trim(strip_tags($_POST['blood-group']));
        $maritalStatus = trim(strip_tags($_POST['marital-status']));
        $sex = trim(strip_tags($_POST['sex']));
        $orderOfChild = (int)$_POST['order-of-child'];
        $occupation = trim(strip_tags($_POST['occupation']));
        $division = trim(strip_tags($_POST['division']));
        $district = trim(strip_tags($_POST['district']));
        $upazila = trim(strip_tags($_POST['upazila']));
        $pouroshova = trim(strip_tags($_POST['pouroshova']));

        error_log("Sanitized inputs: " . print_r([
            'fatherName' => $fatherName,
            'fatherBrn' => $fatherBrn,
            'motherName' => $motherName,
            'motherBrn' => $motherBrn,
            'presentAddress' => $presentAddress,
            'permanentAddress' => $permanentAddress,
            'nationality' => $nationality,
            'bloodGroup' => $bloodGroup,
            'maritalStatus' => $maritalStatus,
            'sex' => $sex,
            'orderOfChild' => $orderOfChild,
            'occupation' => $occupation,
            'division' => $division,
            'district' => $district,
            'upazila' => $upazila,
            'pouroshova' => $pouroshova
        ], true));

        // Validate required fields
        if (empty($fatherName) || empty($fatherBrn) || empty($motherName) || empty($motherBrn) ||
            empty($presentAddress) || empty($permanentAddress) || empty($nationality) || 
            empty($bloodGroup) || empty($maritalStatus) || empty($sex) || 
            empty($orderOfChild) || empty($occupation) || empty($division) ||
            empty($district) || empty($upazila) || empty($pouroshova)) {
            throw new Exception("All fields are required");
        }

        // Validate BRN format (assuming it should be 17 digits)
        if (!preg_match('/^\d{17}$/', $fatherBrn) || !preg_match('/^\d{17}$/', $motherBrn)) {
            throw new Exception("BRN must be 17 digits");
        }

        // Begin transaction
        $conn->beginTransaction();

        error_log("Starting database transaction");

        // Insert detailed user information
        $stmt = $conn->prepare("INSERT INTO tbluserdetails (user_id, father_name, father_brn, mother_name, 
                               mother_brn, present_address, permanent_address, nationality, blood_group, 
                               marital_status, sex, order_of_child, occupation, division, district,
                               upazila, pouroshova) 
                               VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        $params = [
            $_SESSION['registration_user_id'],
            $fatherName, $fatherBrn,
            $motherName, $motherBrn,
            $presentAddress, $permanentAddress,
            $nationality, $bloodGroup,
            $maritalStatus, $sex,
            $orderOfChild, $occupation,
            $division, $district,
            $upazila, $pouroshova
        ];
        
        error_log("Executing SQL with params: " . print_r($params, true));
        
        if (!$stmt->execute($params)) {
            $error = $stmt->errorInfo();
            error_log("Database error: " . print_r($error, true));
            throw new Exception("Failed to save detailed information: " . $error[2]);
        }

        error_log("Database insert successful");

        // Update registration stage
        $_SESSION['registration_stage'] = 2;
        error_log("Updated registration stage to 2");

        // Commit transaction
        $conn->commit();
        error_log("Transaction committed");

        // Redirect to Stage 3
        header("Location: register3.php");
        exit();

    } catch (Exception $e) {
        error_log("Error in register2.php: " . $e->getMessage());
        if ($conn->inTransaction()) {
            $conn->rollBack();
            error_log("Transaction rolled back");
        }
        $error = $e->getMessage();
    }
}

// Display any errors at the top of the form
if (isset($error)) {
    echo '<div style="color: red; background-color: #ffe6e6; padding: 10px; margin-bottom: 20px; border-radius: 5px;">';
    echo htmlspecialchars($error);
    echo '</div>';
}

// Rest of the HTML code remains the same...
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detailed Information</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Arial', sans-serif;
            background-color: #b5dcb3; /* Brighter green background */
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        .container {
            display: flex;
            flex-direction: column;
            background-color: #b5dcb3; /* Neomorphic background color */
            border-radius: 20px;
            box-shadow: 9px 9px 16px #88b489, -9px -9px 16px #e2ffe2; /* Neomorphism effect */
            max-width: 900px;
            width: 100%;
            padding: 30px;
            position: relative;
            transition: transform 0.3s ease-in-out;
        }
        .container:hover {
            transform: scale(1.02); /* Scale up on hover */
        }
        .logo {
            position: absolute;
            top: 20px;
            left: 20px;
            width: 80px;
            height: auto;
        }
        h1 {
            text-align: center;
            color: #2c662c; /* Darker green */
            font-size: 2.5rem;
            margin-bottom: 30px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            color: #2c662c;
            font-weight: bold;
            display: block;
            margin-bottom: 8px;
        }
        .input-group {
            background: #b5dcb3;
            border-radius: 12px;
            box-shadow: inset 6px 6px 10px #88b489, inset -6px -6px 10px #e2ffe2;
            padding: 5px;
        }
        .input-group-prepend .input-group-text,
        .input-group-append .input-group-text {
            background: transparent;
            border: none;
            color: #2c662c;
        }
        .form-control {
            border: none;
            background: transparent;
            color: #2c662c;
            padding: 12px;
        }
        .form-control:focus {
            box-shadow: none;
            background: transparent;
        }
        select.form-control {
            height: auto;
            padding: 12px;
        }
        .next-btn {
            width: 100%;
            padding: 14px 28px;
            background-color: #228b22;
            color: white;
            font-size: 18px;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            box-shadow: 6px 6px 10px #88b489, -6px -6px 10px #e2ffe2;
            transition: all 0.3s ease-in-out;
        }
        .next-btn:hover {
            background-color: #196619;
            box-shadow: 9px 9px 16px #88b489, -9px -9px 16px #e2ffe2;
            transform: translateY(-2px);
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            color: #666;
            font-size: 0.9rem;
        }
        @media (max-width: 768px) {
            h1 {
                font-size: 2rem;
            }
            .form-group input[type="text"],
            .form-group input[type="number"],
            .form-group select {
                flex-basis: 100%;
            }
            .next-btn {
                font-size: 1rem;
                padding: 12px;
            }
        }
        @media (max-width: 500px) {
            .container {
                padding: 15px;
            }
            .logo {
                width: 60px;
            }
            h1 {
                font-size: 1.8rem;
            }
            .next-btn {
                font-size: 0.9rem;
                padding: 10px;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <!-- Logo at the top-left corner -->
    <img src="logo.png" alt="Logo" class="logo">

    <!-- Page Title -->
    <h1>Detailed Information</h1>

    <!-- Detailed Information Form -->
    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
        <?php if (isset($error)): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
        </div>
        <?php endif; ?>
        
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="father-name">Father's Name</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-male"></i></span>
                        </div>
                        <input type="text" class="form-control" id="father-name" name="father-name" placeholder="Mr. X" required>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="father-brn">Father's BRN</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                        </div>
                        <input type="text" class="form-control" id="father-brn" name="father-brn" placeholder="21054687526485215" required>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="mother-name">Mother's Name</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-female"></i></span>
                        </div>
                        <input type="text" class="form-control" id="mother-name" name="mother-name" placeholder="Miss Y" required>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="mother-brn">Mother's BRN</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                        </div>
                        <input type="text" class="form-control" id="mother-brn" name="mother-brn" placeholder="12545684562655221" required>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label for="present-address">Present Address</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fas fa-home"></i></span>
                </div>
                <input type="text" class="form-control" id="present-address" name="present-address" placeholder="Shewrapara, Mirpur, Dhaka" required>
            </div>
        </div>

        <div class="form-group">
            <label for="permanent-address">Permanent Address</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                </div>
                <input type="text" class="form-control" id="permanent-address" name="permanent-address" placeholder="Kandirpar, Cumilla" required>
            </div>
        </div>

        <!-- Bangladesh Location Information -->
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="division">Division</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-map"></i></span>
                        </div>
                        <select class="form-control" id="division" name="division" required>
                            <option value="" disabled selected>Select Division</option>
                            <option value="Dhaka">Dhaka (ঢাকা)</option>
                            <option value="Chittagong">Chittagong (চট্টগ্রাম)</option>
                            <option value="Rajshahi">Rajshahi (রাজশাহী)</option>
                            <option value="Khulna">Khulna (খুলনা)</option>
                            <option value="Barisal">Barisal (বরিশাল)</option>
                            <option value="Sylhet">Sylhet (সিলেট)</option>
                            <option value="Rangpur">Rangpur (রংপুর)</option>
                            <option value="Mymensingh">Mymensingh (ময়মনসিংহ)</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="district">District (Zilla)</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-map-signs"></i></span>
                        </div>
                        <select class="form-control" id="district" name="district" required>
                            <option value="" disabled selected>Select District</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="upazila">Upazila</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-map-pin"></i></span>
                        </div>
                        <select class="form-control" id="upazila" name="upazila" required>
                            <option value="" disabled selected>Select Upazila</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="pouroshova">Pouroshova/Union</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-city"></i></span>
                        </div>
                        <select class="form-control" id="pouroshova" name="pouroshova" required>
                            <option value="" disabled selected>Select Pouroshova/Union</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="nationality">Nationality</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-flag"></i></span>
                        </div>
                        <input type="text" class="form-control" id="nationality" name="nationality" placeholder="Bangladeshi" required>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="blood-group">Blood Group</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-tint"></i></span>
                        </div>
                        <select class="form-control" id="blood-group" name="blood-group" required>
                            <option value="" disabled selected>Select your blood group</option>
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
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="marital-status">Marital Status</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-heart"></i></span>
                        </div>
                        <select class="form-control" id="marital-status" name="marital-status" required>
                            <option value="" disabled selected>Select your marital status</option>
                            <option value="Single">Single</option>
                            <option value="Married">Married</option>
                            <option value="Divorced">Divorced</option>
                            <option value="Widowed">Widowed</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="sex">Sex</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-venus-mars"></i></span>
                        </div>
                        <select class="form-control" id="sex" name="sex" required>
                            <option value="" disabled selected>Select your sex</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="order-of-child">Order of Child</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-sort-numeric-down"></i></span>
                        </div>
                        <select class="form-control" id="order-of-child" name="order-of-child" required>
                            <option value="" disabled selected>Select your order</option>
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                            <option value="6">6+</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="occupation">Occupation</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-briefcase"></i></span>
                        </div>
                        <input type="text" class="form-control" id="occupation" name="occupation" placeholder="Student" required>
                    </div>
                </div>
            </div>
        </div>

        <button type="submit" class="next-btn">Next</button>
    </form>

    <!-- Footer Information -->
    <div class="footer">
        <p>&copy; 2024 Online Registration System</p>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="assets/js/locations.js"></script>
<script>
$(document).ready(function() {
    // Handle division change
    $('#division').change(function() {
        const division = $(this).val();
        const districtSelect = $('#district');
        const upazilaSelect = $('#upazila');
        const pouroshovaSelect = $('#pouroshova');
        
        // Clear dependent dropdowns
        districtSelect.html('<option value="" disabled selected>Select District</option>');
        upazilaSelect.html('<option value="" disabled selected>Select Upazila</option>');
        pouroshovaSelect.html('<option value="" disabled selected>Select Pouroshova/Union</option>');
        
        if (division && bangladeshData[division]) {
            const districts = Object.keys(bangladeshData[division].districts);
            districts.forEach(district => {
                districtSelect.append(new Option(district, district));
            });
        }
    });

    // Handle district change
    $('#district').change(function() {
        const division = $('#division').val();
        const district = $(this).val();
        const upazilaSelect = $('#upazila');
        const pouroshovaSelect = $('#pouroshova');
        
        // Clear dependent dropdowns
        upazilaSelect.html('<option value="" disabled selected>Select Upazila</option>');
        pouroshovaSelect.html('<option value="" disabled selected>Select Pouroshova/Union</option>');
        
        if (division && district && bangladeshData[division].districts[district]) {
            const upazilas = Object.keys(bangladeshData[division].districts[district].upazilas);
            upazilas.forEach(upazila => {
                upazilaSelect.append(new Option(upazila, upazila));
            });
        }
    });

    // Handle upazila change
    $('#upazila').change(function() {
        const division = $('#division').val();
        const district = $('#district').val();
        const upazila = $(this).val();
        const pouroshovaSelect = $('#pouroshova');
        
        // Clear dependent dropdown
        pouroshovaSelect.html('<option value="" disabled selected>Select Pouroshova/Union</option>');
        
        if (division && district && upazila && 
            bangladeshData[division].districts[district].upazilas[upazila]) {
            const pouroshovas = bangladeshData[division].districts[district].upazilas[upazila];
            pouroshovas.forEach(pouroshova => {
                pouroshovaSelect.append(new Option(pouroshova, pouroshova));
            });
        }
    });
});
</script>

</body>
</html>
