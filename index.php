<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Birth and Death Certificate System</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            text-align: center;
            background-color: #b5dcb3;
            margin: 0;
            padding: 20px;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .container {
            background-color: #b5dcb3;
            padding: 40px;
            border-radius: 20px;
            width: 100%;
            max-width: 800px;
            box-shadow: 9px 9px 16px #88b489, -9px -9px 16px #e2ffe2;
            text-align: center;
            transition: transform 0.3s ease-in-out;
            animation: fadeIn 0.8s ease-out;
        }

        .container:hover {
            transform: scale(1.02);
        }

        h1 {
            font-size: 28px;
            margin-bottom: 30px;
            color: #2c662c;
            padding: 0 20px;
            line-height: 1.3;
        }

        .logo {
            margin-bottom: 30px;
        }

        .logo img {
            width: 120px;
            height: 120px;
            object-fit: contain;
            border-radius: 50%;
            padding: 10px;
            background: #b5dcb3;
            box-shadow: 6px 6px 12px #88b489, -6px -6px 12px #e2ffe2;
            transition: transform 0.3s ease;
        }

        .logo img:hover {
            transform: scale(1.05);
        }

        .button-container {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 30px;
            flex-wrap: wrap;
            padding: 0 10px;
        }

        .button-container a {
            text-decoration: none;
            background-color: #228b22;
            color: white;
            padding: 15px 30px;
            font-size: 18px;
            font-weight: bold;
            border-radius: 12px;
            box-shadow: 6px 6px 10px #88b489, -6px -6px 10px #e2ffe2;
            transition: all 0.3s ease-in-out;
            min-width: 180px;
        }

        .button-container a:hover {
            background-color: #196619;
            box-shadow: 8px 8px 16px #88b489, -8px -8px 16px #e2ffe2;
            transform: translateY(-2px);
        }

        .help-center {
            margin-top: 40px;
            font-size: 16px;
            color: #2c662c;
            padding: 0 20px;
        }

        .help-center a {
            text-decoration: none;
            color: #228b22;
            font-weight: bold;
            transition: color 0.3s ease;
        }

        .help-center a:hover {
            color: #196619;
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            body {
                padding: 15px;
            }

            .container {
                padding: 30px 20px;
            }

            h1 {
                font-size: 24px;
                margin-bottom: 25px;
            }

            .logo img {
                width: 100px;
                height: 100px;
            }

            .button-container {
                gap: 15px;
            }

            .button-container a {
                padding: 12px 25px;
                font-size: 16px;
                min-width: 160px;
            }
        }

        @media (max-width: 480px) {
            body {
                padding: 10px;
            }

            .container {
                padding: 25px 15px;
            }

            h1 {
                font-size: 20px;
                margin-bottom: 20px;
            }

            .logo img {
                width: 80px;
                height: 80px;
            }

            .button-container {
                gap: 12px;
            }

            .button-container a {
                padding: 10px 20px;
                font-size: 15px;
                min-width: 140px;
            }

            .help-center {
                font-size: 14px;
                margin-top: 30px;
            }
        }

        @media (max-width: 360px) {
            .container {
                padding: 20px 10px;
            }

            h1 {
                font-size: 18px;
            }

            .button-container a {
                padding: 10px 15px;
                font-size: 14px;
                min-width: 130px;
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <img src="logo.png" alt="Logo">
        </div>
        <h1>Welcome to the Online Birth & Death Certificate System</h1>
        <div class="button-container">
            <a href="register.php">Registration</a>
            <a href="login.php">Login</a>
        </div>
        <div class="button-container" style="margin-top: 15px;">
            <a href="verification_status.php">Verification Status</a>
        </div>
        <div class="help-center">
            For more information, visit the <a href="help_center.php">Help Center</a>
        </div>
    </div>
</body>
</html>
