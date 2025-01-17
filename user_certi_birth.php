<?php
session_start();
require_once 'dbconfig.php';

// Check if user is logged in
if (!isset($_SESSION['uid'])) {
    header("Location: user_login.php");
    exit();
}

// Check if certificate ID is provided
if (!isset($_GET['id'])) {
    header("Location: user_added_birth.php");
    exit();
}

try {
    // Fetch certificate details with user information
    $stmt = $conn->prepare("
        SELECT b.*, u.FirstName, u.LastName, u.Email, u.MobileNumber 
        FROM tblbirthapplications b
        JOIN tbluser u ON b.user_id = u.ID
        WHERE (b.id = ? OR b.ID = ? OR b.Id = ?) AND b.user_id = ?
    ");
    $stmt->execute([$_GET['id'], $_GET['id'], $_GET['id'], $_SESSION['uid']]);
    $cert = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$cert) {
        header("Location: user_added_birth.php");
        exit();
    }

    // Format date of birth
    $dob = new DateTime($cert['date_of_birth']);
    $dobInWords = $dob->format('jS M, Y');

    // Store user details
    $user = [
        'FirstName' => $cert['FirstName'],
        'LastName' => $cert['LastName'],
        'Email' => $cert['Email'],
        'MobileNumber' => $cert['MobileNumber']
    ];
} catch(PDOException $e) {
    error_log("Error fetching birth certificate: " . $e->getMessage());
    header("Location: user_added_birth.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Birth Certificate</title>
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@700&display=swap" rel="stylesheet">
    <style>
        @media print {
            @page {
                size: A4 portrait;
                margin: 0;
            }
            
            body {
                margin: 0;
                padding: 0;
                background: none;
                visibility: hidden;
            }
            
            .header, .actions-container {
                display: none !important;
            }
            
            .content-wrapper {
                padding: 0 !important;
                margin: 0 !important;
                width: 100% !important;
                max-width: none !important;
            }
            
            .certificate-container {
                visibility: visible !important;
                position: absolute !important;
                top: 0 !important;
                left: 0 !important;
                margin: 0 !important;
                padding: 20px !important;
                width: 100% !important;
                height: auto !important;
                transform: none !important;
                box-shadow: none !important;
                border: none !important;
                box-sizing: border-box !important;
            }
            
            .certificate-content {
                margin: 0 40px !important;
                padding: 20px 0 !important;
                width: auto !important;
            }
            
            .info-grid {
                width: 100% !important;
                display: grid !important;
                grid-template-columns: 1fr 1fr !important;
                gap: 20px !important;
            }
            
            /* Ensure patterns are visible in print */
            .dot-pattern,
            .corner-decoration,
            .border-pattern,
            .scroll-pattern,
            .lotus-pattern {
                print-color-adjust: exact !important;
                -webkit-print-color-adjust: exact !important;
                visibility: visible !important;
            }
            
            .corner-decoration svg,
            .lotus-pattern svg {
                display: block !important;
                visibility: visible !important;
            }
            
            .verified-stamp {
                display: block !important;
                opacity: 1 !important;
                visibility: visible !important;
                position: absolute !important;
                right: 40px !important;
                bottom: 40px !important;
            }
            
            #qrcode {
                display: block !important;
                print-color-adjust: exact !important;
                -webkit-print-color-adjust: exact !important;
                visibility: visible !important;
            }
            
            h2, .info-item, .info-value, .parental-info {
                visibility: visible !important;
                color: #000 !important;
                print-color-adjust: exact !important;
                -webkit-print-color-adjust: exact !important;
            }
            
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
        }

        .header {
            background: #008000;
            padding: 15px 20px;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header img {
            height: 40px;
            margin-right: 15px;
        }

        .header h1 {
            font-size: 24px;
            margin: 0;
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

        .logout-btn {
            background: #006600;
            color: white;
            padding: 8px 15px;
            text-decoration: none;
            border-radius: 5px;
        }

        .content-wrapper {
            padding: 20px;
            max-width: 1000px;
            margin: 0 auto;
        }

        .certificate-container {
            background: white;
            padding: 40px;
            margin-bottom: 20px;
            position: relative;
            overflow: hidden;
            aspect-ratio: 1 / 1.414; /* A4 ratio */
            max-width: 100%;
            box-sizing: border-box;
        }

        /* Background dot pattern */
        .dot-pattern {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            opacity: 0.5;
            pointer-events: none;
            background-image: 
                radial-gradient(circle at 2px 2px, #008000 2px, transparent 0);
            background-size: 20px 20px;
            background-position: 0 0;
        }

        /* Corner decorations */
        .corner-decoration {
            position: absolute;
            width: 80px;
            height: 80px;
            z-index: 1;
        }

        .corner-decoration svg {
            width: 100%;
            height: 100%;
        }

        .corner-decoration circle {
            fill: rgba(144, 238, 144, 0.6);
        }

        .top-left {
            top: 20px;
            left: 20px;
        }

        .top-right {
            top: 20px;
            right: 20px;
            transform: rotate(90deg);
        }

        .bottom-left {
            bottom: 20px;
            left: 20px;
            transform: rotate(-90deg);
        }

        .bottom-right {
            bottom: 20px;
            right: 20px;
            transform: rotate(180deg);
        }

        /* Additional decorative patterns */
        .border-pattern {
            position: absolute;
            top: 30px;
            left: 30px;
            right: 30px;
            bottom: 30px;
            border: 2px solid rgba(0, 128, 0, 0.2);
            z-index: 1;
            pointer-events: none;
        }

        .border-pattern::before {
            content: '';
            position: absolute;
            top: 5px;
            left: 5px;
            right: 5px;
            bottom: 5px;
            border: 1px solid rgba(0, 128, 0, 0.15);
        }

        .scroll-pattern {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            z-index: 0;
            opacity: 0.15;
            pointer-events: none;
            background-image: 
                linear-gradient(45deg, transparent 48%, #008000 49%, #008000 51%, transparent 52%),
                linear-gradient(-45deg, transparent 48%, #008000 49%, #008000 51%, transparent 52%);
            background-size: 30px 30px;
        }

        /* Lotus pattern */
        .lotus-pattern {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            z-index: 0;
            opacity: 0.4;
            pointer-events: none;
            background-repeat: repeat;
        }

        .lotus-pattern svg {
            width: 100%;
            height: 100%;
            display: block;
        }

        .certificate-content {
            position: relative;
            z-index: 2;
            margin: 0 40px;
            height: 100%;
            display: flex;
            flex-direction: column;
            gap: 30px;
        }

        .certificate-container h2 {
            text-align: center;
            font-family: 'Dancing Script', cursive;
            font-size: clamp(32px, 6vw, 48px);
            color: #000;
            margin: 20px 0 40px;
            position: relative;
            z-index: 2;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: clamp(20px, 3vw, 40px);
            font-size: clamp(16px, 2.5vw, 22px);
        }

        .basic-info {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin-bottom: 30px;
        }

        .parental-info {
            margin-top: clamp(20px, 4vw, 40px);
        }

        .parental-info .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .applicant-info {
            margin-top: 20px;
            border-top: 1px solid rgba(0, 128, 0, 0.2);
            padding-top: 20px;
        }

        .applicant-info .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .info-item {
            margin-bottom: clamp(12px, 2.5vw, 20px);
        }

        .info-item strong {
            font-family: 'Dancing Script', cursive;
            font-size: clamp(16px, 2.5vw, 22px);
            color: #000;
            font-weight: normal;
        }

        .info-value {
            font-family: 'Dancing Script', cursive;
            font-size: clamp(16px, 2.5vw, 22px);
            margin-left: 5px;
        }

        #qrcode {
            position: absolute;
            top: 20px;
            right: 20px;
            z-index: 3;
        }

        #qrcode img {
            width: clamp(80px, 10vw, 100px);
            height: auto;
        }

        /* Responsive adjustments */
        @media screen and (max-width: 768px) {
            .certificate-container {
                padding: 20px;
            }
            
            .certificate-content {
                margin: 0 20px;
            }
            
            .info-grid {
                gap: 15px;
            }
        }

        @media screen and (max-width: 480px) {
            .certificate-container {
                padding: 15px;
            }
            
            .certificate-content {
                margin: 0 10px;
            }
            
            .info-grid {
                grid-template-columns: 1fr;
                gap: 10px;
            }
        }

        /* Print specific styles */
        @media print {
            @page {
                size: A4;
                margin: 0;
            }
            
            .certificate-container {
                width: 210mm;
                height: 297mm;
                margin: 0;
                padding: 20mm;
                box-sizing: border-box;
            }
            
            .certificate-content {
                margin: 0;
                height: 100%;
            }
            
            .info-grid {
                grid-template-columns: 1fr 1fr;
                gap: 5mm;
            }
            
            .info-item {
                margin-bottom: 3mm;
            }
            
            h2 {
                font-size: 32pt;
            }
            
            .info-item strong,
            .info-value {
                font-size: 14pt;
            }
            
            .parental-info h3,
            .applicant-info h3 {
                font-size: 20pt;
            }
            
            #qrcode img {
                width: 25mm;
                height: 25mm;
            }
            
            .verified-stamp {
                width: 30mm;
                height: auto;
            }
        }

        .actions-container {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 20px;
        }

        .action-btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            color: white;
        }

        .print-btn {
            background-color: #008000;
        }

        .exit-btn {
            background-color: #8B0000;
        }

        .applicant-info {
            margin-top: 20px;
            border-top: 1px solid rgba(0, 128, 0, 0.2);
            padding-top: 20px;
        }

        .applicant-info h3 {
            font-family: 'Dancing Script', cursive;
            font-size: clamp(24px, 4vw, 32px);
            color: #000;
            margin-bottom: clamp(15px, 3vw, 25px);
        }

        .parental-info h3, .applicant-info h3 {
            font-family: 'Dancing Script', cursive;
            font-size: clamp(24px, 4vw, 32px);
            color: #000;
            margin-bottom: clamp(15px, 3vw, 25px);
        }

        @media print {
            h2 {
                font-size: 32pt;
            }
            
            .info-item strong,
            .info-value {
                font-size: 14pt;
            }
            
            .parental-info h3,
            .applicant-info h3 {
                font-size: 20pt;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <div style="display: flex; align-items: center;">
            <img src="logo.png" alt="Logo">
            <h1>Online Birth & Death Certificate System</h1>
        </div>
        <div class="user-info">
            <img src="get_profile_image.php" alt="User">
            <span><?php echo htmlspecialchars($_SESSION['email'] ?? ''); ?></span>
            <a href="user_logout.php" class="logout-btn">Logout</a>
        </div>
    </div>

    <div class="content-wrapper">
        <div class="certificate-container">
            <!-- Background Patterns -->
            <div class="dot-pattern"></div>
            
            <!-- Corner Decorations -->
            <div class="corner-decoration top-left">
                <svg viewBox="0 0 80 80">
                    <circle cx="30" cy="30" r="25"/>
                    <circle cx="60" cy="60" r="15"/>
                </svg>
            </div>
            <div class="corner-decoration top-right">
                <svg viewBox="0 0 80 80">
                    <circle cx="30" cy="30" r="25"/>
                    <circle cx="60" cy="60" r="15"/>
                </svg>
            </div>
            <div class="corner-decoration bottom-left">
                <svg viewBox="0 0 80 80">
                    <circle cx="30" cy="30" r="25"/>
                    <circle cx="60" cy="60" r="15"/>
                </svg>
            </div>
            <div class="corner-decoration bottom-right">
                <svg viewBox="0 0 80 80">
                    <circle cx="30" cy="30" r="25"/>
                    <circle cx="60" cy="60" r="15"/>
                </svg>
            </div>

            <!-- Additional Patterns -->
            <div class="border-pattern"></div>
            <div class="scroll-pattern"></div>

            <!-- Lotus Pattern -->
            <div class="lotus-pattern">
                <svg viewBox="0 0 1000 1000" preserveAspectRatio="xMidYMid slice">
                    <defs>
                        <pattern id="lotus" x="0" y="0" width="40" height="40" patternUnits="userSpaceOnUse">
                            <g fill="rgba(0, 128, 0, 0.5)">
                                <!-- Center -->
                                <circle cx="20" cy="20" r="2"/>
                                <!-- Main Petals -->
                                <path d="M20,8 C24,12 24,16 20,20 C16,16 16,12 20,8"/>
                                <path d="M20,32 C24,28 24,24 20,20 C16,24 16,28 20,32"/>
                                <path d="M8,20 C12,24 16,24 20,20 C16,16 12,16 8,20"/>
                                <path d="M32,20 C28,24 24,24 20,20 C24,16 28,16 32,20"/>
                                <!-- Diagonal Petals -->
                                <path d="M14,14 C17,17 19,17 20,20 C17,19 17,17 14,14"/>
                                <path d="M26,14 C23,17 21,17 20,20 C23,19 23,17 26,14"/>
                                <path d="M14,26 C17,23 19,23 20,20 C17,21 17,23 14,26"/>
                                <path d="M26,26 C23,23 21,23 20,20 C23,21 23,23 26,26"/>
                                <!-- Decorative Lines -->
                                <path d="M20,8 C22,12 22,16 20,18 M20,8 C18,12 18,16 20,18" stroke="rgba(0, 128, 0, 0.5)" fill="none"/>
                                <path d="M8,20 C12,22 16,22 18,20 M8,20 C12,18 16,18 18,20" stroke="rgba(0, 128, 0, 0.5)" fill="none"/>
                                <path d="M32,20 C28,22 24,22 22,20 M32,20 C28,18 24,18 22,20" stroke="rgba(0, 128, 0, 0.5)" fill="none"/>
                                <path d="M20,32 C22,28 22,24 20,22 M20,32 C18,28 18,24 20,22" stroke="rgba(0, 128, 0, 0.5)" fill="none"/>
                            </g>
                        </pattern>
                    </defs>
                    <rect width="100%" height="100%" fill="url(#lotus)"/>
                </svg>
            </div>

            <h2>Birth Certificate</h2>

            <div class="certificate-content">
                <div class="info-grid">
                    <div class="left-column">
                        <div class="info-item">
                            <strong>Register No:</strong>
                            <span class="info-value"><?php echo htmlspecialchars($cert['registration_no']); ?></span>
                        </div>
                        <div class="info-item">
                            <strong>Date of Registration:</strong>
                            <span class="info-value"><?php echo date('d/m/Y', strtotime($cert['application_date'])); ?></span>
                        </div>
                        <div class="info-item">
                            <strong>BR Number:</strong>
                            <span class="info-value"><?php echo htmlspecialchars($cert['registration_no']); ?></span>
                        </div>
                        <div class="info-item">
                            <strong>Name:</strong>
                            <span class="info-value"><?php echo htmlspecialchars($cert['name']); ?></span>
                        </div>
                        <div class="info-item">
                            <strong>Date of Birth:</strong>
                            <span class="info-value"><?php echo date('d/m/Y', strtotime($cert['date_of_birth'])); ?></span>
                        </div>
                        <div class="info-item">
                            <strong>In Word:</strong>
                            <span class="info-value"><?php echo $dobInWords; ?></span>
                        </div>
                        <div class="info-item">
                            <strong>Place of Birth:</strong>
                            <span class="info-value"><?php echo htmlspecialchars($cert['place_of_birth']); ?></span>
                        </div>
                        <div class="info-item">
                            <strong>Permanent Address:</strong>
                            <span class="info-value"><?php echo htmlspecialchars($cert['permanent_address']); ?></span>
                        </div>
                    </div>

                    <div class="right-column">
                        <div class="info-item">
                            <strong>Sex:</strong>
                            <span class="info-value"><?php echo htmlspecialchars($cert['gender']); ?></span>
                        </div>
                        <div class="info-item">
                            <strong>Blood Group:</strong>
                            <span class="info-value"><?php echo htmlspecialchars($cert['blood_group']); ?></span>
                        </div>
                        <div class="info-item">
                            <strong>Marital Status:</strong>
                            <span class="info-value"><?php echo htmlspecialchars($cert['marital_status']); ?></span>
                        </div>
                        <div class="info-item">
                            <strong>Occupation:</strong>
                            <span class="info-value"><?php echo htmlspecialchars($cert['occupation']); ?></span>
                        </div>
                        <div class="info-item">
                            <strong>Order of Child:</strong>
                            <span class="info-value"><?php echo htmlspecialchars($cert['order_of_child']); ?></span>
                        </div>
                        <div id="qrcode"></div>
                    </div>
                </div>

                <!-- Location Details -->
                <div class="info-grid">
                    <div>
                        <div class="info-item">
                            <strong>Division:</strong>
                            <span class="info-value"><?php echo htmlspecialchars($cert['division']); ?></span>
                        </div>
                        <div class="info-item">
                            <strong>District:</strong>
                            <span class="info-value"><?php echo htmlspecialchars($cert['district']); ?></span>
                        </div>
                    </div>
                    <div>
                        <div class="info-item">
                            <strong>Upazila:</strong>
                            <span class="info-value"><?php echo htmlspecialchars($cert['upazila']); ?></span>
                        </div>
                        <div class="info-item">
                            <strong>Union/Pouroshova:</strong>
                            <span class="info-value"><?php echo htmlspecialchars($cert['union_pouroshova']); ?></span>
                        </div>
                    </div>
                </div>

                <div class="parental-info">
                    <h3>Parental Information</h3>
                    <div class="info-grid">
                        <div>
                            <div class="info-item">
                                <strong>Father's Name:</strong>
                                <span class="info-value"><?php echo htmlspecialchars($cert['father_name']); ?></span>
                            </div>
                            <div class="info-item">
                                <strong>Father's BRN:</strong>
                                <span class="info-value"><?php echo htmlspecialchars($cert['father_brn']); ?></span>
                            </div>
                            <div class="info-item">
                                <strong>Father's NID:</strong>
                                <span class="info-value"><?php echo htmlspecialchars($cert['father_nid']); ?></span>
                            </div>
                            <div class="info-item">
                                <strong>Father's Occupation:</strong>
                                <span class="info-value"><?php echo htmlspecialchars($cert['father_occupation']); ?></span>
                            </div>
                            <div class="info-item">
                                <strong>Father's Nationality:</strong>
                                <span class="info-value"><?php echo htmlspecialchars($cert['nationality']); ?></span>
                            </div>
                        </div>
                        <div>
                            <div class="info-item">
                                <strong>Mother's Name:</strong>
                                <span class="info-value"><?php echo htmlspecialchars($cert['mother_name']); ?></span>
                            </div>
                            <div class="info-item">
                                <strong>Mother's BRN:</strong>
                                <span class="info-value"><?php echo htmlspecialchars($cert['mother_brn']); ?></span>
                            </div>
                            <div class="info-item">
                                <strong>Mother's NID:</strong>
                                <span class="info-value"><?php echo htmlspecialchars($cert['mother_nid']); ?></span>
                            </div>
                            <div class="info-item">
                                <strong>Mother's Occupation:</strong>
                                <span class="info-value"><?php echo htmlspecialchars($cert['mother_occupation']); ?></span>
                            </div>
                            <div class="info-item">
                                <strong>Mother's Nationality:</strong>
                                <span class="info-value"><?php echo htmlspecialchars($cert['nationality']); ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="applicant-info">
                    <h3>Application Details</h3>
                    <div class="info-grid">
                        <div>
                            <div class="info-item">
                                <strong>Applied By:</strong>
                                <span class="info-value"><?php echo htmlspecialchars($user['FirstName'] . ' ' . $user['LastName']); ?></span>
                            </div>
                            <div class="info-item">
                                <strong>Relationship:</strong>
                                <span class="info-value"><?php echo htmlspecialchars($cert['relationship']); ?></span>
                            </div>
                        </div>
                        <div>
                            <div class="info-item">
                                <strong>Contact Number:</strong>
                                <span class="info-value"><?php echo htmlspecialchars($user['MobileNumber']); ?></span>
                            </div>
                            <div class="info-item">
                                <strong>Email:</strong>
                                <span class="info-value"><?php echo htmlspecialchars($user['Email']); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php if (strtolower($cert['status']) === 'approved'): ?>
                <img src="vari.png" class="verified-stamp">
            <?php endif; ?>
        </div>
        
        <div class="actions-container">
            <button class="action-btn print-btn" onclick="window.print()">Print Certificate</button>
            <button class="action-btn exit-btn" onclick="window.location.href='user_added_birth.php'">Exit Page</button>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.qrcode/1.0/jquery.qrcode.min.js"></script>
    <script>
        $(document).ready(function() {
            var qrData = "Register No: <?php echo $cert['registration_no']; ?>, Name: <?php echo $cert['name']; ?>, Date of Birth: <?php echo date('d/m/Y', strtotime($cert['date_of_birth'])); ?>";
            $('#qrcode').qrcode({
                text: qrData,
                width: 100,
                height: 100
            });
        });
    </script>
</body>
</html> "