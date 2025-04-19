<?php
require 'auth.php';
require 'db.php';

if (!isAdmin()) {
    header("Location: index.php");
    exit();
}

$summary = $pdo->query("
    SELECT 
        COUNT(*) as total_bills,
        SUM(amount) as total_amount,
        SUM(CASE WHEN status = 'paid' THEN amount ELSE 0 END) as paid_amount,
        SUM(CASE WHEN status = 'unpaid' THEN amount ELSE 0 END) as unpaid_amount
    FROM bills
")->fetch();

$bills = $pdo->query("
    SELECT b.*, p.full_name as patient_name, d.full_name as doctor_name
    FROM bills b
    JOIN patients p ON b.patient_id = p.id
    JOIN doctors d ON b.doctor_id = d.id
    ORDER BY b.created_at DESC
    LIMIT 10
")->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Billing Reports</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'header.php'; ?>
    
    <main class="container">
        <h2>Billing Reports</h2>
        
        <div class="report-summary">
            <div class="summary-card">
                <h3>Total Bills</h3>
                <p><?= $summary['total_bills'] ?></p>
            </div>
            <div class="summary-card">
                <h3>Total Amount</h3>
                <p>$<?= number_format($summary['total_amount'], 2) ?></p>
            </div>
            <div class="summary-card paid">
                <h3>Paid Amount</h3>
                <p>$<?= number_format($summary['paid_amount'], 2) ?></p>
            </div>
            <div class="summary-card unpaid">
                <h3>Unpaid Amount</h3>
                <p>$<?= number_format($summary['unpaid_amount'], 2) ?></p>
            </div>
        </div>
        
        <h3>Recent Bills</h3>
        <table class="billing-table">
           
        </table>
    </main>
</body>
</html>