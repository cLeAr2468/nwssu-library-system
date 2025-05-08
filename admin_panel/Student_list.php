<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ../admin_panel/index.php'); // Adjust the path to your login page
    exit();
}
include '../component-library/connect.php';
// Database connection
try {
    $conn = new PDO("mysql:host=$db_host;dbname=$db_name", $user_name, $user_password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}
// Fetch user data from the database
$query = $conn->query("SELECT * FROM user_info WHERE status = 'approved'");
$userinfo = $query->fetchAll(PDO::FETCH_ASSOC);

function checkExistingAccount($userId, $email) {
    global $conn;
    try {
        // Check if user_id or email already exists
        $stmt = $conn->prepare("SELECT * FROM user_info WHERE user_id = ? OR email = ?");
        $stmt->execute([$userId, $email]);
        $existingUser = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($existingUser) {
            if ($existingUser['user_id'] === $userId) {
                return "User ID already exists! Please use a different ID.";
            }
            if ($existingUser['email'] === $email) {
                return "Email already exists! Please use a different email.";
            }
        }
        return false; // No existing account found
    } catch (PDOException $e) {
        error_log("Error checking existing account: " . $e->getMessage());
        return "Error checking existing account.";
    }
}

function insertOrUpdateUser($data, $Id = null) {
    global $conn;
    try {
        // If inserting a new user, check for existing account
        if (!$Id) {
            $existingAccount = checkExistingAccount($data['user_id_input'], $data['email']);
            if ($existingAccount) {
                echo json_encode(['success' => false, 'message' => $existingAccount]);
                return;
            }
            // Set default values for new users
            $data['status'] = 'approved'; // Default status
            $data['account_status'] = 'active'; // Default account status
        }

        $imagePath = null; // Default to null
        // Check if the image is uploaded and no error occurred
        if (isset($_FILES['images']) && $_FILES['images']['error'] === UPLOAD_ERR_OK) {
            $originalFileName = basename($_FILES['images']['name']);
            // Create a unique filename to avoid overwriting
            $uniqueFileName = time() . '_' . $originalFileName;
            $imagePath = '../uploaded_file/' . $uniqueFileName;
            // Ensure the upload directory exists
            if (!file_exists(dirname($imagePath))) {
                mkdir(dirname($imagePath), 0777, true);
            }
            // Move the uploaded file to the target directory
            if (!move_uploaded_file($_FILES['images']['tmp_name'], $imagePath)) {
                throw new Exception('Failed to upload the image.');
            }
        }

        if ($Id) {
            // Update existing user
            $dataToBind = [];
            $query = "UPDATE user_info SET first_name = ?, middle_name = ?, last_name = ?, patron_type = ?, email = ?, address = ?, status = ?, account_status = ? ";
            $dataToBind[] = $data['first_name'];
            $dataToBind[] = $data['middle_name'];
            $dataToBind[] = $data['last_name'];
            $dataToBind[] = $data['patron_type'];
            $dataToBind[] = $data['email'];
            $dataToBind[] = $data['address'];
            $dataToBind[] = $data['status'];
            $dataToBind[] = $data['account_status'];
            
            // Update password if a new one is provided
            if (!empty($data['password'])) {
                $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
                $query .= ", password = ?";
                $dataToBind[] = $hashedPassword;
            }
            // Check if new image is provided
            if ($imagePath) {
                $query .= ", images = ?";
                $dataToBind[] = $imagePath;
            }
            $query .= " WHERE user_id = ?";
            $dataToBind[] = $Id;
            $stmt = $conn->prepare($query);
            $stmt->execute($dataToBind);
            echo json_encode(['success' => true, 'message' => "User updated successfully!"]);
        } else {
            // Insert new user
            $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO user_info (
                user_id, first_name, middle_name, last_name, patron_type, email, address, password, images, status, account_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $data['images'] = $imagePath;
            $dataToBind = [
                $data['user_id_input'],
                $data['first_name'],
                $data['middle_name'],
                $data['last_name'],
                $data['patron_type'],
                $data['email'],
                $data['address'],
                $hashedPassword,
                $data['images'],
                $data['status'],
                $data['account_status']
            ];
            $stmt->execute($dataToBind);
            echo json_encode(['success' => true, 'message' => "User added successfully!"]);
        }
    } catch (Exception $e) {
        error_log("Error inserting/updating user: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => "Failed to " . ($Id ? "update" : "add") . " user: " . $e->getMessage()]);
    }
}
// Insert or update user if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check if passwords match
    if ($_POST['password'] !== $_POST['confirm_password']) {
        echo json_encode(['success' => false, 'message' => "Passwords do not match!"]);
        exit();
    }
    $Id = isset($_POST['user_id']) ? $_POST['user_id'] : null; // Check if user ID is present
    insertOrUpdateUser($_POST, $Id);
    exit(); // Exit to prevent further processing
}
include '../admin_panel/side_nav.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NwSSU : User Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.lineicons.com/4.0/lineicons.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
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
    <div class="p-4 lg:ml-64 mt-14">
        <div class="p-4 rounded-lg">
            <!-- Header Section -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">User Management</h1>
                    <p class="text-gray-600">Manage library users and their accounts</p>
                </div>
                <div class="mt-4 md:mt-0">
                    <div class="flex space-x-2">
                        <button class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg transition duration-200 flex items-center" onclick="openAddUserModal('student')">
                            <i class="lni lni-plus mr-2"></i> Add Student
                        </button>
                        <button class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition duration-200 flex items-center" onclick="openAddUserModal('faculty')">
                            <i class="lni lni-plus mr-2"></i> Add Faculty
                        </button>
                    </div>
                </div>
            </div>
            <!-- Search and Filter Section -->
            <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100 mb-6">
                <div class="flex flex-col md:flex-row items-start md:items-center space-y-4 md:space-y-0 md:space-x-4">
                    <div class="flex-1 w-full">
                        <div class="relative">
                            <i class="lni lni-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            <input type="text" id="searchInput" placeholder="Search users..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 text-left" style="text-align: left;">
                            <div id="searchResults" class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg hidden">
                                <!-- Search results will be displayed here -->
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <select id="statusFilter" class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                            <option value="all">All Status</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                        <select id="patronTypeFilter" class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                            <option value="all">All Types</option>
                            <optgroup label="Students">
                                <option value="student-BSA">student-BSA</option>
                                <option value="student-BSCRIM">student-BSCRIM</option>
                                <option value="student-BAT">student-BAT</option>
                                <option value="student-BSIT">student-BSIT</option>
                                <option value="student-BTLED">student-BTLED</option>
                                <option value="student-BEED">student-BEED</option>
                                <option value="student-BSF">student-BSF</option>
                                <option value="student-BSABE">student-BSABE</option>
                            </optgroup>
                            <optgroup label="Faculty">
                                <option value="faculty-Teaching">faculty-Teaching</option>
                                <option value="faculty-Non-Teaching">faculty-Non-Teaching</option>
                                <option value="faculty-Support Service">faculty-Support Service</option>
                                <option value="faculty-Contract of Service">faculty-Contract of Service</option>
                                <option value="faculty-Part-timer">faculty-Part-timer</option>
                                <option value="faculty-Guard">faculty-Guard</option>
                                <option value="faculty-Casual">faculty-Casual</option>
                            </optgroup>
                        </select>
                    </div>
                </div>
            </div>
            <!-- Users Table -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-primary-600">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">User Info</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Contact</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($userinfo as $user): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?>
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                ID: <?php echo htmlspecialchars($user['user_id']); ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900"><?php echo htmlspecialchars($user['email']); ?></div>
                                    <div class="text-sm text-gray-500"><?php echo htmlspecialchars($user['address']); ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                        <?php echo htmlspecialchars($user['patron_type']); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $user['account_status'] === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                        <?php echo htmlspecialchars($user['account_status']); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center space-x-3">
                                        <button class="text-primary-600 hover:text-primary-900" onclick="editUser(<?php echo htmlspecialchars(json_encode($user)); ?>)">
                                            <i class="lni lni-pencil"></i>
                                        </button>
                                        <button class="text-blue-600 hover:text-blue-900" onclick="viewUserRecord('<?php echo $user['user_id']; ?>')">
                                            <i class="lni lni-eye"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- Add/Edit User Modal -->
    <div id="addUserModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-3xl shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center pb-3 border-b">
                <h3 class="text-xl font-semibold text-gray-900" id="addUserModalLabel">Add New User</h3>
                <button type="button" class="text-gray-400 hover:text-gray-500" onclick="closeModal()">
                    <i class="lni lni-close text-xl"></i>
                </button>
            </div>
            <div class="mt-4">
                <form id="userForm" method="POST" enctype="multipart/form-data" class="space-y-4">
                    <input type="hidden" id="user_id" name="user_id" value="">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">User ID <span class="text-red-500">*</span></label>
                            <input type="text" name="user_id_input" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">First Name <span class="text-red-500">*</span></label>
                            <input type="text" name="first_name" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Middle Name <span class="text-red-500">*</span></label>
                            <input type="text" name="middle_name" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Last Name <span class="text-red-500">*</span></label>
                            <input type="text" name="last_name" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Email <span class="text-red-500">*</span></label>
                            <input type="email" name="email" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Patron Type <span class="text-red-500">*</span></label>
                            <select name="patron_type" id="patronTypeSelect" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                                <option value="" disabled selected>Choose Patron type</option>
                            </select>
                        </div>
                        <div id="statusDiv" class="hidden">
                            <label class="block text-sm font-medium text-gray-700">Status <span class="text-red-500">*</span></label>
                            <select name="status" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                                <option value="approved">Approved</option>
                                <option value="pending">Pending</option>
                            </select>
                        </div>
                        <div id="accountStatusDiv" class="hidden">
                            <label class="block text-sm font-medium text-gray-700">Account Status <span class="text-red-500">*</span></label>
                            <select name="account_status" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Address <span class="text-red-500">*</span></label>
                            <textarea name="address" rows="2" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700" id="passwordLabel">Create Password <span class="text-red-500">*</span></label>
                            <input type="password" name="password" id="passwordInput" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                            <p class="text-xs text-gray-500 mt-1" id="passwordHint">Password is required for new users</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700" id="confirmPasswordLabel">Confirm Password <span class="text-red-500">*</span></label>
                            <input type="password" name="confirm_password" id="confirmPasswordInput" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                        </div>
                    </div>
                    <div class="flex justify-end space-x-3 pt-4 border-t">
                        <button type="button" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600" onclick="closeModal()">Cancel</button>
                        <button type="submit" id="submitButton" class="bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700">Save User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        let originalValues = {};
        let currentUserType = 'student';

        // Add filter functionality
        function applyFilters() {
            const statusFilter = document.getElementById('statusFilter').value;
            const patronTypeFilter = document.getElementById('patronTypeFilter').value;
            const searchInput = document.getElementById('searchInput').value.toLowerCase();
            
            const rows = document.querySelectorAll('tbody tr');
            
            rows.forEach(row => {
                const status = row.querySelector('td:nth-child(4) span').textContent.trim();
                const patronType = row.querySelector('td:nth-child(3) span').textContent.trim();
                const userInfo = row.querySelector('td:nth-child(1)').textContent.toLowerCase();
                const contactInfo = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
                
                let showRow = true;
                
                // Apply status filter
                if (statusFilter !== 'all' && status !== statusFilter) {
                    showRow = false;
                }
                
                // Apply patron type filter
                if (patronTypeFilter !== 'all' && patronType !== patronTypeFilter) {
                    showRow = false;
                }
                
                // Apply search filter
                if (searchInput && !userInfo.includes(searchInput) && !contactInfo.includes(searchInput)) {
                    showRow = false;
                }
                
                row.style.display = showRow ? '' : 'none';
            });
        }

        // Add event listeners for filters
        document.addEventListener('DOMContentLoaded', function() {
            const statusFilter = document.getElementById('statusFilter');
            const patronTypeFilter = document.getElementById('patronTypeFilter');
            const searchInput = document.getElementById('searchInput');
            
            statusFilter.addEventListener('change', applyFilters);
            patronTypeFilter.addEventListener('change', applyFilters);
            searchInput.addEventListener('input', applyFilters);
            
            // Initialize filters
            applyFilters();
        });

        function checkFormChanges() {
            const form = document.getElementById('userForm');
            const submitButton = document.getElementById('submitButton');
            const userId = document.getElementById('user_id').value;

            // Only check for changes if we're in edit mode (userId exists)
            if (userId) {
                let hasChanges = false;
                
                // Check each form field against original values
                for (const key in originalValues) {
                    const currentValue = form[key] ? form[key].value : '';
                    if (currentValue !== originalValues[key]) {
                        hasChanges = true;
                        break;
                    }
                }

                // Enable/disable submit button based on changes
                submitButton.disabled = !hasChanges;
                submitButton.classList.toggle('opacity-50', !hasChanges);
                submitButton.classList.toggle('cursor-not-allowed', !hasChanges);
            }
        }

        function openAddUserModal(userType) {
            currentUserType = userType;
            document.getElementById('addUserModal').classList.remove('hidden');
            document.getElementById('addUserModalLabel').textContent = userType === 'student' ? 'Add New Student' : 'Add New Faculty';
            document.getElementById('userForm').reset();
            document.getElementById('user_id').value = '';
            document.getElementById('passwordLabel').textContent = 'Create Password *';
            document.getElementById('confirmPasswordLabel').textContent = 'Confirm Password *';
            document.getElementById('passwordInput').required = true;
            document.getElementById('confirmPasswordInput').required = true;
            document.getElementById('passwordHint').textContent = 'Password is required for new users';
            document.getElementById('submitButton').textContent = 'Save User';
            
            // Hide the Status and Account Status dropdowns
            document.getElementById('statusDiv').classList.add('hidden');
            document.getElementById('accountStatusDiv').classList.add('hidden');
            
            // Update patron type options based on user type
            const patronTypeSelect = document.getElementById('patronTypeSelect');
            patronTypeSelect.innerHTML = '<option value="" disabled selected>Choose Patron type</option>';
            
            if (userType === 'student') {
                const studentOptions = [
                    'student-BSA', 'student-BSCRIM', 'student-BAT', 'student-BSIT',
                    'student-BTLED', 'student-BEED', 'student-BSF', 'student-BSABE'
                ];
                studentOptions.forEach(option => {
                    const opt = document.createElement('option');
                    opt.value = option;
                    opt.textContent = option.replace('student-', '');
                    patronTypeSelect.appendChild(opt);
                });
            } else {
                const facultyOptions = [
                    'faculty-Teaching', 'faculty-Non-Teaching', 'faculty-Support Service',
                    'faculty-Contract of Service', 'faculty-Part-timer', 'faculty-Guard',
                    'faculty-Casual'
                ];
                facultyOptions.forEach(option => {
                    const opt = document.createElement('option');
                    opt.value = option;
                    opt.textContent = option.replace('faculty-', '');
                    patronTypeSelect.appendChild(opt);
                });
            }
            
            // Clear original values when adding new user
            originalValues = {};
            document.getElementById('submitButton').disabled = false;
            document.getElementById('submitButton').classList.remove('opacity-50', 'cursor-not-allowed');
        }

        function closeModal() {
            document.getElementById('addUserModal').classList.add('hidden');
        }

        function editUser(user) {
            document.getElementById('addUserModal').classList.remove('hidden');
            document.getElementById('addUserModalLabel').textContent = 'Update User';
            document.getElementById('user_id').value = user.user_id;
            
            // Show the Status and Account Status dropdowns
            document.getElementById('statusDiv').classList.remove('hidden');
            document.getElementById('accountStatusDiv').classList.remove('hidden');
            
            // Determine user type based on patron_type
            currentUserType = user.patron_type.startsWith('student-') ? 'student' : 'faculty';
            
            // Update patron type options based on user type
            const patronTypeSelect = document.getElementById('patronTypeSelect');
            patronTypeSelect.innerHTML = '<option value="" disabled>Choose Patron type</option>';
            
            if (currentUserType === 'student') {
                const studentOptions = [
                    'student-BSA', 'student-BSCRIM', 'student-BAT', 'student-BSIT',
                    'student-BTLED', 'student-BEED', 'student-BSF', 'student-BSABE'
                ];
                studentOptions.forEach(option => {
                    const opt = document.createElement('option');
                    opt.value = option;
                    opt.textContent = option.replace('student-', '');
                    if (option === user.patron_type) {
                        opt.selected = true;
                    }
                    patronTypeSelect.appendChild(opt);
                });
            } else {
                const facultyOptions = [
                    'faculty-Teaching', 'faculty-Non-Teaching', 'faculty-Support Service',
                    'faculty-Contract of Service', 'faculty-Part-timer', 'faculty-Guard',
                    'faculty-Casual'
                ];
                facultyOptions.forEach(option => {
                    const opt = document.createElement('option');
                    opt.value = option;
                    opt.textContent = option.replace('faculty-', '');
                    if (option === user.patron_type) {
                        opt.selected = true;
                    }
                    patronTypeSelect.appendChild(opt);
                });
            }
            
            // Populate other form fields
            const form = document.getElementById('userForm');
            form.first_name.value = user.first_name;
            form.middle_name.value = user.middle_name || '';
            form.last_name.value = user.last_name;
            form.email.value = user.email;
            form.user_id_input.value = user.user_id;
            form.status.value = user.status;
            form.address.value = user.address;
            form.account_status.value = user.account_status;
            
            // Set up for updating a user
            document.getElementById('passwordLabel').textContent = 'New Password';
            document.getElementById('confirmPasswordLabel').textContent = 'Confirm New Password';
            document.getElementById('passwordInput').required = false;
            document.getElementById('confirmPasswordInput').required = false;
            document.getElementById('passwordHint').textContent = 'Leave blank to keep current password';
            document.getElementById('submitButton').textContent = 'Update User';

            // Store original values
            originalValues = {
                first_name: user.first_name,
                middle_name: user.middle_name || '',
                last_name: user.last_name,
                email: user.email,
                user_id_input: user.user_id,
                patron_type: user.patron_type,
                status: user.status,
                address: user.address,
                account_status: user.account_status
            };

            // Initially disable update button
            document.getElementById('submitButton').disabled = true;
            document.getElementById('submitButton').classList.add('opacity-50', 'cursor-not-allowed');

            // Add change event listeners to all form fields
            const formElements = form.querySelectorAll('input, select, textarea');
            formElements.forEach(element => {
                element.addEventListener('input', checkFormChanges);
                element.addEventListener('change', checkFormChanges);
            });
        }

        function viewUserRecord(userId) {
            window.location.href = `record.php?user_id=${userId}`;
        }

        document.addEventListener('DOMContentLoaded', function() {
            const userForm = document.getElementById('userForm');
            
            // Add form submit handler
            userForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const userId = document.getElementById('user_id').value;
                const password = userForm.password.value;
                const confirmPassword = userForm.confirm_password.value;
                const userType = currentUserType; // Get the current user type (student/faculty)

                // Validate required fields
                if (!userForm.user_id_input.value || !userForm.first_name.value || 
                    !userForm.last_name.value || !userForm.email.value || 
                    !userForm.patron_type.value || !userForm.address.value) {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Please fill in all required fields!',
                        icon: 'error'
                    });
                    return;
                }

                // Validate email format
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(userForm.email.value)) {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Please enter a valid email address!',
                        icon: 'error'
                    });
                    return;
                }

                // For new users, password is required
                if (!userId && (!password || !confirmPassword)) {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Password is required for new users!',
                        icon: 'error'
                    });
                    return;
                }

                // Check if passwords match (only if password is provided)
                if (password && password !== confirmPassword) {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Passwords do not match!',
                        icon: 'error'
                    });
                    return;
                }

                // Validate patron type based on user type
                const patronType = userForm.patron_type.value;
                if (userType === 'student' && !patronType.startsWith('student-')) {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Invalid patron type for student!',
                        icon: 'error'
                    });
                    return;
                }
                if (userType === 'faculty' && !patronType.startsWith('faculty-')) {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Invalid patron type for faculty!',
                        icon: 'error'
                    });
                    return;
                }

                // If we're in edit mode, check if there are changes
                if (userId && document.getElementById('submitButton').disabled) {
                    Swal.fire({
                        title: 'No Changes',
                        text: 'No changes were made to update.',
                        icon: 'info'
                    });
                    return;
                }

                // Show loading state
                Swal.fire({
                    title: 'Processing...',
                    text: 'Please wait while we process your request.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                const formData = new FormData(userForm);
                fetch(window.location.href, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: 'Success!',
                            text: data.message,
                            icon: 'success'
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: data.message,
                            icon: 'error'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        title: 'Error!',
                        text: 'An error occurred while processing your request.',
                        icon: 'error'
                    });
                });
            });

            // Add input validation for user ID
            const userIdInput = document.querySelector('input[name="user_id_input"]');
            userIdInput.addEventListener('input', function(e) {
                // Remove any non-alphanumeric characters
                this.value = this.value.replace(/[^a-zA-Z0-9]/g, '');
            });

            // Add input validation for email
            const emailInput = document.querySelector('input[name="email"]');
            emailInput.addEventListener('input', function(e) {
                // Convert to lowercase
                this.value = this.value.toLowerCase();
            });
        });
    </script>
</body>
</html>