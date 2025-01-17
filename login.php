<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Birth and Death Certificate System - Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            background-color: #b5dcb3;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .container {
            background-color: #b5dcb3;
            padding: 50px;
            border-radius: 20px;
            width: 500px;
            box-shadow: 9px 9px 16px #88b489, -9px -9px 16px #e2ffe2;
            text-align: center;
            transition: transform 0.3s ease-in-out;
            margin: 20px;
        }

        @media (max-width: 768px) {
            .container {
                width: 90%;
                padding: 30px;
                margin: 15px;
            }

            h2 {
                font-size: 24px;
            }

            h3 {
                font-size: 20px;
            }

            .logo img {
                width: 100px;
            }

            a.button {
                padding: 12px 24px;
                font-size: 16px;
                margin: 8px;
                width: calc(100% - 16px);
                box-sizing: border-box;
            }
        }

        @media (max-width: 480px) {
            .container {
                padding: 20px;
                margin: 10px;
            }

            h2 {
                font-size: 20px;
            }

            h3 {
                font-size: 18px;
            }

            .logo img {
                width: 80px;
            }

            a.button {
                padding: 10px 20px;
                font-size: 14px;
                margin: 6px;
            }
        }

        @media (max-width: 360px) {
            .container {
                padding: 15px;
                margin: 8px;
            }

            h2 {
                font-size: 18px;
            }

            h3 {
                font-size: 16px;
            }

            .logo img {
                width: 70px;
            }

            a.button {
                padding: 8px 16px;
                font-size: 13px;
                margin: 5px;
            }
        }

        .container:hover {
            transform: scale(1.05); /* Scale up on hover */
        }

        h2 {
            margin-bottom: 20px;
            color: #2c662c; /* Darker green */
        }

        h3 {
            margin-bottom: 30px;
            color: #2c662c; /* Darker green */
        }

        .logo img {
            width: 120px;
            margin-bottom: 20px;
        }

        a.button {
            display: inline-block;
            padding: 14px 28px;
            margin: 10px;
            font-size: 18px;
            color: white;
            background-color: #228b22; /* More vibrant green button */
            text-decoration: none;
            border-radius: 12px;
            box-shadow: 6px 6px 10px #88b489, -6px -6px 10px #e2ffe2; /* Neomorphism for button with brighter green tones */
            transition: background-color 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
        }

        a.button:hover {
            background-color: #196619; /* Darker vibrant green on hover */
            box-shadow: 9px 9px 16px #88b489, -9px -9px 16px #e2ffe2; /* Enhanced shadow on hover */
            transform: translateY(-2px);
        }

        h2, h3 {
            color: #2c662c;;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <img src="logo.png" alt="Logo">
        </div>
        <h2>Online Birth and Death Certificate System</h2>
        <h3>Select Login Type</h3>

        <a href="user_login.php" class="button">User Login</a>
        <a href="admin_login.php" class="button">Admin Login</a>
    </div>
</body>
</html>
