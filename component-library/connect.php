<?php
$db_host = 'localhost';
$db_name = 'library-system';
$user_name = 'root';
$user_password = '';

try {
    $conn = new PDO("mysql:host=$db_host;dbname=$db_name", $user_name, $user_password);
    // Set PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

// Check if unique_id() function already exists before declaring it
if (!function_exists('unique_id')) {
    function unique_id(){
        $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charLength = strlen($chars);
        $randomString = ''; 
        for ($i=0; $i < 20; $i++){
            $randomString.=$chars[mt_rand(0, $charLength - 1)];
        }
        return $randomString;
    }
}
?>
