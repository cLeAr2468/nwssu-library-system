<?php

session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ../admin_panel/index.php'); // Adjust the path to your login page
    exit();
}
// Database connection
include '../component-library/connect.php';
// Database connection
try {
    $conn = new PDO("mysql:host=$db_host;dbname=$db_name", $user_name, $user_password);
    // Set PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
} catch (PDOException $e) {
    die('Connection failed: ' . $e->getMessage());
}

// Query to get categories and their book counts, sorted alphabetically
$query = "SELECT category, COUNT(*) AS item_count FROM books GROUP BY category ORDER BY category ASC";
$categ = $conn->prepare($query);
$categ->execute();
$categories = $categ->fetchAll(PDO::FETCH_ASSOC);

// Check if the logout button is clicked
if (isset($_POST['logout'])) {
    // Unset student-specific session variables
    unset($_SESSION['user_id']);
    unset($_SESSION['user_name']);
    // Redirect to student login page
    header('Location: student_login.php');
    exit();
}

include '../admin_panel/side_nav.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categories</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
    <div class="min-h-screen p-4 ml-64"> <!-- Adjusted for sidebar -->
        <div class="mb-6">
            <div class="flex items-center">
                <h1 class="text-2xl font-bold text-gray-800">Dashboard</h1>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-semibold text-gray-800">Categories</h2>
                <div class="relative">
                    <input type="text" 
                           id="searchInput" 
                           placeholder="Search Category" 
                           class="pl-10 pr-4 py-2 border rounded-full w-64 focus:outline-none focus:ring-2 focus:ring-blue-500"
                           onkeyup="searchCategories()">
                    <i class="bi bi-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr class="bg-primary-600">
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Category</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Item(s)</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody id="categoriesTableBody" class="bg-white divide-y divide-gray-200">
                        <?php foreach ($categories as $category): ?>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <a href="categbooks.php?category=<?php echo urlencode($category['category']); ?>" 
                                       class="text-[#156295] font-bold hover:text-blue-800">
                                        <?php echo htmlspecialchars($category['category']); ?>
                                    </a>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-500">
                                    <?php echo htmlspecialchars($category['item_count']); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <a href="categbooks.php?category=<?php echo urlencode($category['category']); ?>" 
                                       class="text-blue-600 hover:text-blue-800 font-medium">
                                        Browse
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        function searchCategories() {
            const input = document.getElementById('searchInput').value.toLowerCase();
            const rows = document.querySelectorAll('#categoriesTableBody tr');
            rows.forEach(row => {
                const categoryName = row.cells[0].textContent.toLowerCase();
                row.style.display = categoryName.includes(input) ? '' : 'none';
            });
        }
    </script>
</body>
</html>