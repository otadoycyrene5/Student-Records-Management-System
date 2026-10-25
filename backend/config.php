<?php
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

function respond($data, $status = 200) {
    http_response_code($status);
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
    exit;
}

$servername = 'localhost';
$username = 'root';
$password = '';
$dbname = 'student_records';

mysqli_report(MYSQLI_REPORT_OFF);
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    respond(['ok'=>false, 'message'=>'Database connection failed. Start MySQL and check backend/config.php.'], 500);
}
if (!$conn->set_charset('utf8mb4')) {
    respond(['ok'=>false, 'message'=>'Could not set the database character encoding.'], 500);
}

session_start();
if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}

function studentValues() {
    $values = [];
    $fields = ['student_number', 'first_name', 'last_name', 'email', 'course', 'year_level'];
    foreach ($fields as $field) {
        $values[$field] = '';
        if (isset($_POST[$field]) && is_string($_POST[$field])) {
            $values[$field] = trim($_POST[$field]);
        }
    }
    return $values;
}

function validateStudent($values) {
    $errors = [];
    $required_fields = ['student_number'=>'Student ID', 'first_name'=>'First name', 'last_name'=>'Last name', 'email'=>'Email', 'course'=>'Course'];
    foreach ($required_fields as $field => $label) {
        if ($values[$field] === '') {
            $errors[] = $label . ' is required.';
        }
    }
    if ($values['student_number'] !== '' && !preg_match('/^[0-9]{4}-[0-9]{5}-[A-Z]{2}-[0-9]$/D', $values['student_number'])) {
        $errors[] = 'Use Student ID format YYYY-#####-AA-#, e.g. 2024-12345-MN-0.';
    }
    if (mb_strlen($values['first_name'], 'UTF-8') > 80 || mb_strlen($values['last_name'], 'UTF-8') > 80 || mb_strlen($values['course'], 'UTF-8') > 120 || mb_strlen($values['email'], 'UTF-8') > 160) {
        $errors[] = 'One or more fields are too long.';
    }
    if (!filter_var($values['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Enter a valid email address.';
    }
    if (!in_array($values['year_level'], ['1', '2', '3', '4'], true)) {
        $errors[] = 'Select a valid year level.';
    }
    return $errors;
}
?>
