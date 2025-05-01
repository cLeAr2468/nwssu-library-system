<?php
session_start();
include '../component-library/connect.php';
// Fetch user reservations for display
$queryReserved = "
    SELECT rb.user_id, ui.last_name, ui.first_name, ui.middle_name, ui.patron_type, rb.book_id, b.title AS book_title, rb.copies, rb.status 
    FROM borrowed_books rb 
    JOIN user_info ui ON rb.user_id = ui.user_id 
    JOIN books b ON rb.book_id = b.id 
    WHERE rb.status = 'returned'";
$stmtReserved = $conn->prepare($queryReserved);
$stmtReserved->execute();
$reservedBooks = $stmtReserved->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NwSSU : Circulation</title>
    <link rel="icon" type="image/png" href="../images/logo.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.lineicons.com/4.0/lineicons.css" rel="stylesheet" />
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
    <!-- Sidebar -->
    <?php include '../admin_panel/side_nav.php'?>
    <div class="p-4 lg:ml-64 mt-14">
        <div class="p-4 rounded-lg">
            <!-- Header Section -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Circulation</h1>
                    <p class="text-gray-600">Manage book checkouts and returns</p>
                </div>
            </div>
            <!-- Main Content -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <!-- Users Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Books Title</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Patron Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Copy</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="userTableBody">
                            <!-- Display Reserved Books -->
                            <?php foreach ($reservedBooks as $book): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?php echo htmlspecialchars($book['user_id']); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?php 
                                        // Display last name, first name, and middle name
                                        echo htmlspecialchars($book['last_name'] . ', ' . $book['first_name'] . ' ' . $book['middle_name']); 
                                        ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?php echo htmlspecialchars($book['book_title']); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?php echo htmlspecialchars($book['patron_type']); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            <?php echo htmlspecialchars($book['status']); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-center">
                                        <?php echo htmlspecialchars($book['copies']); ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- Circulate Item Modal -->
    <div id="circulateModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-1/2 shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center pb-3">
                <h3 class="text-xl font-semibold text-gray-900" id="circulateModalLabel">Circulate Item</h3>
                <button onclick="closeCirculateModal()" class="text-gray-400 hover:text-gray-500">
                    <i class="lni lni-close"></i>
                </button>
            </div>
            <div class="mt-2">
                <form id="circulateForm" class="space-y-4">
                    <div>
                        <label for="user_id" class="block text-sm font-medium text-gray-700">User ID</label>
                        <input type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500" id="user_id" name="user_id" required>
                    </div>
                    <div>
                        <label for="book_input" class="block text-sm font-medium text-gray-700">Book Title</label>
                        <input type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500" id="book_input" name="book_input" required>
                    </div>
                    <input type="hidden" name="action_type" value="borrow">
                    <input type="hidden" name="quick_checkout" value="1">
                    <div class="flex justify-end space-x-3 mt-4">
                        <button type="button" onclick="closeCirculateModal()" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">Cancel</button>
                        <button type="submit" class="bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        // Function to search users
        function searchUsers() {
            const input = document.getElementById('searchInput');
            const filter = input.value.toLowerCase();
            const tbody = document.getElementById('userTableBody');
            const rows = tbody.getElementsByTagName('tr');
            for (let i = 0; i < rows.length; i++) {
                const userIdCell = rows[i].getElementsByTagName('td')[0];
                const userNameCell = rows[i].getElementsByTagName('td')[1];
                if (userIdCell && userNameCell) {
                    const userId = userIdCell.textContent || userIdCell.innerText;
                    const userName = userNameCell.textContent || userNameCell.innerText;
                    if (userId.toLowerCase().indexOf(filter) > -1 || userName.toLowerCase().indexOf(filter) > -1) {
                        rows[i].style.display = '';
                    } else {
                        rows[i].style.display = 'none';
                    }
                }
            }
        }
    </script>
</body>
</html>