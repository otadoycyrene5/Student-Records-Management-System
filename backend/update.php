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
$id = false;
if (isset($_POST['id'])) {
    $id = filter_var($_POST['id'], FILTER_VALIDATE_INT);
}
if (!$id || $id < 1) {
    $errors[] = 'Invalid student record.';
}
if ($errors) {
    respond(['ok'=>false, 'message'=>'Please check the student information.', 'errors'=>$errors], 422);
}

$student_number = $values['student_number'];
$first_name = $values['first_name'];
$last_name = $values['last_name'];
$email = $values['email'];
$course = $values['course'];
$year_level = (int)$values['year_level'];

$stmt = $conn->prepare('UPDATE students SET student_number=?, first_name=?, last_name=?, email=?, course=?, year_level=? WHERE id=?');
if (!$stmt) {
    respond(['ok'=>false, 'message'=>'Could not prepare the save query.'], 500);
}
$stmt->bind_param('sssssii', $student_number, $first_name, $last_name, $email, $course, $year_level, $id);
if (!$stmt->execute()) {
    if ($stmt->errno === 1062) {
        respond(['ok'=>false, 'message'=>'Student ID or email already exists.'], 409);
    }
    respond(['ok'=>false, 'message'=>'Could not save the student record.'], 500);
}

if ($stmt->affected_rows === 0) {
    $check = $conn->prepare('SELECT id FROM students WHERE id = ?');
    if (!$check) {
        respond(['ok'=>false, 'message'=>'Could not prepare the student check.'], 500);
    }
    $check->bind_param('i', $id);
    if (!$check->execute()) {
        respond(['ok'=>false, 'message'=>'Could not check the student record.'], 500);
    }
    if (!$check->get_result()->fetch_assoc()) {
        respond(['ok'=>false, 'message'=>'Student record no longer exists.'], 404);
    }
}
respond(['ok'=>true, 'message'=>'Student update saved.', 'id'=>$id]);
?>
