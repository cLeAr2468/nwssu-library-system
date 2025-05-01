<?php
session_start(); // Start the session
include '../component-library/connect.php';

header('Content-Type: application/json');

try {
    $term = $_GET['term'] ?? '';
    $category = $_GET['category'] ?? 'all';
    
    if (strlen($term) < 2) {
        echo json_encode([]);
        exit;
    }

    $conn = new PDO("mysql:host=$db_host;dbname=$db_name", $user_name, $user_password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $search_term = "%$term%";
    $suggestions = [];
    
    if ($category !== 'all') {
        // Validate category to prevent SQL injection
        $allowed_categories = ['title', 'author', 'publisher', 'ISBN', 'subject', 'material_type', 'sub_type'];
        if (!in_array($category, $allowed_categories)) {
            throw new Exception('Invalid category');
        }
        
        $query = $conn->prepare("SELECT DISTINCT $category FROM books WHERE $category LIKE :term ORDER BY $category LIMIT 10");
        $query->bindParam(':term', $search_term);
        $query->execute();
        $suggestions = $query->fetchAll(PDO::FETCH_COLUMN);
    } else {
        // Search across multiple columns with proper ordering
        $query = $conn->prepare("
            (SELECT DISTINCT title as value, 'title' as type FROM books WHERE title LIKE :term)
            UNION ALL
            (SELECT DISTINCT author as value, 'author' as type FROM books WHERE author LIKE :term)
            UNION ALL
            (SELECT DISTINCT publisher as value, 'publisher' as type FROM books WHERE publisher LIKE :term)
            UNION ALL
            (SELECT DISTINCT ISBN as value, 'ISBN' as type FROM books WHERE ISBN LIKE :term)
            UNION ALL
            (SELECT DISTINCT subject as value, 'subject' as type FROM books WHERE subject LIKE :term)
            UNION ALL
            (SELECT DISTINCT material_type as value, 'material_type' as type FROM books WHERE material_type LIKE :term)
            UNION ALL
            (SELECT DISTINCT sub_type as value, 'sub_type' as type FROM books WHERE sub_type LIKE :term)
            ORDER BY type, value
            LIMIT 10
        ");
        $query->bindParam(':term', $search_term);
        $query->execute();
        
        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        foreach ($results as $row) {
            $suggestions[] = $row['value'];
        }
    }

    // Remove any empty values and duplicates
    $suggestions = array_filter(array_unique($suggestions));
    
    echo json_encode($suggestions);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
} 