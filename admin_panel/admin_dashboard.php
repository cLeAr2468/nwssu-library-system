<?php 
include '../admin_panel/dash-function.php';
include '../admin_panel/side_nav.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
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
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');
        
        body {
            font-family: 'Poppins', sans-serif;
        }
        
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        
        ::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 3px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="p-4 lg:ml-64 mt-14">
        <div class="p-4 rounded-lg">
            <!-- Header Section -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Dashboard Overview</h1>
                    <p class="text-gray-600">Welcome back, Administrator</p>
                </div>
                <div class="mt-4 md:mt-0">
                    <button id="addAnnouncementBtn" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg transition duration-200 flex items-center">
                        <i class="lni lni-plus mr-2"></i> Add Announcement
                    </button>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                <!-- Users Card -->
                <a href="../admin_panel/Student_list.php" class="block">
                    <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition duration-300 p-6 border border-gray-100">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Total Users</p>
                                <p class="text-2xl font-bold text-gray-800"><?= $approvedCount ?></p>
                            </div>
                            <div class="bg-primary-100 p-3 rounded-full">
                                <i class="lni lni-users text-primary-600 text-xl"></i>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="flex items-center text-sm">
                                <span class="text-green-500 flex items-center">
                                    <i class="lni lni-arrow-up mr-1"></i> 12%
                                </span>
                                <span class="text-gray-500 ml-2">from last month</span>
                            </div>
                        </div>
                    </div>
                </a>

                <!-- Pending Accounts Card -->
                <a href="../admin_panel/confirm.php?status=pending" class="block">
                    <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition duration-300 p-6 border border-gray-100">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Pending Accounts</p>
                                <p class="text-2xl font-bold text-gray-800"><?= $pendingCount ?></p>
                            </div>
                            <div class="bg-yellow-100 p-3 rounded-full">
                                <i class="lni lni-user text-yellow-600 text-xl"></i>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="flex items-center text-sm">
                                <span class="text-yellow-500 flex items-center">
                                    <i class="lni lni-warning mr-1"></i> Needs Review
                                </span>
                            </div>
                        </div>
                    </div>
                </a>

                <!-- Books Card -->
                <a href="../admin_panel/display_books.php" class="block">
                    <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition duration-300 p-6 border border-gray-100">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Total Books</p>
                                <p class="text-2xl font-bold text-gray-800"><?= $totalBooksCount ?></p>
                                <p class="text-sm text-gray-500"><?= $totalCopiesCount ?> copies</p>
                            </div>
                            <div class="bg-blue-100 p-3 rounded-full">
                                <i class="lni lni-book text-blue-600 text-xl"></i>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="flex items-center text-sm">
                                <span class="text-green-500 flex items-center">
                                    <i class="lni lni-arrow-up mr-1"></i> 8%
                                </span>
                                <span class="text-gray-500 ml-2">from last month</span>
                            </div>
                        </div>
                    </div>
                </a>

                <!-- Reserved Books Card -->
                <a href="../admin_panel/reserved.php" class="block">
                    <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition duration-300 p-6 border border-gray-100">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Reserved Books</p>
                                <p class="text-2xl font-bold text-gray-800"><?= $totalReservedBooksCount ?></p>
                            </div>
                            <div class="bg-purple-100 p-3 rounded-full">
                                <i class="lni lni-bookmark text-purple-600 text-xl"></i>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="flex items-center text-sm">
                                <span class="text-purple-500 flex items-center">
                                    <i class="lni lni-bookmark mr-1"></i> Active Reservations
                                </span>
                            </div>
                        </div>
                    </div>
                </a>

                <!-- Borrowed Books Card -->
                <a href="../admin_panel/return.php" class="block">
                    <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition duration-300 p-6 border border-gray-100">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Borrowed Books</p>
                                <p class="text-2xl font-bold text-gray-800"><?= $totalBorrowedBooksCount ?></p>
                            </div>
                            <div class="bg-green-100 p-3 rounded-full">
                                <i class="lni lni-library text-green-600 text-xl"></i>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="flex items-center text-sm">
                                <span class="text-red-500 flex items-center">
                                    <i class="lni lni-arrow-down mr-1"></i> 3%
                                </span>
                                <span class="text-gray-500 ml-2">from last month</span>
                            </div>
                        </div>
                    </div>
                </a>

                <!-- Fines Card -->
                <a href="../admin_panel/fine_rec.php" class="block">
                    <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition duration-300 p-6 border border-gray-100">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Pending Fines</p>
                                <p class="text-2xl font-bold text-gray-800"><?= $totalUsersWithFinesCount ?></p>
                            </div>
                            <div class="bg-red-100 p-3 rounded-full">
                                <i class="lni lni-coin text-red-600 text-xl"></i>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="flex items-center text-sm">
                                <span class="text-red-500 flex items-center">
                                    <i class="lni lni-arrow-up mr-1"></i> 5%
                                </span>
                                <span class="text-gray-500 ml-2">from last month</span>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Charts Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- Borrowing Trends Chart -->
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Borrowing Trends</h3>
                    <canvas id="borrowingTrendsChart" height="300"></canvas>
                </div>

                <!-- Book Categories Distribution -->
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Book Categories Distribution</h3>
                    <canvas id="categoriesChart" height="300"></canvas>
                </div>
            </div>

            <!-- Recent Activity Section -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Recent Activity</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Activity</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <!-- Sample recent activities - Replace with dynamic data -->
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Book Borrowed</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">John Doe</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">2024-03-15</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Completed</span>
                                </td>
                            </tr>
                            <!-- Add more rows as needed -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Announcement Modal -->
    <div id="announcementModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
                <div class="flex justify-between items-center border-b p-4">
                    <h3 class="text-xl font-semibold text-gray-800">Add Announcement</h3>
                    <button id="closeModal" class="text-gray-500 hover:text-gray-700">
                        <i class="lni lni-close text-xl"></i>
                    </button>
                </div>
                <div class="p-4">
                    <form id="announcementForm">
                        <div class="mb-4">
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                            <input type="text" id="title" name="title" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500">
                        </div>
                        <div class="mb-4">
                            <label for="content" class="block text-sm font-medium text-gray-700 mb-1">Content</label>
                            <textarea id="content" name="content" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500"></textarea>
                        </div>
                        <div class="flex justify-end">
                            <button type="submit" class="bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700 transition duration-200">
                                Post Announcement
                            </button>
                        </div>
                    </form>
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
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                    datasets: [{
                        label: 'Books Borrowed',
                        data: [65, 59, 80, 81, 56, 55],
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
                            beginAtZero: true
                        }
                    }
                }
            });

            // Categories Distribution Chart
            const categoriesCtx = document.getElementById('categoriesChart').getContext('2d');
            new Chart(categoriesCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Fiction', 'Non-Fiction', 'Science', 'History', 'Technology'],
                    datasets: [{
                        data: [30, 25, 15, 20, 10],
                        backgroundColor: [
                            '#0ea5e9',
                            '#3b82f6',
                            '#60a5fa',
                            '#93c5fd',
                            '#bfdbfe'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        });

        // Modal functionality
        const modal = document.getElementById('announcementModal');
        const addBtn = document.getElementById('addAnnouncementBtn');
        const closeBtn = document.getElementById('closeModal');

        addBtn.addEventListener('click', () => {
            modal.classList.remove('hidden');
        });

        closeBtn.addEventListener('click', () => {
            modal.classList.add('hidden');
        });

        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.classList.add('hidden');
            }
        });
    </script>
</body>
</html>