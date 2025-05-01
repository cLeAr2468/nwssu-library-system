<?php
session_start();
include './activity_logger.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    $element_id = $_POST['element_id'] ?? '';
    $page_url = $_POST['page_url'] ?? '';
    $click_details = $_POST['click_details'] ?? '';
    
    if (!empty($element_id) && !empty($page_url)) {
        logUserClick($_SESSION['user_id'], $element_id, $page_url, $click_details);
    }
}
?> 