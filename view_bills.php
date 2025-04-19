<?php
require 'auth.php';
require 'db.php';

$bills = [];
$is_patient = isPatient();

if ($is_patient) {
    $patient_id = $pdo->query("SELECT id FROM patients WHERE user_id = {$_SESSION['user_id']}")->fetchColumn();
    $bills = $pdo->prepare("
        SELECT b.*, d.full_name AS doctor_name 
        FROM bills b
        JOIN doctors d ON b.doctor_id = d.id
        WHERE b.patient_id = ?
        ORDER BY b.created_at DESC
    ");
    $bills->execute([$patient_id]);
} elseif (isAdmin()) {
    $bills = $pdo->query("
        SELECT b.*, p.full_name AS patient_name, d.full_name AS doctor_name 
        FROM bills b
        JOIN patients p ON b.patient_id = p.id
        JOIN doctors d ON b.doctor_id = d.id
        ORDER BY b.created_at DESC
    ");
} else {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title><?= $is_patient ? 'My Bills' : 'Billing Management' ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'header.php'; ?>
    
    <main class="container">
        <h2><?= $is_patient ? 'My Bills' : 'Billing Management' ?></h2>
        
        <table class="billing-table">
            <thead>
                <tr>
                    <?php if (isAdmin()): ?>
                        <th>Patient</th>
                    <?php endif; ?>
                    <th>Doctor</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($bills as $bill): ?>
                <tr class="status-<?= $bill['status'] ?>">
                    <?php if (isAdmin()): ?>
                        <td><?= htmlspecialchars($bill['patient_name']) ?></td>
                    <?php endif; ?>
                    <td><?= htmlspecialchars($bill['doctor_name']) ?></td>
                    <td>$<?= number_format($bill['amount'], 2) ?></td>
                    <td><?= ucfirst($bill['status']) ?></td>
                    <td><?= date('M j, Y', strtotime($bill['created_at'])) ?></td>
                    <td class="actions">
                        <a href="bill_details.php?id=<?= $bill['id'] ?>" class="btn small">View</a>
                        <?php if (isAdmin() && $bill['status'] == 'unpaid'): ?>
                            <a href="mark_paid.php?id=<?= $bill['id'] ?>" class="btn small">Mark Paid</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </main>
</body>
</html>