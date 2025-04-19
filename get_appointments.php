<?php
require 'auth.php';
require 'db.php';

if (!isDoctor()) {
    header("HTTP/1.1 403 Forbidden");
    exit();
}

$patient_id = (int)$_GET['patient_id'];
$doctor_id = $pdo->query("SELECT id FROM doctors WHERE user_id = {$_SESSION['user_id']}")->fetchColumn();

$stmt = $pdo->prepare("
    SELECT id, appointment_date, appointment_time, status 
    FROM appointments 
    WHERE patient_id = ? AND doctor_id = ?
    ORDER BY appointment_date DESC
");
$stmt->execute([$patient_id, $doctor_id]);
$appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($appointments);