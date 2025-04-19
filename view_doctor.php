<?php
require 'auth.php';
require 'db.php';

if (!isAdmin()) {
    header("Location: index.php");
    exit();
}

$doctor_id = (int)$_GET['id'];
$doctor = $pdo->query("
    SELECT d.*, u.email, u.username 
    FROM doctors d 
    JOIN users u ON d.user_id = u.id 
    WHERE d.id = $doctor_id
")->fetch();

if (!$doctor) {
    header("Location: doctors.php");
    exit();
}

$appointments = $pdo->prepare("
    SELECT a.*, p.full_name AS patient_name 
    FROM appointments a 
    JOIN patients p ON a.patient_id = p.id 
    WHERE a.doctor_id = ? AND a.status != 'Completed'
    ORDER BY a.appointment_date, a.appointment_time
");
$appointments->execute([$doctor_id]);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Doctor Details</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'header.php'; ?>
    
    <main class="container">
        <h2>Doctor Details</h2>
        
        <div class="doctor-details">
            <div class="detail-row">
                <strong>ID:</strong>
                <span><?= $doctor['id'] ?></span>
            </div>
            <div class="detail-row">
                <strong>Name:</strong>
                <span><?= htmlspecialchars($doctor['full_name']) ?></span>
            </div>
            <div class="detail-row">
                <strong>Email:</strong>
                <span><?= htmlspecialchars($doctor['email']) ?></span>
            </div>
            <div class="detail-row">
                <strong>Username:</strong>
                <span><?= htmlspecialchars($doctor['username']) ?></span>
            </div>
            <div class="detail-row">
                <strong>Specialization:</strong>
                <span><?= htmlspecialchars($doctor['specialization']) ?></span>
            </div>
            <div class="detail-row">
                <strong>Qualification:</strong>
                <span><?= htmlspecialchars($doctor['qualification'] ?? 'N/A') ?></span>
            </div>
            <div class="detail-row">
                <strong>Phone:</strong>
                <span><?= htmlspecialchars($doctor['phone'] ?? 'N/A') ?></span>
            </div>
           
            
            <h3>Upcoming Appointments</h3>
            <?php if ($appointments->rowCount() > 0): ?>
                <table class="appointments-table">
                    <thead>
                        <tr>
                            <th>Patient</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($appointments as $apt): ?>
                        <tr>
                            <td><?= htmlspecialchars($apt['patient_name']) ?></td>
                            <td><?= date('M j, Y', strtotime($apt['appointment_date'])) ?></td>
                            <td><?= date('g:i A', strtotime($apt['appointment_time'])) ?></td>
                            <td><?= $apt['status'] ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No upcoming appointments.</p>
            <?php endif; ?>
            
            <div class="action-buttons">
                <a href="edit_doctor.php?id=<?= $doctor['id'] ?>" class="btn">Edit</a>
                <a href="doctors.php" class="btn">Back to Doctors</a>
            </div>
        </div>
    </main>
</body>
</html>