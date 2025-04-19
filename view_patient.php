<?php
require 'auth.php';
require 'db.php';

if (!isAdmin() && !isDoctor()) {
    header("Location: index.php");
    exit();
}

$patient_id = (int)$_GET['id'];
$patient = $pdo->query("
    SELECT p.*, u.email, u.username 
    FROM patients p 
    JOIN users u ON p.user_id = u.id 
    WHERE p.id = $patient_id
")->fetch();

if (!$patient) {
    header("Location: ". (isAdmin() ? "patients.php" : "dashboard.php"));
    exit();
}


$appointments = $pdo->prepare("
    SELECT a.*, d.full_name AS doctor_name, d.specialization
    FROM appointments a
    JOIN doctors d ON a.doctor_id = d.id
    WHERE a.patient_id = ? AND a.status != 'Completed'
    ORDER BY a.appointment_date, a.appointment_time
");
$appointments->execute([$patient_id]);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Patient Details</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'header.php'; ?>
    
    <main class="container">
        <h2>Patient Details</h2>
        
        <div class="patient-details">
            <div class="detail-section">
                <h3>Personal Information</h3>
                <div class="detail-row">
                    <strong>ID:</strong>
                    <span><?= $patient['id'] ?></span>
                </div>
                <div class="detail-row">
                    <strong>Full Name:</strong>
                    <span><?= htmlspecialchars($patient['full_name']) ?></span>
                </div>
                <div class="detail-row">
                    <strong>Email:</strong>
                    <span><?= htmlspecialchars($patient['email']) ?></span>
                </div>
                <div class="detail-row">
                    <strong>Username:</strong>
                    <span><?= htmlspecialchars($patient['username']) ?></span>
                </div>
                <div class="detail-row">
                    <strong>Date of Birth:</strong>
                    <span><?= $patient['dob'] ? date('M j, Y', strtotime($patient['dob'])) : 'N/A' ?></span>
                </div>
                <div class="detail-row">
                    <strong>Gender:</strong>
                    <span><?= $patient['gender'] ?? 'N/A' ?></span>
                </div>
                <div class="detail-row">
                    <strong>Phone:</strong>
                    <span><?= htmlspecialchars($patient['phone'] ?? 'N/A') ?></span>
                </div>
                <div class="detail-row">
                    <strong>Blood Group:</strong>
                    <span><?= htmlspecialchars($patient['blood_group'] ?? 'N/A') ?></span>
                </div>
                <div class="detail-row">
                    <strong>Address:</strong>
                    <span><?= htmlspecialchars($patient['address'] ?? 'N/A') ?></span>
                </div>
            </div>
            
            <div class="detail-section">
                <h3>Upcoming Appointments</h3>
                <?php if ($appointments->rowCount() > 0): ?>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Doctor</th>
                                <th>Specialization</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($appointments as $apt): ?>
                            <tr>
                                <td><?= htmlspecialchars($apt['doctor_name']) ?></td>
                                <td><?= htmlspecialchars($apt['specialization']) ?></td>
                                <td><?= date('M j, Y', strtotime($apt['appointment_date'])) ?></td>
                                <td><?= date('g:i A', strtotime($apt['appointment_time'])) ?></td>
                                <td><?= $apt['status'] ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No upcoming appointments found.</p>
                <?php endif; ?>
            </div>
            
            <div class="action-buttons">
                <?php if (isAdmin()): ?>
                    <a href="edit_patient.php?id=<?= $patient_id ?>" class="btn">Edit Patient</a>
                <?php endif; ?>
                <a href="<?= isAdmin() ? 'patients.php' : 'dashboard.php' ?>" class="btn">Back</a>
            </div>
        </div>
    </main>
</body>
</html>