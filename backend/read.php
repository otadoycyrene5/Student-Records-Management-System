<?php
require_once __DIR__ . '/config.php';
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    header('Allow: GET');
    respond(['ok'=>false, 'message'=>'Use GET to retrieve students.'], 405);
}

if (isset($_GET['id'])) {
    $id = filter_var($_GET['id'], FILTER_VALIDATE_INT);
    if (!$id || $id < 1) {
        respond(['ok'=>false, 'message'=>'Invalid student record.'], 422);
    }
    $stmt = $conn->prepare('SELECT * FROM students WHERE id = ?');
    if (!$stmt) {
        respond(['ok'=>false, 'message'=>'Could not prepare the student query.'], 500);
    }
    $stmt->bind_param('i', $id);
    if (!$stmt->execute()) {
        respond(['ok'=>false, 'message'=>'Could not retrieve the student.'], 500);
    }
    $student = $stmt->get_result()->fetch_assoc();
    if (!$student) {
        respond(['ok'=>false, 'message'=>'Student record not found.'], 404);
    }
    respond(['ok'=>true, 'student'=>$student, 'csrf'=>$_SESSION['csrf']]);
}

$q = '';
if (isset($_GET['q'])) {
    if (!is_string($_GET['q'])) {
        respond(['ok'=>false, 'message'=>'Enter a valid search value.'], 422);
    }
    $q = trim($_GET['q']);
}
if ($q !== '') {
    $stmt = $conn->prepare("SELECT * FROM students WHERE student_number LIKE ? OR first_name LIKE ? OR last_name LIKE ? OR CONCAT(first_name, ' ', last_name) LIKE ? OR email LIKE ? OR course LIKE ? ORDER BY id DESC");
    if (!$stmt) {
        respond(['ok'=>false, 'message'=>'Could not prepare the search query.'], 500);
    }
    $term = '%' . $q . '%';
    $stmt->bind_param('ssssss', $term, $term, $term, $term, $term, $term);
    if (!$stmt->execute()) {
        respond(['ok'=>false, 'message'=>'Could not search student records.'], 500);
    }
    $result = $stmt->get_result();
} else {
    $result = $conn->query('SELECT * FROM students ORDER BY id DESC');
}
if (!$result) {
    respond(['ok'=>false, 'message'=>'Could not read student records. Import database/database.sql first.'], 500);
}
$students = [];
while ($student = $result->fetch_assoc()) {
    $students[] = $student;
}
respond(['ok'=>true, 'students'=>$students, 'csrf'=>$_SESSION['csrf']]);
?>
