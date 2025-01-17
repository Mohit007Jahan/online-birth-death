<?php
$error_code = $_SERVER['REDIRECT_STATUS'] ?? 404;
$error_messages = [
    400 => 'Bad Request',
    401 => 'Unauthorized',
    403 => 'Forbidden',
    404 => 'Page Not Found',
    500 => 'Internal Server Error'
];
$error_message = $error_messages[$error_code] ?? 'Unknown Error';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error <?php echo $error_code; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f0f8f0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .header {
            background-color: #1d991f;
            padding: 15px;
            color: white;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .header h1 img {
            height: 40px;
            margin-right: 15px;
        }

        .container {
            max-width: 800px;
            margin: 40px auto;
            padding: 0 20px;
            flex-grow: 1;
            text-align: center;
        }

        .error-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            padding: 40px;
            margin-bottom: 30px;
        }

        .error-icon {
            font-size: 64px;
            margin-bottom: 20px;
            color: #dc3545;
        }

        .error-code {
            font-size: 48px;
            font-weight: bold;
            color: #dc3545;
            margin-bottom: 20px;
        }

        .error-message {
            font-size: 24px;
            color: #666;
            margin-bottom: 30px;
        }

        .error-description {
            color: #666;
            margin-bottom: 30px;
            line-height: 1.6;
        }

        .button-group {
            display: flex;
            gap: 20px;
            justify-content: center;
            margin-top: 30px;
        }

        .btn {
            display: inline-block;
            padding: 12px 24px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            transition: background-color 0.3s;
        }

        .btn-primary {
            background-color: #1d991f;
            color: white;
        }

        .btn-primary:hover {
            background-color: #167c18;
        }

        .btn-secondary {
            background-color: #f8f9fa;
            color: #1d991f;
            border: 2px solid #1d991f;
        }

        .btn-secondary:hover {
            background-color: #e2e6ea;
        }

        .footer {
            background-color: #1d991f;
            color: white;
            text-align: center;
            padding: 20px;
            margin-top: auto;
        }

        .footer p {
            margin: 0;
        }

        @media (max-width: 768px) {
            .container {
                padding: 15px;
                margin: 20px auto;
            }

            .error-card {
                padding: 20px;
            }

            .button-group {
                flex-direction: column;
            }

            .btn {
                width: 100%;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>
            <img src="logo.png" alt="Logo">
            Online Birth & Death Certificate System
        </h1>
    </div>

    <div class="container">
        <div class="error-card">
            <i class="fas fa-exclamation-triangle error-icon"></i>
            <div class="error-code"><?php echo $error_code; ?></div>
            <div class="error-message"><?php echo htmlspecialchars($error_message); ?></div>
            
            <div class="error-description">
                <?php if ($error_code === 404): ?>
                    The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.
                <?php elseif ($error_code === 403): ?>
                    You don't have permission to access this resource.
                <?php elseif ($error_code === 401): ?>
                    Please log in to access this resource.
                <?php elseif ($error_code === 400): ?>
                    The request could not be understood by the server due to malformed syntax.
                <?php else: ?>
                    An unexpected error occurred. Please try again later.
                <?php endif; ?>
            </div>

            <div class="button-group">
                <a href="javascript:history.back()" class="btn btn-secondary">Go Back</a>
                <a href="index.php" class="btn btn-primary">Return to Home</a>
            </div>
        </div>
    </div>

    <div class="footer">
        <p>© <?php echo date('Y'); ?> Online Birth & Death Certificate System. All rights reserved.</p>
    </div>
</body>
</html> 