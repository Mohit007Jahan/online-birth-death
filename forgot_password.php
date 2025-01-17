<?php
require_once('dbconfig.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $identifier = $_POST['identifier']; // Can be email or mobile number

    // Check if the user exists
    $stmt = $conn->prepare("SELECT * FROM tbluser WHERE MobileNumber = :identifier OR Email = :identifier");
    $stmt->bindParam(':identifier', $identifier);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        $email = $user['Email'];

        // Generate a 5-digit reset code
        $resetCode = rand(10000, 99999);

        // Set token expiry time (e.g., 30 minutes from now)
        $tokenExpiry = date("Y-m-d H:i:s", strtotime('+30 minutes'));

        // Save the reset code and token expiry in the database
        $updateStmt = $conn->prepare("UPDATE tbluser SET reset_token = :reset_token, token_expiry = :token_expiry WHERE Email = :email");
        $updateStmt->bindParam(':reset_token', $resetCode);
        $updateStmt->bindParam(':token_expiry', $tokenExpiry);
        $updateStmt->bindParam(':email', $email);
        $updateStmt->execute();

        // Send the reset code via email (for demonstration, we're just displaying it)
        // In a real application, you would send the code via email here

        // Redirect to reset_password.php with a success message
        echo "<script>
                alert('A password reset code has been sent to your email address.');
                window.location.href = 'reset_password.php';
              </script>";
        exit;
    } else {
        $error = "No account found with that email or mobile number.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Online System</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            background-color: #b5dcb3; /* Green background */
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .forgot-password-container {
            background-color: #b5dcb3; /* Matching the background for neomorphism */
            padding: 50px;
            border-radius: 20px;
            width: 500px;
            box-shadow: 9px 9px 16px #88b489, -9px -9px 16px #e2ffe2; /* Neomorphism effect */
            text-align: left;
            transition: transform 0.3s ease-in-out;
        }

        .forgot-password-container:hover {
            transform: scale(1.05); /* Scale up on hover */
        }

        .forgot-password-container h2 {
            margin-bottom: 20px;
            color: #2c662c; /* Darker green */
            text-align: center;
        }

        .forgot-password-container label {
            font-weight: bold;
            margin-bottom: 10px;
            display: block;
            color: #2c662c; /* Darker green */
        }

        .forgot-password-container input[type="text"],
        .forgot-password-container input[type="submit"] {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border-radius: 12px;
            border: none;
            font-size: 16px;
            box-shadow: inset 6px 6px 10px #88b489, inset -6px -6px 10px #e2ffe2; /* Neomorphism for inputs */
            background-color: #b5dcb3;
            transition: box-shadow 0.3s ease-in-out;
        }

        .forgot-password-container input[type="text"]:focus,
        .forgot-password-container input[type="submit"]:focus {
            box-shadow: inset 9px 9px 16px #88b489, inset -9px -9px 16px #e2ffe2; /* Deepened shadow on focus */
            outline: none;
        }

        .forgot-password-container input[type="submit"] {
            background-color: #228b22; /* More vibrant green button */
            color: white;
            cursor: pointer;
            box-shadow: 6px 6px 10px #88b489, -6px -6px 10px #e2ffe2; /* Neomorphism for button */
            transition: background-color 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
        }


        .forgot-password-container input[type="submit"]:hover {
            background-color: #196619; /* Darker green on hover */
            box-shadow: 9px 9px 16px #88b489, -9px -9px 16px #e2ffe2; /* Enhanced shadow on hover */
        }
    </style>
</head>
<body>
    <div class="forgot-password-container">
        <h2>Forgot Password</h2>
        <?php if (isset($error)) { ?>
            <p style="color: red;"><?php echo $error; ?></p>
        <?php } ?>
        <form method="POST" action="">
            <label for="identifier">Enter your Email or Mobile Number:</label>
            <input type="text" name="identifier" id="identifier" placeholder="abc@gub.ac.bd or 01*********" required>
            <input type="submit" value="Send Reset Code">
        </form>
    </div>
</body>
</html>