<?php
require_once __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Allow: POST');
    respond(['ok'=>false, 'message'=>'Use POST to submit this form.'], 405);
}
if (!isset($_POST['csrf']) || !is_string($_POST['csrf']) || !hash_equals($_SESSION['csrf'], $_POST['csrf'])) {
    respond(['ok'=>false, 'message'=>'Your form session is invalid. Reload the page and try again.'], 403);
}
$id = false;
if (isset($_POST['id'])) {
    $id = filter_var($_POST['id'], FILTER_VALIDATE_INT);
}
if (!$id || $id < 1) {
    respond(['ok'=>false, 'message'=>'Invalid student record.'], 422);
}
$stmt = $conn->prepare('DELETE FROM students WHERE id = ?');
if (!$stmt) {
    respond(['ok'=>false, 'message'=>'Could not prepare the delete query.'], 500);
}
$stmt->bind_param('i', $id);
if (!$stmt->execute()) {
    respond(['ok'=>false, 'message'=>'Could not delete the student.'], 500);
}
if ($stmt->affected_rows === 0) {
    respond(['ok'=>false, 'message'=>'Student record no longer exists.'], 404);
}
respond(['ok'=>true, 'message'=>'Student deleted successfully.']);
?>
