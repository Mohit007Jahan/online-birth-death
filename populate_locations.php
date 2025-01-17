<?php
require_once 'config.php';
require_once 'dbconfig.php';

try {
    // Start transaction
    $conn->beginTransaction();

    // Get Dhaka Division ID
    $stmt = $conn->query("SELECT id FROM divisions WHERE name = 'Dhaka'");
    $dhakaDivisionId = $stmt->fetchColumn();

    if ($dhakaDivisionId) {
        // Insert districts for Dhaka Division
        $districts = [
            ['name' => 'Dhaka', 'bn_name' => 'ঢাকা'],
            ['name' => 'Gazipur', 'bn_name' => 'গাজীপুর'],
            ['name' => 'Narayanganj', 'bn_name' => 'নারায়ণগঞ্জ'],
            ['name' => 'Tangail', 'bn_name' => 'টাঙ্গাইল'],
            ['name' => 'Narsingdi', 'bn_name' => 'নরসিংদী']
        ];

        $stmt = $conn->prepare("INSERT IGNORE INTO districts (division_id, name, bn_name) VALUES (?, ?, ?)");
        foreach ($districts as $district) {
            $stmt->execute([$dhakaDivisionId, $district['name'], $district['bn_name']]);
        }

        // Get Dhaka District ID
        $stmt = $conn->query("SELECT id FROM districts WHERE name = 'Dhaka'");
        $dhakaDistrictId = $stmt->fetchColumn();

        if ($dhakaDistrictId) {
            // Insert upazilas for Dhaka District
            $upazilas = [
                ['name' => 'Savar', 'bn_name' => 'সাভার'],
                ['name' => 'Dhamrai', 'bn_name' => 'ধামরাই'],
                ['name' => 'Keraniganj', 'bn_name' => 'কেরাণীগঞ্জ'],
                ['name' => 'Dohar', 'bn_name' => 'দোহার'],
                ['name' => 'Nawabganj', 'bn_name' => 'নবাবগঞ্জ']
            ];

            $stmt = $conn->prepare("INSERT IGNORE INTO upazilas (district_id, name, bn_name) VALUES (?, ?, ?)");
            foreach ($upazilas as $upazila) {
                $stmt->execute([$dhakaDistrictId, $upazila['name'], $upazila['bn_name']]);
            }

            // Get Savar Upazila ID
            $stmt = $conn->query("SELECT id FROM upazilas WHERE name = 'Savar'");
            $savarUpazilaId = $stmt->fetchColumn();

            if ($savarUpazilaId) {
                // Insert unions for Savar Upazila
                $unions = [
                    ['name' => 'Savar Pouroshova', 'bn_name' => 'সাভার পৌরসভা', 'type' => 'pouroshova'],
                    ['name' => 'Ashulia', 'bn_name' => 'আশুলিয়া', 'type' => 'union'],
                    ['name' => 'Birulia', 'bn_name' => 'বিরুলিয়া', 'type' => 'union'],
                    ['name' => 'Yearpur', 'bn_name' => 'ইয়ারপুর', 'type' => 'union'],
                    ['name' => 'Dhamsona', 'bn_name' => 'ধামসোনা', 'type' => 'union']
                ];

                $stmt = $conn->prepare("INSERT IGNORE INTO unions (upazila_id, name, bn_name, type) VALUES (?, ?, ?, ?)");
                foreach ($unions as $union) {
                    $stmt->execute([$savarUpazilaId, $union['name'], $union['bn_name'], $union['type']]);
                }
            }
        }
    }

    // Get Chittagong Division ID
    $stmt = $conn->query("SELECT id FROM divisions WHERE name = 'Chittagong'");
    $chittagongDivisionId = $stmt->fetchColumn();

    if ($chittagongDivisionId) {
        // Insert districts for Chittagong Division
        $districts = [
            ['name' => 'Chittagong', 'bn_name' => 'চট্টগ্রাম'],
            ['name' => "Cox's Bazar", 'bn_name' => 'কক্সবাজার'],
            ['name' => 'Rangamati', 'bn_name' => 'রাঙ্গামাটি'],
            ['name' => 'Bandarban', 'bn_name' => 'বান্দরবান'],
            ['name' => 'Khagrachari', 'bn_name' => 'খাগড়াছড়ি']
        ];

        $stmt = $conn->prepare("INSERT IGNORE INTO districts (division_id, name, bn_name) VALUES (?, ?, ?)");
        foreach ($districts as $district) {
            $stmt->execute([$chittagongDivisionId, $district['name'], $district['bn_name']]);
        }
    }

    // Commit transaction
    $conn->commit();
    echo "Location data populated successfully!";

} catch (Exception $e) {
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }
    echo "Error: " . $e->getMessage();
} 