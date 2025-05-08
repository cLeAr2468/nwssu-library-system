<?php
include '../admin_panel/dash-function.php';
include '../admin_panel/side_nav.php';

// Get date range filter
$dateRange = $_GET['date_range'] ?? 'last_30_days';
$reportType = $_GET['report_type'] ?? 'borrowing';

// Calculate date range
$endDate = date('Y-m-d');
switch ($dateRange) {
    case 'last_7_days':
        $startDate = date('Y-m-d', strtotime('-7 days'));
        break;
    case 'last_30_days':
        $startDate = date('Y-m-d', strtotime('-30 days'));
        break;
    case 'last_3_months':
        $startDate = date('Y-m-d', strtotime('-3 months'));
        break;
    case 'last_year':
        $startDate = date('Y-m-d', strtotime('-1 year'));
        break;
    default:
        $startDate = date('Y-m-d', strtotime('-30 days'));
}

// Get statistics
try {
    // Total Borrowings
    $borrowedQuery = $conn->prepare("
        SELECT COUNT(*) as total_borrowed 
        FROM borrowed_books 
        WHERE borrowed_date BETWEEN ? AND ?
    ");
    $borrowedQuery->execute([$startDate, $endDate]);
    $totalBorrowed = $borrowedQuery->fetch(PDO::FETCH_ASSOC)['total_borrowed'];

    // Active Users
    $activeUsersQuery = $conn->prepare("
        SELECT COUNT(DISTINCT user_id) as active_users 
        FROM borrowed_books 
        WHERE borrowed_date BETWEEN ? AND ?
    ");
    $activeUsersQuery->execute([$startDate, $endDate]);
    $activeUsers = $activeUsersQuery->fetch(PDO::FETCH_ASSOC)['active_users'];

    // Fine Collection
    $finesQuery = $conn->prepare("
        SELECT COALESCE(SUM(bb.fine), 0) as total_fines 
        FROM borrowed_books bb
        JOIN return_books rb ON bb.user_id = rb.user_id AND bb.book_id = rb.book_id
        WHERE bb.borrowed_date BETWEEN ? AND ?
        AND bb.status = 'returned'
    ");
    $finesQuery->execute([$startDate, $endDate]);
    $totalFines = $finesQuery->fetch(PDO::FETCH_ASSOC)['total_fines'];

    // Return Rate
    $returnedQuery = $conn->prepare("
        SELECT COUNT(*) as total_returned 
        FROM return_books 
        WHERE return_date BETWEEN ? AND ?
    ");
    $returnedQuery->execute([$startDate, $endDate]);
    $totalReturned = $returnedQuery->fetch(PDO::FETCH_ASSOC)['total_returned'];
    
    $returnRate = $totalBorrowed > 0 ? round(($totalReturned / $totalBorrowed) * 100) : 0;

    // Get recent activities
    $activitiesQuery = $conn->prepare("
        SELECT a.*, u.first_name, u.last_name 
        FROM activities a 
        LEFT JOIN user_info u ON a.user_id = u.user_id 
        ORDER BY a.activity_date DESC 
        LIMIT 5
    ");
    $activitiesQuery->execute();
    $recentActivities = $activitiesQuery->fetchAll(PDO::FETCH_ASSOC);

    // Get borrowing trends data for chart
    $trendsQuery = $conn->prepare("
        SELECT 
            DATE(borrowed_date) as date,
            COUNT(*) as borrowed_count
        FROM borrowed_books 
        WHERE borrowed_date BETWEEN ? AND ?
        GROUP BY DATE(borrowed_date)
        ORDER BY date
    ");
    $trendsQuery->execute([$startDate, $endDate]);
    $borrowingTrends = $trendsQuery->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports & Analytics</title>
    <link rel="icon" type="image/png" href="../images/logo.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.lineicons.com/4.0/lineicons.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            200: '#bae6fd',
                            300: '#7dd3fc',
                            400: '#38bdf8',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                            800: '#075985',
                            900: '#0c4a6e',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50">
    <div class="p-4 lg:ml-64 mt-14">
        <div class="p-4 rounded-lg">
            <!-- Header Section -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Reports & Analytics</h1>
                    <p class="text-gray-600">Library performance metrics and statistics</p>
                </div>
                <div class="mt-4 md:mt-0 flex space-x-2">
                    <button onclick="exportReport()" class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-50 transition duration-200 flex items-center">
                        <i class="lni lni-download mr-2"></i> Export Report
                    </button>
                    <button onclick="printReport()" class="bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700 transition duration-200 flex items-center">
                        <i class="lni lni-printer mr-2"></i> Print Report
                    </button>
                </div>
            </div>

            <!-- Date Range Filter -->
            <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100 mb-6">
                <form id="filterForm" class="flex flex-col md:flex-row items-start md:items-center space-y-4 md:space-y-0 md:space-x-4">
                    <div class="flex items-center space-x-2">
                        <label class="text-sm font-medium text-gray-700">Date Range:</label>
                        <select name="date_range" class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                            <option value="last_7_days" <?= $dateRange === 'last_7_days' ? 'selected' : '' ?>>Last 7 days</option>
                            <option value="last_30_days" <?= $dateRange === 'last_30_days' ? 'selected' : '' ?>>Last 30 days</option>
                            <option value="last_3_months" <?= $dateRange === 'last_3_months' ? 'selected' : '' ?>>Last 3 months</option>
                            <option value="last_year" <?= $dateRange === 'last_year' ? 'selected' : '' ?>>Last year</option>
                        </select>
                    </div>
                    <div class="flex items-center space-x-2">
                        <label class="text-sm font-medium text-gray-700">Report Type:</label>
                        <select name="report_type" class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                            <option value="borrowing" <?= $reportType === 'borrowing' ? 'selected' : '' ?>>Borrowing Statistics</option>
                            <option value="user_activity" <?= $reportType === 'user_activity' ? 'selected' : '' ?>>User Activity</option>
                            <option value="fine_collection" <?= $reportType === 'fine_collection' ? 'selected' : '' ?>>Fine Collection</option>
                            <option value="book_inventory" <?= $reportType === 'book_inventory' ? 'selected' : '' ?>>Book Inventory</option>
                        </select>
                    </div>
                    <button type="submit" class="bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700 transition duration-200">
                        Generate Report
                    </button>
                </form>
            </div>

            <!-- Key Metrics -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                <!-- Total Borrowings -->
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Borrowings</p>
                            <p class="text-2xl font-bold text-gray-800"><?= $totalBorrowed ?></p>
                        </div>
                        <div class="bg-blue-100 p-3 rounded-full">
                            <i class="lni lni-book text-blue-600 text-xl"></i>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="flex items-center text-sm">
                            <span class="text-gray-500">Period: <?= date('M d', strtotime($startDate)) ?> - <?= date('M d', strtotime($endDate)) ?></span>
                        </div>
                    </div>
                </div>

                <!-- Active Users -->
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Active Users</p>
                            <p class="text-2xl font-bold text-gray-800"><?= $activeUsers ?></p>
                        </div>
                        <div class="bg-green-100 p-3 rounded-full">
                            <i class="lni lni-users text-green-600 text-xl"></i>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="flex items-center text-sm">
                            <span class="text-gray-500">Unique borrowers</span>
                        </div>
                    </div>
                </div>

                <!-- Fine Collection -->
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Fine Collection</p>
                            <p class="text-2xl font-bold text-gray-800">₱<?= number_format($totalFines, 2) ?></p>
                        </div>
                        <div class="bg-yellow-100 p-3 rounded-full">
                            <i class="lni lni-coin text-yellow-600 text-xl"></i>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="flex items-center text-sm">
                            <span class="text-gray-500">Total fines collected</span>
                        </div>
                    </div>
                </div>

                <!-- Return Rate -->
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Return Rate</p>
                            <p class="text-2xl font-bold text-gray-800"><?= $returnRate ?>%</p>
                        </div>
                        <div class="bg-purple-100 p-3 rounded-full">
                            <i class="lni lni-checkmark text-purple-600 text-xl"></i>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="flex items-center text-sm">
                            <span class="text-gray-500">Books returned on time</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Borrowing Trends Chart -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Borrowing Trends</h3>
                <canvas id="borrowingTrendsChart"></canvas>
            </div>

            <!-- User Logs Card -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 mb-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Recent User Activities</h3>
                    <a href="activity_logs.php" class="text-primary-600 hover:text-primary-800 text-sm font-medium flex items-center">
                        View All Logs
                        <i class="lni lni-arrow-right ml-1"></i>
                    </a>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Activity</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($recentActivities as $activity): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        <?= htmlspecialchars($activity['first_name'] . ' ' . $activity['last_name']) ?>
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        <?= htmlspecialchars($activity['user_id']) ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">
                                        <?= htmlspecialchars($activity['activity_details']) ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?= date('M d, Y h:i A', strtotime($activity['activity_date'])) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        <?= $activity['status'] === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                                        <?= htmlspecialchars($activity['status']) ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Initialize Charts
        document.addEventListener('DOMContentLoaded', function() {
            // Borrowing Trends Chart
            const borrowingTrendsCtx = document.getElementById('borrowingTrendsChart').getContext('2d');
            new Chart(borrowingTrendsCtx, {
                type: 'line',
                data: {
                    labels: <?= json_encode(array_column($borrowingTrends, 'date')) ?>,
                    datasets: [{
                        label: 'Books Borrowed',
                        data: <?= json_encode(array_column($borrowingTrends, 'borrowed_count')) ?>,
                        borderColor: '#0ea5e9',
                        tension: 0.4,
                        fill: false
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    }
                }
            });
        });

        // Export Report
        function exportReport() {
            const dateRange = document.querySelector('select[name="date_range"]').value;
            const reportType = document.querySelector('select[name="report_type"]').value;
            window.location.href = `export_report.php?date_range=${dateRange}&report_type=${reportType}`;
        }

        // Print Report
        function printReport() {
            window.print();
        }
    </script>
</body>
</html> 