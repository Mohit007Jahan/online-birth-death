<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register New Birth Certificate</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #b5dcb3;
            min-height: 100vh;
        }

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
            transition: transform 0.3s ease;
        }

        .logo-section img:hover {
            transform: scale(1.05);
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

        .sidebar a i {
            width: 20px;
            text-align: center;
        }

        .sidebar a:hover, .sidebar a.active {
            background-color: #007700;
            transform: translateX(5px);
        }

        .main-content {
            flex: 1;
            padding: 20px;
            background: #d4edda;
            box-shadow: inset 2px 2px 5px rgba(0,0,0,0.05);
        }

        .relationship-options {
            margin-top: 20px;
            animation: fadeIn 0.5s ease-in-out;
        }

        .relationship-options label {
            display: inline-block;
            margin-right: 15px;
            background: #81c784;
            padding: 10px 20px;
            border-radius: 8px;
            border: 1px solid #ccc;
            transition: all 0.3s ease;
            box-shadow: 2px 2px 5px rgba(0,0,0,0.1);
            cursor: pointer;
        }

        .relationship-options label:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }

        .relationship-options input[type="radio"]:checked + label {
            background: #004400;
            color: white;
            transform: scale(1.05);
        }

        .aliveness-check {
            margin-top: 30px;
            padding: 25px;
            background: white;
            border-radius: 12px;
            border: 1px solid #ccc;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            animation: slideUp 0.5s ease-in-out;
        }

        .aliveness-check img {
            display: block;
            margin: 20px auto;
            width: 150px;
            height: auto;
            animation: scaleIn 0.5s ease-in-out;
        }

        .btn-ready {
            display: inline-block;
            padding: 12px 25px;
            background: #007700;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 2px 2px 5px rgba(0,0,0,0.1);
            font-weight: bold;
        }

        .btn-ready:hover {
            background: #003300;
            transform: scale(1.05);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        @keyframes slideUp {
            from {
                transform: translateY(20px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        @keyframes scaleIn {
            from {
                transform: scale(0.9);
                opacity: 0;
            }
            to {
                transform: scale(1);
                opacity: 1;
            }
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
                flex-direction: row;
            }

            .logo-section {
                width: auto;
                display: flex;
                align-items: center;
                gap: 10px;
            }

            .logo-section img {
                width: 30px;
                height: 30px;
            }

            .logo-section h1 {
                font-size: 16px;
                color: white;
            }

            .user-info {
                display: flex;
                align-items: center;
                gap: 10px;
            }

            .user-info img {
                width: 30px;
                height: 30px;
            }

            .user-info span {
                color: white;
                font-size: 14px;
            }

            .logout-btn {
                padding: 6px 12px;
                font-size: 14px;
                background: #004d00;
            }

            .sidebar {
                position: fixed;
                top: 60px;
                left: 0;
                right: 0;
                bottom: auto;
                width: 100%;
                height: 50px;
                padding: 5px;
                display: flex;
                overflow-x: auto;
                overflow-y: hidden;
                white-space: nowrap;
                z-index: 900;
            }

            .sidebar a {
                flex: 0 0 auto;
                margin: 0 3px;
                padding: 8px 12px;
                font-size: 13px;
                border-radius: 15px;
                display: flex;
                align-items: center;
                background: rgba(255,255,255,0.1);
            }

            .sidebar a i {
                margin-right: 5px;
            }

            .main-content {
                margin-left: 0;
                margin-top: 110px;
                padding: 10px;
            }

            .relationship-options {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 10px;
                margin: 15px 0;
            }

            .relationship-options label {
                margin: 0;
                width: 100%;
                text-align: center;
                font-size: 14px;
                padding: 8px 12px;
            }

            .aliveness-check {
                margin-top: 20px;
                padding: 15px;
            }

            .aliveness-check img {
                width: 120px;
                margin: 15px auto;
            }

            .btn-ready {
                width: 100%;
                max-width: 200px;
                padding: 10px 20px;
                font-size: 14px;
            }
        }

        @media (max-width: 480px) {
            .header {
                height: 50px;
                padding: 8px;
            }

            .logo-section h1 {
                font-size: 14px;
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
                height: 45px;
                padding: 5px;
            }

            .sidebar a {
                padding: 6px 10px;
                font-size: 12px;
            }

            .main-content {
                margin-top: 95px;
                padding: 8px;
            }

            .relationship-options {
                grid-template-columns: 1fr;
            }

            .relationship-options label {
                font-size: 13px;
                padding: 8px;
            }

            .aliveness-check {
                padding: 12px;
            }

            .aliveness-check img {
                width: 100px;
            }

            .aliveness-check p {
                font-size: 13px;
            }

            .btn-ready {
                padding: 8px 16px;
                font-size: 13px;
            }
        }

        @media (max-width: 360px) {
            .logo-section h1 {
                font-size: 13px;
            }

            .user-info span {
                display: none;
            }

            .sidebar a {
                padding: 5px 8px;
                font-size: 11px;
            }

            .main-content {
                margin-top: 95px;
                padding: 5px;
            }

            .relationship-options label {
                font-size: 12px;
                padding: 6px;
            }

            .aliveness-check {
                padding: 10px;
            }

            .aliveness-check img {
                width: 90px;
            }

            .aliveness-check p {
                font-size: 12px;
            }

            .btn-ready {
                padding: 6px 12px;
                font-size: 12px;
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
            <img src="get_profile_image.php" alt="User">
            <span><?php echo htmlspecialchars($_SESSION['email'] ?? ''); ?></span>
            <a href="user_logout.php" class="logout-btn">Logout</a>
        </div>
    </div>

    <div class="container">
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

        <!-- Main Content -->
        <div class="main-content">
            <form action="user_application_birth2.php" method="POST">
                <h2>Relationship with you-</h2>
                <div class="relationship-options">
                <label><input type="radio" name="relationship" value="own" required> Own</label>
                    <label><input type="radio" name="relationship" value="son" required> Son</label>
                    <label><input type="radio" name="relationship" value="daughter"> Daughter</label>
                    <label><input type="radio" name="relationship" value="wife"> Wife</label>
                    <label><input type="radio" name="relationship" value="husband"> Husband</label>
                    <label><input type="radio" name="relationship" value="mother"> Mother</label>
                    <label><input type="radio" name="relationship" value="father"> Father</label>
                    <label><input type="radio" name="relationship" value="brother"> Brother</label>
                    <label><input type="radio" name="relationship" value="sister"> Sister</label>
                </div>

                <h2>Verify it’s you registering!</h2>
                <div class="aliveness-check">
                    <p>Aliveness Check</p>
                    <p>Center your whole face in the frame and follow the on-screen interactions.</p>
                    <img src="face authentication.png" alt="Aliveness Check Image" />
                    <p class="instructions">
                        • Don't hide parts of your face with hats or glasses.<br>
                        • Ensure good lighting, neither too dark nor too bright.
                    </p>
                    <button type="submit" class="btn-ready">I'm Ready</button>
                </div>
            </form>
        </div>
    </div>

</body>
</html>
