<?php
require 'auth.php';
redirectIfNotLoggedIn();

$message = '';
$appointments = [];
$doctors = [];
$patients = [];

if (isPatient()) {
    $patient_id = $pdo->query("SELECT id FROM patients WHERE user_id = {$_SESSION['user_id']}")->fetchColumn();
} elseif (isDoctor()) {
    $doctor_id = $pdo->query("SELECT id FROM doctors WHERE user_id = {$_SESSION['user_id']}")->fetchColumn();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['create'])) {
           
            $patient_id = isPatient() ? $patient_id : $_POST['patient_id'];
            $stmt = $pdo->prepare("INSERT INTO appointments 
                                  (patient_id, doctor_id, appointment_date, appointment_time, status) 
                                  VALUES (?, ?, ?, ?, 'Pending')");
            $stmt->execute([
                $patient_id,
                $_POST['doctor_id'],
                $_POST['appointment_date'],
                $_POST['appointment_time']
            ]);
            $message = "Appointment booked successfully!";
            
        } elseif (isset($_POST['update'])) {
           
            $stmt = $pdo->prepare("UPDATE appointments SET status = ? WHERE id = ?");
            $stmt->execute([$_POST['status'], $_POST['id']]);
            $message = "Appointment updated successfully!";
            
        } elseif (isset($_POST['delete'])) {
           
            $stmt = $pdo->prepare("DELETE FROM appointments WHERE id = ?");
            $stmt->execute([$_POST['id']]);
            $message = "Appointment cancelled successfully!";
        }
    } catch (PDOException $e) {
        $message = "Error: " . $e->getMessage();
    }
}

$query = "SELECT a.*, 
          p.full_name AS patient_name, 
          d.full_name AS doctor_name,
          d.specialization
          FROM appointments a
          JOIN patients p ON a.patient_id = p.id
          JOIN doctors d ON a.doctor_id = d.id";

if (isPatient()) {
    $query .= " WHERE a.patient_id = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$patient_id]);
} elseif (isDoctor()) {
    $query .= " WHERE a.doctor_id = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$doctor_id]);
} else {
    $stmt = $pdo->query($query);
}

$appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

$doctors = $pdo->query("SELECT id, full_name, specialization FROM doctors")->fetchAll();

if (isAdmin()) {
    $patients = $pdo->query("SELECT id, full_name FROM patients")->fetchAll();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointments - Hospital System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Hospital Management System</h1>
        <nav>
            <span>Welcome, <?= htmlspecialchars($_SESSION['username']) ?> (<?= $_SESSION['role'] ?>)</span>
            <a href="dashboard.php">Dashboard</a>
            <?php if (isAdmin() || isDoctor()): ?>
                <a href="patients.php">Patients</a>
                <a href="doctors.php">Doctors</a>
            <?php endif; ?>
            <a href="appointments.php">Appointments</a>
            <a href="logout.php">Logout</a>
        </nav>
    </header>
    
    <main class="container">
        <h2>Appointment Management</h2>
        
        <?php if ($message): ?>
            <div class="alert <?= strpos($message, 'Error') === false ? 'success' : 'error' ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_GET['action']) && $_GET['action'] === 'create'): ?>
            
            <div class="card">
                <h3>Book New Appointment</h3>
                <form method="post">
                    <?php if (isAdmin()): ?>
                        <div class="form-group">
                            <label>Patient:</label>
                            <select name="patient_id" required>
                                <?php foreach ($patients as $patient): ?>
                                    <option value="<?= $patient['id'] ?>">
                                        <?= htmlspecialchars($patient['full_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <label>Doctor:</label>
                        <select name="doctor_id" required>
                            <?php foreach ($doctors as $doctor): ?>
                                <option value="<?= $doctor['id'] ?>">
                                    <?= htmlspecialchars($doctor['full_name']) ?> 
                                    (<?= htmlspecialchars($doctor['specialization']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Date:</label>
                        <input type="date" name="appointment_date" required 
                               min="<?= date('Y-m-d') ?>">
                    </div>
                    
                    <div class="form-group">
                        <label>Time:</label>
                        <input type="time" name="appointment_time" required>
                    </div>
                    
                    <button type="submit" name="create" class="btn">Book Appointment</button>
                    <a href="appointments.php" class="btn">Cancel</a>
                </form>
            </div>
            
        <?php else: ?>
        
            <?php if (isPatient() || isAdmin()): ?>
                <a href="appointments.php?action=create" class="btn">Book New Appointment</a>
            <?php endif; ?>
            
            <div class="card">
                <h3>Appointment List</h3>
                
                <?php if (empty($appointments)): ?>
                    <p>No appointments found.</p>
                <?php else: ?>
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <?php if (isAdmin() || isDoctor()): ?>
                                    <th>Patient</th>
                                <?php endif; ?>
                                <?php if (isAdmin() || isPatient()): ?>
                                    <th>Doctor</th>
                                <?php endif; ?>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($appointments as $appt): ?>
                            <tr>
                                <td><?= $appt['id'] ?></td>
                                <?php if (isAdmin() || isDoctor()): ?>
                                    <td><?= htmlspecialchars($appt['patient_name']) ?></td>
                                <?php endif; ?>
                                <?php if (isAdmin() || isPatient()): ?>
                                    <td>
                                        <?= htmlspecialchars($appt['doctor_name']) ?>
                                        <?php if ($appt['specialization']): ?>
                                            (<?= htmlspecialchars($appt['specialization']) ?>)
                                        <?php endif; ?>
                                    </td>
                                <?php endif; ?>
                                <td><?= date('M j, Y', strtotime($appt['appointment_date'])) ?></td>
                                <td><?= date('g:i A', strtotime($appt['appointment_time'])) ?></td>
                                <td>
                                    <?php if (isAdmin() || isDoctor()): ?>
                                        <form method="post" class="inline-form">
                                            <input type="hidden" name="id" value="<?= $appt['id'] ?>">
                                            <select name="status" onchange="this.form.submit()">
                                                <option value="Pending" <?= $appt['status'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
                                                <option value="Confirmed" <?= $appt['status'] === 'Confirmed' ? 'selected' : '' ?>>Confirmed</option>
                                                <option value="Cancelled" <?= $appt['status'] === 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                                <option value="Completed" <?= $appt['status'] === 'Completed' ? 'selected' : '' ?>>Completed</option>
                                            </select>
                                            <input type="hidden" name="update">
                                        </form>
                                    <?php else: ?>
                                        <?= $appt['status'] ?>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($appt['status'] !== 'Cancelled' && (isAdmin() || isPatient())): ?>
                                        <form method="post" class="inline-form">
                                            <input type="hidden" name="id" value="<?= $appt['id'] ?>">
                                            <button type="submit" name="delete" class="btn small" 
                                                    onclick="return confirm('Are you sure you want to cancel this appointment?')">
                                                Cancel
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </main>
    
    <footer>
        <p>&copy; <?= date('Y') ?> Hospital Management System</p>
    </footer>
</body>
</html>