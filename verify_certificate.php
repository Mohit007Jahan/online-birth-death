<?php
require_once 'dbconfig.php';

$error = '';
$certificate = null;
$type = '';

if (isset($_GET['code']) && isset($_GET['reg'])) {
    $code = trim($_GET['code']);
    $reg_no = trim($_GET['reg']);
    
    try {
        // Try birth certificate first
        $stmt = $conn->prepare("
            SELECT 
                'birth' as type,
                ba.*,
                DATE_FORMAT(ba.date_of_birth, '%d %M %Y') as formatted_date,
                DATE_FORMAT(ba.application_date, '%d %M %Y') as formatted_application_date
            FROM tblbirthapplications ba
            WHERE ba.registration_no = ?
            AND ba.status != 'cancelled'
        ");
        $stmt->execute([$reg_no]);
        $certificate = $stmt->fetch();

        // If not found, try death certificate
        if (!$certificate) {
            $stmt = $conn->prepare("
                SELECT 
                    'death' as type,
                    da.*,
                    DATE_FORMAT(da.date_of_death, '%d %M %Y') as formatted_date,
                    DATE_FORMAT(da.application_date, '%d %M %Y') as formatted_application_date
                FROM tbldeathapplications da
                WHERE da.registration_no = ?
                AND da.status != 'cancelled'
            ");
            $stmt->execute([$reg_no]);
            $certificate = $stmt->fetch();
        }

        if ($certificate) {
            $type = $certificate['type'];
            // Verify the hash
            $verificationCode = hash('sha256', $certificate['id'] . $certificate['registration_no'] . 
                ($type === 'birth' ? $certificate['date_of_birth'] : $certificate['date_of_death']));
            
            if (substr($verificationCode, 0, 12) !== $code) {
                $error = 'Invalid verification code';
                $certificate = null;
            }
        } else {
            $error = 'Certificate not found';
        }
    } catch(PDOException $e) {
        error_log("Verification Error: " . $e->getMessage());
        $error = 'An error occurred during verification';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Certificate</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #b5dcb3;
            margin: 0;
            padding: 20px;
            min-height: 100vh;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header h1 {
            color: #008000;
            margin-bottom: 10px;
        }

        .verification-form {
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            color: #333;
        }

        input[type="text"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }

        .btn {
            background: #008000;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        .btn:hover {
            opacity: 0.9;
        }

        .error {
            color: #dc3545;
            margin-bottom: 20px;
        }

        .success {
            color: #28a745;
            margin-bottom: 20px;
        }

        .certificate-details {
            border-top: 2px solid #eee;
            padding-top: 20px;
        }

        .detail-group {
            margin-bottom: 15px;
        }

        .detail-label {
            font-weight: bold;
            color: #008000;
        }

        .verification-status {
            text-align: center;
            font-size: 24px;
            margin: 20px 0;
            padding: 10px;
            border-radius: 4px;
        }

        .verified {
            background: #d4edda;
            color: #155724;
        }

        .not-verified {
            background: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Certificate Verification</h1>
            <p>Verify the authenticity of birth and death certificates</p>
        </div>

        <div class="verification-form">
            <form method="GET" action="">
                <div class="form-group">
                    <label for="reg">Registration Number:</label>
                    <input type="text" id="reg" name="reg" value="<?php echo isset($_GET['reg']) ? htmlspecialchars($_GET['reg']) : ''; ?>" required>
                </div>
                <div class="form-group">
                    <label for="code">Verification Code:</label>
                    <input type="text" id="code" name="code" value="<?php echo isset($_GET['code']) ? htmlspecialchars($_GET['code']) : ''; ?>" required>
                </div>
                <button type="submit" class="btn">Verify Certificate</button>
            </form>
        </div>

        <?php if ($error): ?>
            <div class="verification-status not-verified">
                <i class="fas fa-times-circle"></i> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php elseif ($certificate): ?>
            <div class="verification-status verified">
                <i class="fas fa-check-circle"></i> Certificate Verified Successfully
            </div>
            <div class="certificate-details">
                <h2><?php echo $type === 'birth' ? 'Birth' : 'Death'; ?> Certificate Details</h2>
                <div class="detail-group">
                    <div class="detail-label">Registration Number:</div>
                    <div><?php echo htmlspecialchars($certificate['registration_no']); ?></div>
                </div>
                <div class="detail-group">
                    <div class="detail-label">Name:</div>
                    <div><?php echo htmlspecialchars($certificate['name']); ?></div>
                </div>
                <div class="detail-group">
                    <div class="detail-label"><?php echo $type === 'birth' ? 'Date of Birth' : 'Date of Death'; ?>:</div>
                    <div><?php echo htmlspecialchars($certificate['formatted_date']); ?></div>
                </div>
                <div class="detail-group">
                    <div class="detail-label">Application Date:</div>
                    <div><?php echo htmlspecialchars($certificate['formatted_application_date']); ?></div>
                </div>
                <div class="detail-group">
                    <div class="detail-label">Status:</div>
                    <div><?php echo htmlspecialchars($certificate['status']); ?></div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>
</html> 