<?php
require 'auth.php';
require 'db.php';

if (!isAdmin()) {
    header("Location: index.php");
    exit();
}

$bill_id = (int)$_GET['id'];
$pdo->prepare("UPDATE bills SET status = 'paid', paid_at = NOW() WHERE id = ?")->execute([$bill_id]);

header("Location: bill_details.php?id=$bill_id");
exit();