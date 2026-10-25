<?php
require_once __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Allow: POST');
    respond(['ok'=>false, 'message'=>'Use POST to submit this form.'], 405);
}
if (!isset($_POST['csrf']) || !is_string($_POST['csrf']) || !hash_equals($_SESSION['csrf'], $_POST['csrf'])) {
    respond(['ok'=>false, 'message'=>'Your form session is invalid. Reload the page and try again.'], 403);
}
$values = studentValues();
$errors = validateStudent($values);
if ($errors) {
    respond(['ok'=>false, 'message'=>'Please check the student information.', 'errors'=>$errors], 422);
}

$student_number = $values['student_number'];
$first_name = $values['first_name'];
$last_name = $values['last_name'];
$email = $values['email'];
$course = $values['course'];
$year_level = (int)$values['year_level'];

$stmt = $conn->prepare('INSERT INTO students (student_number, first_name, last_name, email, course, year_level) VALUES (?, ?, ?, ?, ?, ?)');
if (!$stmt) {
    respond(['ok'=>false, 'message'=>'Could not prepare the save query.'], 500);
}
$stmt->bind_param('sssssi', $student_number, $first_name, $last_name, $email, $course, $year_level);
if (!$stmt->execute()) {
    if ($stmt->errno === 1062) {
        respond(['ok'=>false, 'message'=>'Student ID or email already exists.'], 409);
    }
    respond(['ok'=>false, 'message'=>'Could not save the student record.'], 500);
}
respond(['ok'=>true, 'message'=>'Student added successfully.', 'id'=>$conn->insert_id], 201);
?>
