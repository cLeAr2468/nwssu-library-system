<?php
// Include database connection
include '../component-library/connect.php';

if (!function_exists('logActivity')) {
    function logActivity($user_id, $activity_type, $activity_details) {
        global $conn;
        
        try {
            // Get current date and time
            $current_date = date('Y-m-d H:i:s');
            
            // Prepare the insert statement
            $stmt = $conn->prepare("INSERT INTO activities (user_id, activity_type, activity_details, activity_date, status) 
                                   VALUES (?, ?, ?, ?, 'active')");
            
            // Execute the statement with the parameters
            $stmt->execute([$user_id, $activity_type, $activity_details, $current_date]);
            
            return true;
        } catch (PDOException $e) {
            // Log error to a file or handle it appropriately
            error_log("Activity logging failed: " . $e->getMessage());
            return false;
        }
    }
}

if (!function_exists('logUserClick')) {
    function logUserClick($user_id, $element_id, $page_url, $click_details = '') {
        global $conn;
        
        try {
            $current_date = date('Y-m-d H:i:s');
            $activity_type = 'click';
            $activity_details = "Clicked on element: $element_id on page: $page_url";
            if (!empty($click_details)) {
                $activity_details .= " - $click_details";
            }
            
            $stmt = $conn->prepare("INSERT INTO activities (user_id, activity_type, activity_details, activity_date, status) 
                                   VALUES (?, ?, ?, ?, 'active')");
            
            $stmt->execute([$user_id, $activity_type, $activity_details, $current_date]);
            
            return true;
        } catch (PDOException $e) {
            error_log("Click logging failed: " . $e->getMessage());
            return false;
        }
    }
}

if (!function_exists('logNavigationClick')) {
    function logNavigationClick($user_id, $page_name) {
        $activity_details = '';
        
        switch($page_name) {
            case 'home':
                $activity_details = 'User accessed Home page';
                break;
            case 'catalog':
                $activity_details = 'User accessed Book Catalog page';
                break;
            case 'topcollection':
                $activity_details = 'User accessed Top Collection page';
                break;
            case 'newcollection':
                $activity_details = 'User accessed New Collections page';
                break;
            case 'about_menu':
                $activity_details = 'User accessed About menu';
                break;
            case 'mission_vision':
                $activity_details = 'User accessed Mission & Vision page';
                break;
            case 'online_services':
                $activity_details = 'User accessed Online Services menu';
                break;
            case 'proquest':
                $activity_details = 'User accessed Proquest Central Database';
                break;
            case 'ejournals':
                $activity_details = 'User accessed Philippine E-Journals';
                break;
            case 'starbooks':
                $activity_details = 'User accessed DOST Starbooks';
                break;
            case 'ask_librarian':
                $activity_details = 'User accessed Ask a Librarian menu';
                break;
            case 'email_librarian':
                $activity_details = 'User accessed Email Account for Librarian';
                break;
            case 'messenger_librarian':
                $activity_details = 'User accessed Messenger for Librarian';
                break;
            default:
                $activity_details = "User accessed $page_name page";
        }
        
        return logActivity($user_id, 'click', $activity_details);
    }
}

// Handle AJAX requests for navigation logging
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['page']) && isset($_POST['user_id'])) {
    logNavigationClick($_POST['user_id'], $_POST['page']);
}

// Handle AJAX requests for activity logging
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['user_id']) && isset($_POST['activity_type']) && isset($_POST['activity_details'])) {
        logActivity(
            $_POST['user_id'],
            $_POST['activity_type'],
            $_POST['activity_details']
        );
    }
}

?> 