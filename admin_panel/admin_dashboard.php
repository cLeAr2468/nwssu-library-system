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

            <!-- Announcements Section -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 mb-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Recent Announcements</h3>
                    <button id="addAnnouncementBtn" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg transition duration-200 flex items-center">
                        <i class="lni lni-plus mr-2"></i> Add Announcement
                    </button>
                </div>
                <div class="space-y-4">
                    <?php if (!empty($announcements)): ?>
                        <?php foreach ($announcements as $announcement): ?>
                            <div class="border border-gray-200 rounded-lg p-4 relative">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h4 class="font-semibold text-gray-800"><?= htmlspecialchars($announcement['title']) ?></h4>
                                        <p class="text-sm text-gray-500 mt-1">
                                            <?php
                                            $post_time = new DateTime($announcement['date']);
                                            $now = new DateTime();
                                            $interval = $post_time->diff($now);
                                            if ($interval->y > 0) {
                                                echo $interval->y . ' year' . ($interval->y > 1 ? 's' : '') . ' ago';
                                            } elseif ($interval->m > 0) {
                                                echo $interval->m . ' month' . ($interval->m > 1 ? 's' : '') . ' ago';
                                            } elseif ($interval->d > 0) {
                                                echo $interval->d . ' day' . ($interval->d > 1 ? 's' : '') . ' ago';
                                            } elseif ($interval->h > 0) {
                                                echo $interval->h . ' hour' . ($interval->h > 1 ? 's' : '') . ' ago';
                                            } elseif ($interval->i > 0) {
                                                echo $interval->i . ' minute' . ($interval->i > 1 ? 's' : '') . ' ago';
                                            } else {
                                                echo 'Just now';
                                            }
                                            ?>
                                        </p>
                                    </div>
                                    <div class="flex space-x-2">
                                        <button onclick="editAnnouncement(<?= $announcement['id'] ?>, '<?= htmlspecialchars($announcement['title']) ?>', '<?= htmlspecialchars($announcement['message']) ?>')" class="text-blue-600 hover:text-blue-800">
                                            <i class="lni lni-pencil"></i>
                                        </button>
                                        <button onclick="deleteAnnouncement(<?= $announcement['id'] ?>)" class="text-red-600 hover:text-red-800">
                                            <i class="lni lni-trash-can"></i>
                                        </button>
                                    </div>
                                </div>
                                <p class="mt-2 text-gray-600"><?= htmlspecialchars($announcement['message']) ?></p>
                                <?php if (!empty($announcement['image'])): ?>
                                    <div class="mt-3">
                                        <img src="<?= htmlspecialchars($announcement['image']) ?>" alt="Announcement Image" class="max-w-xs rounded-lg">
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-gray-500 text-center py-4">No announcements available.</p>
                    <?php endif; ?>
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
                    <h3 class="text-xl font-semibold text-gray-800" id="modalTitle">Add Announcement</h3>
                    <button id="closeModal" class="text-gray-500 hover:text-gray-700">
                        <i class="lni lni-close text-xl"></i>
                    </button>
                </div>
                <div class="p-4">
                    <form id="announcementForm" action="process_announcement.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" id="announcementId" name="id">
                        <div class="mb-4">
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                            <input type="text" id="title" name="title" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500" required>
                        </div>
                        <div class="mb-4">
                            <label for="content" class="block text-sm font-medium text-gray-700 mb-1">Content</label>
                            <textarea id="content" name="content" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500" required></textarea>
                        </div>
                        <div class="mb-4">
                            <label for="image" class="block text-sm font-medium text-gray-700 mb-1">Image (Optional)</label>
                            <input type="file" id="image" name="image" accept="image/*" class="w-full">
                        </div>
                        <div class="flex justify-end space-x-2">
                            <button type="button" id="deleteBtn" class="hidden bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition duration-200">
                                Delete
                            </button>
                            <button type="submit" class="bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700 transition duration-200">
                                Save Announcement
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Modal functionality
        const modal = document.getElementById('announcementModal');
        const addBtn = document.getElementById('addAnnouncementBtn');
        const closeBtn = document.getElementById('closeModal');
        const form = document.getElementById('announcementForm');
        const deleteBtn = document.getElementById('deleteBtn');
        const modalTitle = document.getElementById('modalTitle');

        function editAnnouncement(id, title, content) {
            document.getElementById('announcementId').value = id;
            document.getElementById('title').value = title;
            document.getElementById('content').value = content;
            modalTitle.textContent = 'Edit Announcement';
            deleteBtn.classList.remove('hidden');
            modal.classList.remove('hidden');
        }

        function deleteAnnouncement(id) {
            if (confirm('Are you sure you want to delete this announcement?')) {
                const formData = new FormData();
                formData.append('action', 'delete');
                formData.append('id', id);

                fetch('process_announcement.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification(data.message, 'success');
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        showNotification(data.message, 'error');
                    }
                })
                .catch(error => {
                    showNotification('An error occurred while deleting the announcement', 'error');
                    console.error('Error:', error);
                });
            }
        }

        function openAnnouncementModal() {
            form.reset();
            document.getElementById('announcementId').value = '';
            modalTitle.textContent = 'Add Announcement';
            deleteBtn.classList.add('hidden');
            modal.classList.remove('hidden');
        }

        function closeAnnouncementModal() {
            modal.classList.add('hidden');
            form.reset();
        }

        function showNotification(message, type = 'success') {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg ${
                type === 'success' ? 'bg-green-500' : 'bg-red-500'
            } text-white z-50`;
            notification.textContent = message;
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.remove();
            }, 3000);
        }

        addBtn.addEventListener('click', openAnnouncementModal);

        closeBtn.addEventListener('click', closeAnnouncementModal);

        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                closeAnnouncementModal();
            }
        });

        deleteBtn.addEventListener('click', () => {
            const id = document.getElementById('announcementId').value;
            deleteAnnouncement(id);
        });

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const id = document.getElementById('announcementId').value;
            formData.append('action', id ? 'update' : 'add');
            
            fetch('process_announcement.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification(data.message, 'success');
                    closeAnnouncementModal();
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showNotification(data.message, 'error');
                }
            })
            .catch(error => {
                showNotification('An error occurred while saving the announcement', 'error');
                console.error('Error:', error);
            });
        });
    </script>
</body>
</html>