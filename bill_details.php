<?php
require 'auth.php';
require 'db.php';

$bill_id = (int)$_GET['id'];
$bill = $pdo->prepare("
    SELECT b.*, 
           p.full_name AS patient_name,
           d.full_name AS doctor_name,
           a.appointment_date, a.appointment_time
    FROM bills b
    JOIN patients p ON b.patient_id = p.id
    JOIN doctors d ON b.doctor_id = d.id
    LEFT JOIN appointments a ON b.appointment_id = a.id
    WHERE b.id = ?
");
$bill->execute([$bill_id]);
$bill = $bill->fetch();

if (!$bill) {
    header("Location: " . (isPatient() ? "view_bills.php" : "dashboard.php"));
    exit();
}

if (isPatient()) {
    $patient_id = $pdo->query("SELECT id FROM patients WHERE user_id = {$_SESSION['user_id']}")->fetchColumn();
    if ($bill['patient_id'] != $patient_id) {
        header("Location: view_bills.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Bill Details</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'header.php'; ?>
    
    <main class="container">
        <h2>Bill Details</h2>
        
        <div class="bill-details">
            <div class="detail-row">
                <strong>Bill ID:</strong>
                <span>#<?= $bill['id'] ?></span>
            </div>
            <?php if (isAdmin()): ?>
                <div class="detail-row">
                    <strong>Patient:</strong>
                    <span><?= htmlspecialchars($bill['patient_name']) ?></span>
                </div>
            <?php endif; ?>
            <div class="detail-row">
                <strong>Doctor:</strong>
                <span><?= htmlspecialchars($bill['doctor_name']) ?></span>
            </div>
            <?php if ($bill['appointment_id']): ?>
                <div class="detail-row">
                    <strong>Appointment:</strong>
                    <span>
                        <?= date('M j, Y', strtotime($bill['appointment_date'])) ?>
                        at <?= date('g:i A', strtotime($bill['appointment_time'])) ?>
                    </span>
                </div>
            <?php endif; ?>
            <div class="detail-row">
                <strong>Amount:</strong>
                <span>$<?= number_format($bill['amount'], 2) ?></span>
            </div>
            <div class="detail-row">
                <strong>Status:</strong>
                <span class="status-<?= $bill['status'] ?>"><?= ucfirst($bill['status']) ?></span>
            </div>
            <div class="detail-row">
                <strong>Issued On:</strong>
                <span><?= date('M j, Y g:i A', strtotime($bill['created_at'])) ?></span>
            </div>
            <?php if ($bill['status'] == 'paid' && $bill['paid_at']): ?>
                <div class="detail-row">
                    <strong>Paid On:</strong>
                    <span><?= date('M j, Y g:i A', strtotime($bill['paid_at'])) ?></span>
                </div>
            <?php endif; ?>
            <div class="detail-section">
                <h3>Description</h3>
                <p><?= nl2br(htmlspecialchars($bill['description'])) ?></p>
            </div>
            
            <div class="action-buttons">
                <?php if (isAdmin() && $bill['status'] == 'unpaid'): ?>
                    <a href="mark_paid.php?id=<?= $bill['id'] ?>" class="btn">Mark as Paid</a>
                <?php endif; ?>
                <a href="<?= isAdmin() ? 'view_bills.php' : 'dashboard.php' ?>" class="btn">Back</a>
            </div>
        </div>
    </main>
</body>
</html>