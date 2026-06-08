<?php
header('Content-Type: application/json');
error_reporting(0);
require_once __DIR__ . '/../db.php';

$code = strtoupper(trim($_GET['code'] ?? ''));

if (!$code) {
    echo json_encode(['success' => false, 'error' => 'Mã không hợp lệ']);
    exit;
}

$stmt = $mysqli->prepare("
    SELECT tr.*, c.name as coach_name
    FROM training_registrations tr
    LEFT JOIN coaches c ON tr.coach_id = c.id
    WHERE tr.student_code = ?
    LIMIT 1
");
$stmt->bind_param('s', $code);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$student) {
    echo json_encode(['success' => false, 'error' => 'Không tìm thấy học viên']);
    exit;
}

$course_labels = [
    'beginner'     => 'Cơ bản (3 tháng)',
    'intermediate' => 'Trung cấp (4 tháng)',
    'advanced'     => 'Nâng cao (5 tháng)',
];

echo json_encode([
    'success' => true,
    'student' => [
        'student_code'  => $student['student_code'],
        'student_name'  => $student['student_name'],
        'phone'         => $student['phone'],
        'course'        => $student['course'],
        'course_label'  => $course_labels[$student['course']] ?? $student['course'],
        'coach_name'    => $student['coach_name'] ?? 'Chưa phân công',
        'schedule_days' => $student['schedule_days'],
        'schedule_time' => $student['schedule_time'],
        'week_start'    => $student['week_start'],
        'status'        => $student['status'],
    ]
]);
