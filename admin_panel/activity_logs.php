<?php
session_start();
include '../component-library/connect.php';


// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: admin_login.php');
    exit();
}

// Get filter parameters
$search = isset($_GET['search']) ? $_GET['search'] : '';
$activityType = isset($_GET['activity_type']) ? $_GET['activity_type'] : '';
$startDate = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$endDate = isset($_GET['end_date']) ? $_GET['end_date'] : '';

// Build query conditions
$conditions = [];
$params = [];

if (!empty($search)) {
    $conditions[] = "(u.first_name LIKE :search OR u.last_name LIKE :search OR u.user_id LIKE :search OR a.activity_details LIKE :search)";
    $params[':search'] = "%$search%";
}

if (!empty($activityType)) {
    $conditions[] = "a.activity_type = :activity_type";
    $params[':activity_type'] = $activityType;
}

if (!empty($startDate)) {
    $conditions[] = "a.activity_date >= :start_date";
    $params[':start_date'] = $startDate . ' 00:00:00';
}

if (!empty($endDate)) {
    $conditions[] = "a.activity_date <= :end_date";
    $params[':end_date'] = $endDate . ' 23:59:59';
}

$whereClause = !empty($conditions) ? 'WHERE ' . implode(' AND ', $conditions) : '';

// Handle CSV export
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="activity_logs_' . date('Y-m-d') . '.csv"');

    $output = fopen('php://output', 'w');
    fputcsv($output, ['User ID', 'Name', 'Patron Type', 'Activity Type', 'Details', 'Date', 'Status', 'IP Address']);

    $exportQuery = $conn->prepare("
        SELECT a.*, u.first_name, u.last_name, u.patron_type 
        FROM activities a 
        LEFT JOIN user_info u ON a.user_id = u.user_id 
        $whereClause
        ORDER BY a.activity_date DESC
    ");

    foreach ($params as $key => $value) {
        $exportQuery->bindValue($key, $value);
    }
    $exportQuery->execute();

    while ($row = $exportQuery->fetch(PDO::FETCH_ASSOC)) {
        fputcsv($output, [
            $row['user_id'],
            $row['first_name'] . ' ' . $row['last_name'],
            $row['patron_type'],
            $row['activity_type'],
            $row['activity_details'],
            $row['activity_date'],
            $row['status'],
            $row['ip_address'] ?? 'N/A'
        ]);
    }
    fclose($output);
    exit();
}

// Pagination
$limit = 20;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Get total number of activities
$countQuery = $conn->prepare("SELECT COUNT(*) FROM activities a LEFT JOIN user_info u ON a.user_id = u.user_id $whereClause");
foreach ($params as $key => $value) {
    $countQuery->bindValue($key, $value);
}
$countQuery->execute();
$totalActivities = $countQuery->fetchColumn();
$totalPages = ceil($totalActivities / $limit);

