<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ../admin_panel/index.php');
    exit();
}

include "../component-library/connect.php";

function sendEmail($to, $subject, $message) {
    $headers = "From: Online Library Administrator <reyesjerald638@gmail.com>\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    mail($to, $subject, $message, $headers);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["user_id"]) && isset($_POST["action"])) {
    $user_id = $_POST["user_id"];
    $action = $_POST["action"];
    try {
        if ($action == 'approve') {
            $updateQuery = "UPDATE user_info SET status = 'approved' WHERE user_id = :user_id";
            $updates = $conn->prepare($updateQuery);
            $updates->bindParam(':user_id', $user_id);
            $updates->execute();

            $emailQuery = "SELECT email FROM user_info WHERE user_id = :user_id";
            $emails = $conn->prepare($emailQuery);
            $emails->bindParam(':user_id', $user_id);
            $emails->execute();
            $emailResult = $emails->fetch(PDO::FETCH_ASSOC);
            $user_email = $emailResult['email'];

            // Send approval email
            $subject = "Account Approved";
            $message = "Your account has been approved! Please login now: <a href='http://localhost/library-system/index.php'>Login Here</a>";
            sendEmail($user_email, $subject, $message);
            echo "success";
        } elseif ($action == 'remove') {
            $emailQuery = "SELECT email FROM user_info WHERE user_id = :user_id";
            $emails = $conn->prepare($emailQuery);
            $emails->bindParam(':user_id', $user_id);
            $emails->execute();
            $emailResult = $emails->fetch(PDO::FETCH_ASSOC);
            $user_email = $emailResult['email'];

            // Delete user
            $deleteQuery = "DELETE FROM user_info WHERE user_id = :user_id";
            $deleted = $conn->prepare($deleteQuery);
            $deleted->bindParam(':user_id', $user_id);
            $deleted->execute();

            // Send decline email
            $subject = "Account Declined";
            $message = "Your account has been declined. Please register again with valid information: <a href='http://localhost/library-system/student/student_login.php'>Register Here</a>";
            sendEmail($user_email, $subject, $message);
            echo "success";
        }
        exit();
    } catch (PDOException $e) {
        error_log($e->getMessage());
        echo "error";
        exit();
    }
}

try {
    $search = "SELECT * FROM user_info WHERE status != 'approved'";
    $stat = $conn->prepare($search);
    $stat->execute();
    $result = $stat->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Query failed: " . $e->getMessage());
}

include '../admin_panel/side_nav.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Confirmation Account</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.lineicons.com/4.0/lineicons.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
                    <h1 class="text-2xl font-bold text-gray-800">Pending Account Approvals</h1>
                    <p class="text-gray-600">Review and manage pending user accounts</p>
                </div>
                <div class="mt-4 md:mt-0">
                    <div class="relative">
                        <input type="text" 
                               id="searchInput" 
                               placeholder="Search by ID or Name" 
                               class="w-full md:w-64 px-4 py-2 pl-10 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                               onkeyup="searchUsers()">
                        <i class="lni lni-search absolute left-3 top-3 text-gray-400"></i>
                    </div>
                </div>
            </div>

            <!-- Users Table -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-gray-200">
                        <thead class="bg-[#0284c7]">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Student ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Patron Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Address</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="userTableBody">
                            <?php foreach($result as $row): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?php echo htmlspecialchars($row['user_id']); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?php echo htmlspecialchars($row['first_name'] . ' ' . ($row['middle_name'] ?? '') . ' ' . $row['last_name']); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php echo htmlspecialchars($row['patron_type']); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php echo htmlspecialchars($row['email']); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php echo htmlspecialchars($row['address']); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <div class="flex space-x-2">
                                        <button onclick="approveAccount('<?php echo htmlspecialchars($row['user_id']); ?>')" 
                                                class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded-lg text-sm flex items-center">
                                            <i class="lni lni-checkmark-circle mr-1"></i> Approve
                                        </button>
                                        <button onclick="removeAccount('<?php echo htmlspecialchars($row['user_id']); ?>')" 
                                                class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded-lg text-sm flex items-center">
                                            <i class="lni lni-close-circle mr-1"></i> Decline
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

    <script>
    function approveAccount(user_id) {
        Swal.fire({
            title: 'Approve Account',
            text: 'Are you sure you want to approve this account?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#10B981',
            cancelButtonColor: '#EF4444',
            confirmButtonText: 'Yes, approve it!',
            cancelButtonText: 'No, cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                sendRequest(user_id, 'approve');
            }
        });
    }

    function removeAccount(user_id) {
        Swal.fire({
            title: 'Decline Account',
            text: 'Are you sure you want to decline this account?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#EF4444',
            cancelButtonColor: '#6B7280',
            confirmButtonText: 'Yes, decline it!',
            cancelButtonText: 'No, cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                sendRequest(user_id, 'remove');
            }
        });
    }

    function sendRequest(user_id, action) {
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "confirm.php?" + new Date().getTime(), true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                if (xhr.responseText.trim() === "success") {
                    var row = document.querySelector("button[onclick*='" + user_id + "']").closest('tr');
                    if (action === 'approve') {
                        Swal.fire({
                            title: 'Success!',
                            text: 'Account approved successfully!',
                            icon: 'success',
                            confirmButtonColor: '#10B981'
                        });
                    } else if (action === 'remove') {
                        Swal.fire({
                            title: 'Success!',
                            text: 'Account declined successfully!',
                            icon: 'success',
                            confirmButtonColor: '#10B981'
                        });
                    }
                    row.parentNode.removeChild(row);
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Failed to process the request.',
                        icon: 'error',
                        confirmButtonColor: '#EF4444'
                    });
                }
            }
        };
        xhr.send("user_id=" + encodeURIComponent(user_id) + "&action=" + encodeURIComponent(action));
    }

    function searchUsers() {
        const input = document.getElementById('searchInput').value.toLowerCase();
        const rows = document.querySelectorAll('#userTableBody tr');
        rows.forEach(row => {
            const userId = row.cells[0].textContent.toLowerCase();
            const userName = row.cells[1].textContent.toLowerCase();
            if (userId.includes(input) || userName.includes(input)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }
    </script>
</body>
</html>