<?php
require 'auth.php';
require 'db.php';

if (!isAdmin()) {
    header("Location: index.php");
    exit();
}

if (isset($_GET['delete'])) {
    $doctor_id = (int)$_GET['delete'];
    try {
        $pdo->beginTransaction();
     
        $user_id = $pdo->query("SELECT user_id FROM doctors WHERE id = $doctor_id")->fetchColumn();
       
        $pdo->exec("DELETE FROM doctors WHERE id = $doctor_id");
     
        $pdo->exec("DELETE FROM users WHERE id = $user_id");
        
        $pdo->commit();
        $_SESSION['success'] = "Doctor deleted successfully";
    } catch (PDOException $e) {
        $pdo->rollBack();
        $_SESSION['error'] = "Error deleting doctor: " . $e->getMessage();
    }
    header("Location: doctors.php");
    exit();
}

$doctors = $pdo->query("
    SELECT d.*, u.email, u.username 
    FROM doctors d 
    JOIN users u ON d.user_id = u.id
")->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Doctors</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'header.php'; ?>
    
    <main class="container">
        <h2>Manage Doctors</h2>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert success"><?= $_SESSION['success'] ?></div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert error"><?= $_SESSION['error'] ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
        
        <a href="register.php?role=doctor" class="btn">Add New Doctor</a>
        
        <table>
        
<thead>
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Specialization</th>
        <th>Email</th>
        <th>Actions</th> 
    </tr>
</thead>

<tbody>
    <?php foreach ($doctors as $doctor): ?>
    <tr>
        <td><?= $doctor['id'] ?></td>
        <td><?= htmlspecialchars($doctor['full_name']) ?></td>
        <td><?= htmlspecialchars($doctor['specialization']) ?></td>
        <td><?= htmlspecialchars($doctor['email']) ?></td>
        <td class="actions">
            <a href="view_doctor.php?id=<?= $doctor['id'] ?>" class="btn small">View</a>
            <a href="edit_doctor.php?id=<?= $doctor['id'] ?>" class="btn small">Edit</a>
            <a href="doctors.php?delete=<?= $doctor['id'] ?>" class="btn small danger" 
               onclick="return confirm('Are you sure? This will permanently delete this doctor.')">Delete</a>
        </td>
    </tr>
    <?php endforeach; ?>
</tbody>
        </table>
    </main>
</body>
</html>