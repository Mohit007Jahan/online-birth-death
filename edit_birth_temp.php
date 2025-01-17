<?php
session_start();
require_once 'dbconfig.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit();
}

// Check if application ID is provided
if (!isset($_GET['id'])) {
    header('Location: admin_view_birth1.php');
    exit();
}

$application_id = $_GET['id'];

try {
    // Fetch application details
    $stmt = $pdo->prepare("SELECT * FROM tblbirthapplications WHERE id = ?");
    $stmt->execute([$application_id]);
    $application = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$application) {
        $_SESSION['error'] = "Application not found.";
        header('Location: admin_view_birth1.php');
        exit();
    }

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        error_log("Processing birth application update for ID: " . $application_id);
        error_log("Form data received: " . print_r($_POST, true));

        try {
            // Sanitize and validate input
            $name = trim($_POST['name']);
            $date_of_birth = trim($_POST['date_of_birth']);
            $place_of_birth = trim($_POST['place_of_birth']);
            $gender = trim($_POST['gender']);
            $father_name = trim($_POST['father_name']);
            $father_brn = trim($_POST['father_brn']);
            $father_nid = trim($_POST['father_nid']);
            $father_occupation = trim($_POST['father_occupation']);
            $mother_name = trim($_POST['mother_name']);
            $mother_brn = trim($_POST['mother_brn']);
            $mother_nid = trim($_POST['mother_nid']);
            $mother_occupation = trim($_POST['mother_occupation']);
            $nationality = trim($_POST['nationality']);
            $blood_group = trim($_POST['blood_group']);
            $permanent_address = trim($_POST['permanent_address']);
            $division = trim($_POST['division']);
            $district = trim($_POST['district']);
            $upazila = trim($_POST['upazila']);
            $union_pouroshova = trim($_POST['union_pouroshova']);
            $order_of_child = trim($_POST['order_of_child']);
            $occupation = trim($_POST['occupation']);

            // Handle hospital paper upload
            $hospital_paper = $application['hospital_paper']; // Keep existing value by default
            if (isset($_FILES['hospital_paper']) && $_FILES['hospital_paper']['error'] === UPLOAD_ERR_OK) {
                $upload_dir = 'uploads/hospital_papers/';
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }

                // Delete old file if it exists
                if (!empty($application['hospital_paper'])) {
                    $old_file = $upload_dir . $application['hospital_paper'];
                    if (file_exists($old_file)) {
                        unlink($old_file);
                    }
                }

                // Upload new file
                $file_extension = pathinfo($_FILES['hospital_paper']['name'], PATHINFO_EXTENSION);
                $new_filename = uniqid('hospital_', true) . '.' . $file_extension;
                $upload_path = $upload_dir . $new_filename;

                if (move_uploaded_file($_FILES['hospital_paper']['tmp_name'], $upload_path)) {
                    $hospital_paper = $new_filename;
                }
            }

            // Update application
            $sql = "UPDATE tblbirthapplications SET 
                name = ?, date_of_birth = ?, place_of_birth = ?, gender = ?,
                father_name = ?, father_brn = ?, father_nid = ?, father_occupation = ?,
                mother_name = ?, mother_brn = ?, mother_nid = ?, mother_occupation = ?,
                nationality = ?, blood_group = ?, permanent_address = ?,
                division = ?, district = ?, upazila = ?, union_pouroshova = ?,
                hospital_paper = ?, order_of_child = ?, occupation = ?
                WHERE id = ?";

            $params = [
                $name, $date_of_birth, $place_of_birth, $gender,
                $father_name, $father_brn, $father_nid, $father_occupation,
                $mother_name, $mother_brn, $mother_nid, $mother_occupation,
                $nationality, $blood_group, $permanent_address,
                $division, $district, $upazila, $union_pouroshova,
                $hospital_paper, $order_of_child, $occupation,
                $application_id
            ];

            error_log("Executing update with params: " . print_r($params, true));
            
            $stmt = $pdo->prepare($sql);
            if ($stmt->execute($params)) {
                $_SESSION['success'] = "Application updated successfully.";
                header('Location: admin_view_birth_application.php?id=' . $application_id);
                exit();
            } else {
                error_log("Database error: " . print_r($stmt->errorInfo(), true));
                throw new Exception("Failed to update application.");
            }
        } catch (Exception $e) {
            error_log("Error updating birth application: " . $e->getMessage());
            $_SESSION['error'] = "A database error occurred. Please try again later.";
        }
    }
} catch (Exception $e) {
    error_log("Error in edit_birth_application.php: " . $e->getMessage());
    $_SESSION['error'] = "An error occurred. Please try again later.";
    header('Location: admin_view_birth1.php');
    exit();
}
?> 