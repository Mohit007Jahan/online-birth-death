<?php
session_start();
require_once 'dbconfig.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

try {
    // Fetch birth certificate statistics
    $stmt = $conn->prepare("SELECT 
        COUNT(*) as total_birth,
        SUM(YEAR(application_date) = 2022) as birth_2022,
        SUM(YEAR(application_date) = 2023) as birth_2023,
        SUM(YEAR(application_date) = 2024) as birth_2024
        FROM tblbirthapplications");
    $stmt->execute();
    $birth_stats = $stmt->fetch(PDO::FETCH_ASSOC);

    // Fetch death certificate statistics
    $stmt = $conn->prepare("SELECT 
        COUNT(*) as total_death,
        SUM(YEAR(application_date) = 2022) as death_2022,
        SUM(YEAR(application_date) = 2023) as death_2023,
        SUM(YEAR(application_date) = 2024) as death_2024
        FROM tbldeathapplications");
    $stmt->execute();
    $death_stats = $stmt->fetch(PDO::FETCH_ASSOC);

    // Fetch yearly statistics for chart
    $stmt = $conn->prepare("
        SELECT 
            years.year,
            COALESCE(birth.count, 0) as birth_count,
            COALESCE(death.count, 0) as death_count
        FROM (
            SELECT 2015 as year
            UNION SELECT 2016
            UNION SELECT 2017
            UNION SELECT 2018
            UNION SELECT 2019
            UNION SELECT 2020
            UNION SELECT 2021
            UNION SELECT 2022
            UNION SELECT 2023
            UNION SELECT 2024
        ) years
        LEFT JOIN (
            SELECT YEAR(application_date) as year, COUNT(*) as count
            FROM tblbirthapplications
            GROUP BY YEAR(application_date)
        ) birth ON years.year = birth.year
        LEFT JOIN (
            SELECT YEAR(application_date) as year, COUNT(*) as count
            FROM tbldeathapplications
            GROUP BY YEAR(application_date)
        ) death ON years.year = death.year
        ORDER BY years.year
    ");
    $stmt->execute();
    $yearly_stats = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch recent applications
    $stmt = $conn->prepare("SELECT * FROM (
        SELECT 'Birth Certificate' as category, registration_no as si_no, name, 
            CONCAT('Father''s Birth C, Mother, Birth C, Mother''s face verification') as documents,
            application_date as added_date
        FROM tblbirthapplications
                         UNION ALL
        SELECT 'Death Certificate' as category, registration_no as si_no, name,
            CONCAT('Hospital''s copy, Wife/Spouse''s face verification') as documents,
            application_date as added_date
        FROM tbldeathapplications
    ) combined ORDER BY added_date DESC LIMIT 10");
    $stmt->execute();
    $recent_applications = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Add monthly statistics for current year
    $stmt = $conn->prepare("
        SELECT 
            MONTH(application_date) as month,
            COUNT(CASE WHEN table_name = 'birth' THEN 1 END) as birth_count,
            COUNT(CASE WHEN table_name = 'death' THEN 1 END) as death_count
        FROM (
            SELECT application_date, 'birth' as table_name FROM tblbirthapplications WHERE YEAR(application_date) = YEAR(CURRENT_DATE)
            UNION ALL
            SELECT application_date, 'death' as table_name FROM tbldeathapplications WHERE YEAR(application_date) = YEAR(CURRENT_DATE)
        ) combined
        GROUP BY MONTH(application_date)
        ORDER BY month
    ");
    $stmt->execute();
    $monthly_stats = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Add status distribution
    $stmt = $conn->prepare("
        SELECT 
            status,
            COUNT(CASE WHEN table_name = 'birth' THEN 1 END) as birth_count,
            COUNT(CASE WHEN table_name = 'death' THEN 1 END) as death_count
        FROM (
            SELECT status, 'birth' as table_name FROM tblbirthapplications
            UNION ALL
            SELECT status, 'death' as table_name FROM tbldeathapplications
        ) combined
        GROUP BY status
    ");
    $stmt->execute();
    $status_stats = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    error_log("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Birth & Death Certificate System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
            display: flex;
            flex-direction: column;
        }

        .main-wrapper {
            display: flex;
            flex: 1;
        }

        .header {
            background: #81c784;
            padding: 15px 20px;
            color: white;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            flex-wrap: wrap;
            gap: 10px;
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

        .sidebar {
            width: 250px;
            background-color: #004d00;
            padding: 20px;
            min-height: calc(100vh - 80px);
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
        }

        .sidebar a {
            display: block;
            color: white;
            padding: 12px 15px;
            text-decoration: none;
            margin-bottom: 10px;
            border-radius: 8px;
            transition: all 0.3s ease;
            background-color: rgba(255,255,255,0.1);
            box-shadow: 2px 2px 5px rgba(0,0,0,0.1);
        }

        .sidebar a:hover, .sidebar a.active {
            background-color: #007700;
            transform: translateX(5px);
        }

        .sidebar a i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }

        .content-wrapper {
            flex: 1;
            padding: 20px;
            background: #d4edda;
            box-shadow: inset 2px 2px 5px rgba(0,0,0,0.05);
            overflow-x: hidden;
        }

        .panel-title {
            background: white;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
            color: #004d00;
            font-size: 24px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .stats-box {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .stats-box h3 {
            color: #004d00;
            margin-bottom: 15px;
            font-size: 18px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
        }

        .stat-item {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 6px;
            text-align: center;
        }

        .stat-item .label {
            color: #666;
            font-size: 14px;
        }

        .stat-item .value {
            color: #004d00;
            font-size: 20px;
            font-weight: bold;
        }

        .chart-container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            min-height: 300px;
        }

        .chart-container h3 {
            margin-bottom: 15px;
            color: #004d00;
            text-align: center;
        }

        .applications-table {
            width: 100%;
            overflow-x: auto;
            display: block;
            white-space: nowrap;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-top: 20px;
        }

        .applications-table table {
            width: 100%;
            border-collapse: collapse;
        }

        .applications-table th,
        .applications-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        .applications-table th {
            background: #004d00;
            color: white;
            position: sticky;
            top: 0;
        }

        @media (max-width: 1024px) {
            .chart-grid {
                grid-template-columns: 1fr;
            }
            
            .stats-container {
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            }
        }

        @media (max-width: 768px) {
            .main-wrapper {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
                min-height: auto;
            }

            .header {
                flex-direction: column;
                text-align: center;
            }

            .logo-section {
                justify-content: center;
            }

            .user-info {
                justify-content: center;
            }

            .content-wrapper {
                padding: 10px;
            }

            .chart-container {
                min-height: 250px;
            }
        }

        @media (max-width: 480px) {
            .stats-container {
                grid-template-columns: 1fr;
            }

            .chart-container {
                min-height: 200px;
            }

            .header h1 {
                font-size: 20px;
            }

            .content-wrapper {
                padding: 5px;
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
            <img src="admin.png" alt="Admin">
            <span><?php echo htmlspecialchars($_SESSION['admin_email'] ?? ''); ?></span>
            <a href="admin_logout.php" class="logout-btn">Logout</a>
        </div>
    </div>

    <div class="main-wrapper">
        <div class="sidebar">
            <a href="admin_dashboard.php" class="active">
                <i class="fas fa-home"></i> Home
            </a>
            <a href="admin_profile.php">
                <i class="fas fa-user"></i> View Profile
            </a>
            <a href="pending_requests.php">
                <i class="fas fa-clock"></i> Pending Request
            </a>
            <a href="admin_search_user.php">
                <i class="fas fa-users"></i> View User's Profile
            </a>
            <a href="admin_view_birth1.php">
                <i class="fas fa-certificate"></i> Birth Certificate Status
            </a>
            <a href="admin_view_death1.php">
                <i class="fas fa-scroll"></i> Death Certificate Status
            </a>
        </div>

        <div class="content-wrapper">
            <div class="panel-title">Admin Panel</div>

            <h2 style="margin-bottom: 20px; color: #004d00;">Listed Added schedules</h2>

            <div class="stats-container">
                <div class="stats-box">
                    <h3>Birth Certificates</h3>
                    <div class="stats-grid">
                        <div class="stat-item">
                            <div class="label">Total on site</div>
                            <div class="value"><?php echo number_format($birth_stats['total_birth']); ?></div>
                        </div>
                        <div class="stat-item">
                            <div class="label">Registered on 2022</div>
                            <div class="value"><?php echo number_format($birth_stats['birth_2022']); ?></div>
                        </div>
                        <div class="stat-item">
                            <div class="label">Registered on 2023</div>
                            <div class="value"><?php echo number_format($birth_stats['birth_2023']); ?></div>
                        </div>
                        <div class="stat-item">
                            <div class="label">Registered on 2024</div>
                            <div class="value"><?php echo number_format($birth_stats['birth_2024']); ?></div>
                        </div>
                    </div>
                </div>

                <div class="stats-box">
                    <h3>Death Certificates</h3>
                    <div class="stats-grid">
                        <div class="stat-item">
                            <div class="label">Total on site</div>
                            <div class="value"><?php echo number_format($death_stats['total_death']); ?></div>
                        </div>
                        <div class="stat-item">
                            <div class="label">Registered on 2022</div>
                            <div class="value"><?php echo number_format($death_stats['death_2022']); ?></div>
                        </div>
                        <div class="stat-item">
                            <div class="label">Registered on 2023</div>
                            <div class="value"><?php echo number_format($death_stats['death_2023']); ?></div>
                        </div>
                        <div class="stat-item">
                            <div class="label">Registered on 2024</div>
                            <div class="value"><?php echo number_format($death_stats['death_2024']); ?></div>
                </div>
                </div>
                </div>
            </div>

            <div class="chart-grid">
                <div class="chart-container">
                    <h3>Yearly Trends</h3>
                    <canvas id="certificatesChart"></canvas>
                </div>
                <div class="chart-container">
                    <h3>Monthly Trends (<?php echo date('Y'); ?>)</h3>
                    <canvas id="monthlyChart"></canvas>
                </div>
            </div>

            <div class="chart-grid">
                <div class="chart-container">
                    <h3>Status Distribution</h3>
                    <canvas id="statusChart"></canvas>
                </div>
                <div class="chart-container">
                    <h3>Yearly Growth Trend</h3>
                    <canvas id="trendLineChart"></canvas>
                </div>
            </div>

            <div class="applications-table">
                <table>
                    <thead>
                        <tr>
                            <th>SI No.</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Added Documents</th>
                            <th>Added Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent_applications as $app): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($app['si_no']); ?></td>
                            <td><?php echo htmlspecialchars($app['name']); ?></td>
                            <td><?php echo htmlspecialchars($app['category']); ?></td>
                            <td><?php echo htmlspecialchars($app['documents']); ?></td>
                            <td><?php echo date('H:i:s d-m-Y', strtotime($app['added_date'])); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        // Initialize the chart
        const ctx = document.getElementById('certificatesChart').getContext('2d');
        const yearlyStats = <?php echo json_encode($yearly_stats); ?>;
        
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: yearlyStats.map(stat => stat.year),
                datasets: [{
                    label: 'Birth Certificates',
                    data: yearlyStats.map(stat => stat.birth_count),
                    backgroundColor: '#004d00',
                    borderColor: '#004d00',
                    borderWidth: 1
                }, {
                    label: 'Death Certificates',
                    data: yearlyStats.map(stat => stat.death_count),
                    backgroundColor: '#81c784',
                    borderColor: '#81c784',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Certificate Registration Trends'
                    }
                }
            }
        });

        // Monthly Chart
        const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
        const monthlyStats = <?php echo json_encode($monthly_stats); ?>;
        new Chart(monthlyCtx, {
            type: 'bar',
            data: {
                labels: monthlyStats.map(stat => {
                    const date = new Date(2024, stat.month - 1);
                    return date.toLocaleString('default', { month: 'short' });
                }),
                datasets: [{
                    label: 'Birth Certificates',
                    data: monthlyStats.map(stat => stat.birth_count),
                    backgroundColor: '#004d00',
                    borderColor: '#004d00',
                    borderWidth: 1
                }, {
                    label: 'Death Certificates',
                    data: monthlyStats.map(stat => stat.death_count),
                    backgroundColor: '#81c784',
                    borderColor: '#81c784',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: { y: { beginAtZero: true } }
            }
        });

        // Status Distribution Chart
        const statusCtx = document.getElementById('statusChart').getContext('2d');
        const statusStats = <?php echo json_encode($status_stats); ?>;
        new Chart(statusCtx, {
            type: 'pie',
            data: {
                labels: statusStats.map(stat => stat.status),
                datasets: [{
                    data: statusStats.map(stat => stat.birth_count + stat.death_count),
                    backgroundColor: [
                        '#004d00',
                        '#81c784',
                        '#e8f5e9'
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'right' }
                }
            }
        });

        // Trend Line Chart
        const trendCtx = document.getElementById('trendLineChart').getContext('2d');
        new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: yearlyStats.map(stat => stat.year),
                datasets: [{
                    label: 'Birth Certificates',
                    data: yearlyStats.map(stat => stat.birth_count),
                    borderColor: '#004d00',
                    tension: 0.3,
                    fill: false
                }, {
                    label: 'Death Certificates',
                    data: yearlyStats.map(stat => stat.death_count),
                    borderColor: '#81c784',
                    tension: 0.3,
                    fill: false
                }]
            },
            options: {
                responsive: true,
                scales: { y: { beginAtZero: true } }
            }
        });
    </script>
</body>
</html>