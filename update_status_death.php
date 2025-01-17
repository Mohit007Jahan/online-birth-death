<?php
// Start the session to use session variables
session_start();

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the application ID and new status from the form
    $application_id = $_POST['application_id'];
    $new_status = $_POST['status'];

    // Simulate updating the status (you can replace this with actual database logic later)
    if (!empty($application_id) && !empty($new_status)) {
        // Simulate success by setting a session variable
        $_SESSION['message'] = "Status for Application ID $application_id updated to '$new_status' successfully.";
        
        // Redirect back to the applications page
        header("Location: admin_view_death1.php");
        exit();
    } else {
        // Set an error message if inputs are not valid
        $_SESSION['error'] = "Invalid input. Please ensure all fields are filled.";
        header("Location: admin_view_death1.php");
        exit();
    }
} else {
    // If the request method is not POST, redirect back to the applications page
    header("Location: admin_view_death1.php");
    exit();
}
?>
