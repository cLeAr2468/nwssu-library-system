<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ../admin_panel/index.php');
    exit();
}
include '../component-library/connect.php';

// Check if user_id is provided
if (!isset($_GET['user_id'])) {
    header('Location: Student_list.php');
    exit();
}

$user_id = $_GET['user_id'];

try {
    $conn = new PDO("mysql:host=$db_host;dbname=$db_name", $user_name, $user_password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch user information
    $stmt = $conn->prepare("SELECT * FROM user_info WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        header('Location: Student_list.php');
        exit();
    }

    // Fetch borrowed books
    $stmt = $conn->prepare("
        SELECT bb.*, b.title AS book_title 
        FROM borrowed_books bb
        JOIN books b ON bb.book_id = b.id
        WHERE bb.user_id = ?
    ");
    $stmt->execute([$user_id]);
    $borrowed_books = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch reserved books
    $stmt = $conn->prepare("
        SELECT rb.*, b.title AS book_title 
        FROM reserve_books rb
        JOIN books b ON rb.book_id = b.id
        WHERE rb.user_id = ? AND rb.status = 'reserved'
    ");
    $stmt->execute([$user_id]);
    $reserved_books = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch overdue books
    $stmt = $conn->prepare("
        SELECT bb.*, b.title AS book_title 
        FROM borrowed_books bb
        JOIN books b ON bb.book_id = b.id
        WHERE bb.user_id = ? 
        AND bb.return_sched < CURRENT_DATE 
        AND bb.status = 'borrowed'
    ");
    $stmt->execute([$user_id]);
    $overdue_books = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

include '../admin_panel/side_nav.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NwSSU : User Records</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.lineicons.com/4.0/lineicons.css" rel="stylesheet" />
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
            <!-- Back Button -->
            <div class="mb-6">
                <button onclick="window.location.href='Student_list.php'" class="flex items-center text-gray-600 hover:text-gray-800">
                    <i class="lni lni-arrow-left mr-2"></i>
                    Back to User List
                </button>
            </div>

            <!-- User Info Section -->
            <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                <div class="flex justify-between items-start">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['middle_name'] . ' ' . $user['last_name']); ?></h2>
                        <p class="text-gray-600">ID: <?php echo htmlspecialchars($user['user_id']); ?></p>
                        <p class="text-gray-600">Email: <?php echo htmlspecialchars($user['email']); ?></p>
                        <p class="text-gray-600">Type: <?php echo htmlspecialchars($user['patron_type']); ?></p>
                    </div>
                    <div>
                        <span class="px-3 py-1 rounded-full <?php echo $user['account_status'] === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                            <?php echo htmlspecialchars($user['account_status']); ?>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                            <i class="lni lni-book text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-800">Borrowed Books</h3>
                            <p class="text-3xl font-bold text-gray-600"><?php echo count($borrowed_books); ?></p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                            <i class="lni lni-bookmark text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-800">Reserved Books</h3>
                            <p class="text-3xl font-bold text-gray-600"><?php echo count($reserved_books); ?></p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-red-100 text-red-600">
                            <i class="lni lni-warning text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-800">Overdue Books</h3>
                            <p class="text-3xl font-bold text-gray-600"><?php echo count($overdue_books); ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabs -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="border-b">
                    <nav class="flex" id="tabs">
                        <button class="px-6 py-3 text-gray-600 hover:text-gray-800 border-b-2 border-transparent hover:border-gray-300 tab-button active" data-tab="borrowed">
                            Borrowed Books
                        </button>
                        <button class="px-6 py-3 text-gray-600 hover:text-gray-800 border-b-2 border-transparent hover:border-gray-300 tab-button" data-tab="reserved">
                            Reserved Books
                        </button>
                        <button class="px-6 py-3 text-gray-600 hover:text-gray-800 border-b-2 border-transparent hover:border-gray-300 tab-button" data-tab="overdue">
                            Overdue Books
                        </button>
                    </nav>
                </div>

                <!-- Borrowed Books Table -->
                <div id="borrowed-content" class="tab-content active">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Book Title</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Borrow Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Due Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($borrowed_books as $book): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($book['book_title']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($book['borrowed_date']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($book['return_sched']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $book['status'] === 'returned' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'; ?>">
                                            <?php echo ucfirst(htmlspecialchars($book['status'])); ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Reserved Books Table -->
                <div id="reserved-content" class="tab-content hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Book Title</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reserve Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($reserved_books as $book): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($book['book_title']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($book['reserved_date']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            <?php echo ucfirst(htmlspecialchars($book['status'])); ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Overdue Books Table -->
                <div id="overdue-content" class="tab-content hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Book Title</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Due Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Days Overdue</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fine</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($overdue_books as $book): 
                                    $due_date = new DateTime($book['return_sched']);
                                    $today = new DateTime();
                                    $days_overdue = $today->diff($due_date)->days;
                                    $fine = $days_overdue * 5; // 5 pesos per day
                                ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($book['book_title']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($book['return_sched']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600"><?php echo $days_overdue; ?> days</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600">₱<?php echo number_format($fine, 2); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Tab functionality
        document.addEventListener('DOMContentLoaded', function() {
            const tabs = document.querySelectorAll('.tab-button');
            const contents = document.querySelectorAll('.tab-content');

            tabs.forEach(tab => {
                tab.addEventListener('click', () => {
                    // Remove active class from all tabs and contents
                    tabs.forEach(t => t.classList.remove('active', 'border-primary-600', 'text-primary-600'));
                    contents.forEach(c => c.classList.add('hidden'));

                    // Add active class to clicked tab and show corresponding content
                    tab.classList.add('active', 'border-primary-600', 'text-primary-600');
                    const contentId = tab.getAttribute('data-tab') + '-content';
                    document.getElementById(contentId).classList.remove('hidden');
                });
            });
        });
    </script>
</body>
</html>