<?php
session_start();
require_once 'config.php';
require_once 'dbconfig.php';
require_once 'functions.php';

$result = $error = '';
$success = false;

// Function to validate and format application ID
function validateApplicationId($id) {
    // Remove any spaces or special characters
    $id = trim(preg_replace('/[^A-Z0-9]/', '', strtoupper($id)));
    
    // Check for both formats:
    // 1. BIRTHYYYYXXXXX or DEATHYYYYXXXXX (without hyphen)
    // 2. BIRTH-YYYY-XXXXX or DEATH-YYYY-XXXXX (with hyphens)
    
    if (preg_match('/^(BIRTH|DEATH)\d{9,}$/', $id)) {
        // Format 1: No hyphens
        return $id;
    } elseif (preg_match('/^(BIRTH|DEATH)-?\d{4}-?\d{5,}$/', $id)) {
        // Format 2: With or without hyphens
        // Remove hyphens if they exist
        return preg_replace('/-/', '', $id);
    }
    
    return false;
}

// Function to determine application type from ID
function getApplicationType($id) {
    return stripos($id, 'BIRTH') === 0 ? 'birth' : 'death';
}

// Handle both GET and POST requests
if (($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'GET') && 
    (isset($_REQUEST['application_id']) || isset($_GET['id']))) {
    
    try {
        // Get application ID from either POST, GET or GET['id']
        $raw_id = $_REQUEST['application_id'] ?? $_GET['id'] ?? '';
        
        // Sanitize and validate input
        $application_id = validateApplicationId($raw_id);
        
        if ($application_id === false) {
            throw new Exception("Invalid application ID format. Please enter a valid application ID.");
        }

        // Determine application type from ID
        $application_type = getApplicationType($application_id);
        
        // Prepare the query based on application type
        if ($application_type === 'birth') {
            $stmt = $conn->prepare("
                SELECT 
                    a.registration_no,
                    a.name,
                    a.date_of_birth as event_date,
                    a.place_of_birth as event_place,
                    a.father_name,
                    a.mother_name,
                    a.application_date,
                    a.status,
                    COALESCE(sl.remark, '') as remark,
                    u.FirstName as UserFirstName,
                    u.LastName as UserLastName,
                    u.MobileNumber
                                  FROM tblbirthapplications a 
                                  LEFT JOIN tbluser u ON a.user_id = u.ID 
                LEFT JOIN (
                    SELECT application_id, remark 
                    FROM status_logs 
                    WHERE application_type = 'birth'
                    ORDER BY created_at DESC 
                    LIMIT 1
                ) sl ON sl.application_id = a.id
                WHERE registration_no = ? 
                   OR REPLACE(registration_no, '-', '') = ?
                   OR ? IN (registration_no, REPLACE(registration_no, '-', ''))
                LIMIT 1
            ");
        } else {
            $stmt = $conn->prepare("
                SELECT 
                    a.registration_no,
                    a.name,
                    a.date_of_death as event_date,
                    a.place_of_death as event_place,
                    a.father_name,
                    a.mother_name,
                    a.application_date,
                    a.status,
                    COALESCE(sl.remark, '') as remark,
                    u.FirstName as UserFirstName,
                    u.LastName as UserLastName,
                    u.MobileNumber
                                  FROM tbldeathapplications a 
                                  LEFT JOIN tbluser u ON a.user_id = u.ID 
                LEFT JOIN (
                    SELECT application_id, remark 
                    FROM status_logs 
                    WHERE application_type = 'death'
                    ORDER BY created_at DESC 
                    LIMIT 1
                ) sl ON sl.application_id = a.id
                WHERE registration_no = ? 
                   OR REPLACE(registration_no, '-', '') = ?
                   OR ? IN (registration_no, REPLACE(registration_no, '-', ''))
                LIMIT 1
            ");
        }
        
        // Execute with proper error handling
        if (!$stmt->execute([$application_id, $application_id, $application_id])) {
            throw new Exception("Failed to execute query");
        }
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$result) {
            throw new Exception("No application found with ID: " . htmlspecialchars($raw_id));
        }

        // Log successful query
        error_log("Successfully retrieved application status for ID: " . $application_id);
        $success = true;
        
    } catch (PDOException $e) {
        error_log("Database error checking application status: " . $e->getMessage() . 
                 " | Raw ID: " . ($raw_id ?? 'not set') .
                 " | Formatted ID: " . ($application_id ?? 'not set') .
                 " | Type: " . ($application_type ?? 'not set'));
        $error = "A database error occurred while checking the application status. Please try again later.";
    } catch (Exception $e) {
        error_log("Application status check error: " . $e->getMessage() . 
                 " | Raw ID: " . ($raw_id ?? 'not set') .
                 " | Formatted ID: " . ($application_id ?? 'not set') .
                 " | Type: " . ($application_type ?? 'not set'));
        $error = $e->getMessage();
    }

    // Log all attempts for monitoring
    if ($success) {
        error_log("Successful status check - ID: " . ($application_id ?? 'not set') . 
                 " | Type: " . ($application_type ?? 'not set'));
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check Application Status</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #b5dcb3;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .container {
            max-width: 800px;
            margin: 40px auto;
            padding: 0 20px;
            flex-grow: 1;
        }

        .search-card {
            background-color: #b5dcb3;
            border-radius: 20px;
            box-shadow: 9px 9px 16px #88b489, -9px -9px 16px #e2ffe2;
            padding: 30px;
            margin-bottom: 30px;
            transition: transform 0.3s ease-in-out;
        }

        .search-card:hover {
            transform: scale(1.02);
        }

        .search-card h2 {
            color: #2c662c;
            margin-top: 0;
            margin-bottom: 20px;
            text-align: center;
        }

        .search-form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .form-group label {
            font-weight: bold;
            color: #2c662c;
        }

        .form-group input, 
        .form-group select {
            padding: 12px;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            background: #b5dcb3;
            box-shadow: inset 6px 6px 10px #88b489, inset -6px -6px 10px #e2ffe2;
            color: #2c662c;
        }

        .form-group input:focus, 
        .form-group select:focus {
            outline: none;
            box-shadow: inset 8px 8px 12px #88b489, inset -8px -8px 12px #e2ffe2;
        }

        .submit-btn {
            background-color: #228b22;
            color: white;
            border: none;
            padding: 14px 28px;
            border-radius: 12px;
            cursor: pointer;
            font-size: 18px;
            box-shadow: 6px 6px 10px #88b489, -6px -6px 10px #e2ffe2;
            transition: all 0.3s ease-in-out;
        }

        .submit-btn:hover {
            background-color: #1a691a;
            transform: translateY(-2px);
        }

        .submit-btn:active {
            transform: translateY(0);
            box-shadow: 3px 3px 5px #88b489, -3px -3px 5px #e2ffe2;
        }

        .error-message {
            background-color: #ffebee;
            color: #c62828;
            padding: 15px;
            border-radius: 12px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            box-shadow: inset 3px 3px 5px rgba(0,0,0,0.1);
        }

        .result-card {
            background-color: #b5dcb3;
            border-radius: 20px;
            box-shadow: 9px 9px 16px #88b489, -9px -9px 16px #e2ffe2;
            padding: 30px;
            margin-top: 30px;
            transition: transform 0.3s ease-in-out;
        }

        .result-card:hover {
            transform: scale(1.02);
        }

        .result-card h3 {
            color: #2c662c;
            margin-top: 0;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #2c662c;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .info-item {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .info-item label {
            font-weight: bold;
            color: #2c662c;
            font-size: 14px;
        }

        .info-item span {
            color: #333;
            font-size: 16px;
        }

        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 14px;
        }

        .status-badge.pending {
            background-color: #fff3e0;
            color: #e65100;
        }

        .status-badge.approved {
            background-color: #e8f5e9;
            color: #2e7d32;
        }

        .status-badge.rejected {
            background-color: #ffebee;
            color: #c62828;
        }

        .rejection-reason {
            color: #c62828;
            font-style: italic;
        }

        .form-text {
            color: #2c662c;
            font-size: 12px;
            margin-top: 4px;
        }

        @media (max-width: 480px) {
            .container {
                margin: 20px auto;
                padding: 0 15px;
            }

            .search-card,
            .result-card {
                padding: 20px;
            }

            .form-group label {
                font-size: 13px;
            }

            .form-group input, 
            .form-group select,
            .submit-btn {
                padding: 7px;
                font-size: 12px;
            }

            .info-item label,
            .info-item span {
                font-size: 12px;
            }

            .status-badge {
                padding: 4px 8px;
                font-size: 11px;
            }
        }

        .home-btn {
            display: block;
            background-color: #228b22;
            color: white;
            border: none;
            padding: 14px 28px;
            border-radius: 12px;
            cursor: pointer;
            font-size: 18px;
            box-shadow: 6px 6px 10px #88b489, -6px -6px 10px #e2ffe2;
            transition: all 0.3s ease-in-out;
            text-decoration: none;
            text-align: center;
            margin-top: 0px;
        }

        .home-btn:hover {
            background-color: #1a691a;
            transform: translateY(-2px);
        }

        .home-btn:active {
            transform: translateY(0);
            box-shadow: 3px 3px 5px #88b489, -3px -3px 5px #e2ffe2;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="search-card">
            <h2>Check Application Status</h2>
            
            <?php if ($error): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="search-form" id="statusForm">
                <div class="form-group">
                    <label for="application_type">Application Type</label>
                    <select name="application_type" id="application_type" required>
                        <option value="birth" <?php echo isset($_POST['application_type']) && $_POST['application_type'] === 'birth' ? 'selected' : ''; ?>>Birth Certificate</option>
                        <option value="death" <?php echo isset($_POST['application_type']) && $_POST['application_type'] === 'death' ? 'selected' : ''; ?>>Death Certificate</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="application_id">Application ID</label>
                    <input type="text" name="application_id" id="application_id" 
                           value="<?php echo isset($_POST['application_id']) ? htmlspecialchars($_POST['application_id']) : ''; ?>" 
                           placeholder="Format: BIRTH/DEATH-YYYY-XXXXX"
                           required>
                    <small class="form-text">Example: BIRTH-2024-00001 or DEATH-2024-00001</small>
                </div>

                <button type="submit" class="submit-btn">
                    <i class="fas fa-search"></i> Check Status
                </button>

                <a href="index.php" class="home-btn">
                    <i class="fas fa-home"></i> Back to Home
                </a>
            </form>
        </div>

        <?php if ($success && $result): ?>
            <div class="result-card">
                <h3><?php echo $application_type === 'birth' ? 'Birth' : 'Death'; ?> Certificate Application Details</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <label>Application ID</label>
                        <span><?php echo htmlspecialchars($result['registration_no']); ?></span>
                    </div>
                    
                    <div class="info-item">
                        <label>Applicant Name</label>
                        <span><?php echo htmlspecialchars($result['name']); ?></span>
                    </div>
                    
                    <div class="info-item">
                        <label>Application Date</label>
                        <span><?php echo date('F d, Y', strtotime($result['application_date'])); ?></span>
                    </div>
                    
                    <div class="info-item">
                        <label>Status</label>
                        <span class="status-badge <?php echo strtolower($result['status']); ?>">
                            <?php echo htmlspecialchars($result['status']); ?>
                        </span>
                    </div>

                    <?php if ($result['status'] === 'Rejected' && !empty($result['remark'])): ?>
                        <div class="info-item" style="grid-column: 1/-1;">
                            <label>Rejection Reason</label>
                            <span class="rejection-reason"><?php echo htmlspecialchars($result['remark']); ?></span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script>
    document.getElementById('statusForm').addEventListener('submit', function(e) {
        const applicationId = document.getElementById('application_id').value.trim();
        const applicationType = document.getElementById('application_type').value;
        
        // Remove any special characters for validation
        const cleanId = applicationId.replace(/[^A-Za-z0-9]/g, '').toUpperCase();
        
        // Check for both formats
        const validFormat = /^(BIRTH|DEATH)\d{9,}$/.test(cleanId) || 
                           /^(BIRTH|DEATH)-?\d{4}-?\d{5,}$/.test(cleanId);
        
        if (!validFormat) {
            e.preventDefault();
            alert('Please enter a valid application ID');
            return;
        }
        
        // Ensure application type matches ID prefix
        const idPrefix = cleanId.substring(0, 5); // Get first 5 characters (BIRTH or DEATH)
        if ((applicationType === 'birth' && idPrefix !== 'BIRTH') || 
            (applicationType === 'death' && idPrefix !== 'DEATH')) {
            e.preventDefault();
            alert('Application type does not match the ID format');
        }
    });
    </script>
</body>
</html> 