// Fetch activities with user details
$query = $conn->prepare("
    SELECT a.*, u.first_name, u.last_name, u.patron_type 
    FROM activities a 
    LEFT JOIN user_info u ON a.user_id = u.user_id 
    $whereClause
    ORDER BY a.activity_date DESC 
    LIMIT :limit OFFSET :offset
");

foreach ($params as $key => $value) {
    $query->bindValue($key, $value);
}
$query->bindValue(':limit', $limit, PDO::PARAM_INT);
$query->bindValue(':offset', $offset, PDO::PARAM_INT);
$query->execute();
$activities = $query->fetchAll(PDO::FETCH_ASSOC);

// Get unique activity types for filter dropdown
$activityTypes = $conn->query("SELECT DISTINCT activity_type FROM activities ORDER BY activity_type")->fetchAll(PDO::FETCH_COLUMN);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activity Logs - NWSSU Library System</title>
    <link rel="icon" type="image/png" href="../images/logo.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
</head>

<body class="bg-gray-100">
    <?php include '../admin_panel/side_nav.php'; ?>
    <div class="p-4 lg:ml-64 mt-14">
        <div class="p-4 rounded-lg">
            <div class="mb-6">
                <button onclick="window.location.href='reports.php'" class="flex items-center text-gray-600 hover:text-gray-800">
                    <i class="lni lni-arrow-left mr-2"></i>
                    Back to Reports
                </button>
            </div>
            <div class="container mx-auto px-4 py-8">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h1 class="text-2xl font-bold">Activity Logs</h1>
                        <a href="?export=csv&search=<?php echo urlencode($search); ?>&activity_type=<?php echo urlencode($activityType); ?>&start_date=<?php echo urlencode($startDate); ?>&end_date=<?php echo urlencode($endDate); ?>"
                            class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-md flex items-center">
                            <i class="fas fa-download mr-2"></i> Export CSV
                        </a>
                    </div>

                    <!-- Filters -->
                    <div class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <input type="text"
                                name="search"
                                placeholder="Search users or details..."
                                value="<?php echo htmlspecialchars($search); ?>"
                                class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <select name="activity_type" class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">All Activity Types</option>
                                <?php foreach ($activityTypes as $type): ?>
                                    <option value="<?php echo htmlspecialchars($type); ?>" <?php echo $activityType === $type ? 'selected' : ''; ?>>
                                        <?php echo ucfirst(htmlspecialchars($type)); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <input type="text"
                                name="start_date"
                                placeholder="Start Date"
                                value="<?php echo htmlspecialchars($startDate); ?>"
                                class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 datepicker">
                        </div>
                        <div>
                            <input type="text"
                                name="end_date"
                                placeholder="End Date"
                                value="<?php echo htmlspecialchars($endDate); ?>"
                                class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 datepicker">
                        </div>
                    </div>

                    <!-- Activity Logs Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Activity Type</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Details</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <?php foreach ($activities as $activity): ?>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">
                                                <?php echo htmlspecialchars($activity['first_name'] . ' ' . $activity['last_name']); ?>
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                <?php echo htmlspecialchars($activity['user_id']); ?>
                                            </div>
                                            <div class="text-xs text-gray-400">
                                                <?php echo htmlspecialchars($activity['patron_type']); ?>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        <?php echo $activity['activity_type'] === 'login' ? 'bg-green-100 text-green-800' : ($activity['activity_type'] === 'logout' ? 'bg-red-100 text-red-800' :
                                                'bg-blue-100 text-blue-800'); ?>">
                                                <?php echo htmlspecialchars($activity['activity_type']); ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-gray-900">
                                                <?php echo htmlspecialchars($activity['activity_details']); ?>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <?php echo date('M d, Y h:i A', strtotime($activity['activity_date'])); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        <?php echo $activity['status'] === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                                <?php echo htmlspecialchars($activity['status']); ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-4 flex justify-center">
                        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                            <?php if ($page > 1): ?>
                                <a href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>&activity_type=<?php echo urlencode($activityType); ?>&start_date=<?php echo urlencode($startDate); ?>&end_date=<?php echo urlencode($endDate); ?>"
                                    class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                    <span class="sr-only">Previous</span>
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            <?php endif; ?>

                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&activity_type=<?php echo urlencode($activityType); ?>&start_date=<?php echo urlencode($startDate); ?>&end_date=<?php echo urlencode($endDate); ?>"
                                    class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium 
                           <?php echo $i === $page ? 'text-blue-600 bg-blue-50' : 'text-gray-700 hover:bg-gray-50'; ?>">
                                    <?php echo $i; ?>
                                </a>
                            <?php endfor; ?>

                            <?php if ($page < $totalPages): ?>
                                <a href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>&activity_type=<?php echo urlencode($activityType); ?>&start_date=<?php echo urlencode($startDate); ?>&end_date=<?php echo urlencode($endDate); ?>"
                                    class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                    <span class="sr-only">Next</span>
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            <?php endif; ?>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Initialize date pickers
        flatpickr(".datepicker", {
            dateFormat: "Y-m-d",
            allowInput: true
        });

        // Auto-submit form when filters change
        document.querySelectorAll('input[name="search"], select[name="activity_type"], input[name="start_date"], input[name="end_date"]').forEach(element => {
            element.addEventListener('change', function() {
                const search = document.querySelector('input[name="search"]').value;
                const activityType = document.querySelector('select[name="activity_type"]').value;
                const startDate = document.querySelector('input[name="start_date"]').value;
                const endDate = document.querySelector('input[name="end_date"]').value;

                window.location.href = `?page=1&search=${encodeURIComponent(search)}&activity_type=${encodeURIComponent(activityType)}&start_date=${encodeURIComponent(startDate)}&end_date=${encodeURIComponent(endDate)}`;
            });
        });
    </script>
</body>

</html>