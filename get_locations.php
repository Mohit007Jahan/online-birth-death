<?php
require_once 'config.php';
require_once 'dbconfig.php';

header('Content-Type: application/json');

try {
    $type = $_GET['type'] ?? '';
    $parent_id = $_GET['parent_id'] ?? 0;

    switch ($type) {
        case 'divisions':
            $stmt = $conn->query("SELECT id, name, bn_name FROM divisions ORDER BY name");
            echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
            break;

        case 'districts':
            $division_id = (int)$parent_id;
            $stmt = $conn->prepare("SELECT id, name, bn_name FROM districts WHERE division_id = ? ORDER BY name");
            $stmt->execute([$division_id]);
            echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
            break;

        case 'upazilas':
            $district_id = (int)$parent_id;
            $stmt = $conn->prepare("SELECT id, name, bn_name FROM upazilas WHERE district_id = ? ORDER BY name");
            $stmt->execute([$district_id]);
            echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
            break;

        case 'unions':
            $upazila_id = (int)$parent_id;
            $stmt = $conn->prepare("SELECT id, name, bn_name, type FROM unions WHERE upazila_id = ? ORDER BY type, name");
            $stmt->execute([$upazila_id]);
            echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
            break;

        default:
            throw new Exception('Invalid request type');
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
} 