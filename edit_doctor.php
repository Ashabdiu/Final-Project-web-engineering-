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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $specialization = $_POST['specialization'];
    $qualification = $_POST['qualification'];
    $phone = $_POST['phone'];
    
    try {
        $pdo->beginTransaction();
        
     
        $stmt = $pdo->prepare("
            UPDATE doctors SET 
                full_name = ?, 
                specialization = ?, 
                qualification = ?, 
                phone = ?
            WHERE id = ?
        ");
        $stmt->execute([$full_name, $specialization, $qualification, $phone, $doctor_id]);
        
       
        $stmt = $pdo->prepare("UPDATE users SET email = ?, username = ? WHERE id = ?");
        $stmt->execute([$email, $username, $doctor['user_id']]);
        
        $pdo->commit();
        $_SESSION['success'] = "Doctor updated successfully";
        header("Location: view_doctor.php?id=$doctor_id");
        exit();
    } catch (PDOException $e) {
        $pdo->rollBack();
        $error = "Error updating doctor: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Doctor</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'header.php'; ?>
    
    <main class="container">
        <h2>Edit Doctor</h2>
        
        <?php if (isset($error)): ?>
            <div class="alert error"><?= $error ?></div>
        <?php endif; ?>
        
        <form method="post">
            <div class="form-group">
                <label>Full Name *</label>
                <input type="text" name="full_name" value="<?= htmlspecialchars($doctor['full_name']) ?>" required>
            </div>
            
            <div class="form-group">
                <label>Email *</label>
                <input type="email" name="email" value="<?= htmlspecialchars($doctor['email']) ?>" required>
            </div>
            
            <div class="form-group">
                <label>Username *</label>
                <input type="text" name="username" value="<?= htmlspecialchars($doctor['username']) ?>" required>
            </div>
            
            <div class="form-group">
                <label>Specialization *</label>
                <input type="text" name="specialization" value="<?= htmlspecialchars($doctor['specialization']) ?>" required>
            </div>
            
            <div class="form-group">
                <label>Qualification</label>
                <input type="text" name="qualification" value="<?= htmlspecialchars($doctor['qualification'] ?? '') ?>">
            </div>
            
            <div class="form-group">
                <label>Phone</label>
                <input type="tel" name="phone" value="<?= htmlspecialchars($doctor['phone'] ?? '') ?>">
            </div>
            
            <button type="submit" class="btn">Update Doctor</button>
            <a href="view_doctor.php?id=<?= $doctor_id ?>" class="btn">Cancel</a>
        </form>
    </main>
</body>
</html>