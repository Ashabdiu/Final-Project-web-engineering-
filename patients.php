<?php
require 'auth.php';
require 'db.php';

if (!isAdmin()) {
    header("Location: index.php");
    exit();
}

if (isset($_GET['delete'])) {
    $patient_id = (int)$_GET['delete'];
    try {
        $pdo->beginTransaction();
        
        $user_id = $pdo->query("SELECT user_id FROM patients WHERE id = $patient_id")->fetchColumn();

        $pdo->exec("DELETE FROM patients WHERE id = $patient_id");
  
        $pdo->exec("DELETE FROM users WHERE id = $user_id");
        
        $pdo->commit();
        $_SESSION['success'] = "Patient deleted successfully";
    } catch (PDOException $e) {
        $pdo->rollBack();
        $_SESSION['error'] = "Error deleting patient: " . $e->getMessage();
    }
    header("Location: patients.php");
    exit();
}

$patients = $pdo->query("
    SELECT p.*, u.email, u.username 
    FROM patients p 
    JOIN users u ON p.user_id = u.id
")->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Patients</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'header.php'; ?>
    
    <main class="container">
        <h2>Manage Patients</h2>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert success"><?= $_SESSION['success'] ?></div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert error"><?= $_SESSION['error'] ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
        
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($patients as $patient): ?>
                <tr>
                    <td><?= $patient['id'] ?></td>
                    <td><?= htmlspecialchars($patient['full_name']) ?></td>
                    <td><?= htmlspecialchars($patient['email']) ?></td>
                    <td><?= htmlspecialchars($patient['phone'] ?? 'N/A') ?></td>
                    <td class="actions">
                        <a href="view_patient.php?id=<?= $patient['id'] ?>" class="btn small">View</a>
                        <a href="edit_patient.php?id=<?= $patient['id'] ?>" class="btn small">Edit</a>
                        <a href="patients.php?delete=<?= $patient['id'] ?>" class="btn small danger" 
                           onclick="return confirm('Are you sure? This will permanently delete this patient.')">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </main>
</body>
</html>