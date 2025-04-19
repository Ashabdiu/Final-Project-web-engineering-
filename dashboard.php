<?php
require 'auth.php';
require 'db.php';
redirectIfNotLoggedIn();

if (isPatient()) {
    $stmt = $pdo->prepare("SELECT * FROM patients WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user_data = $stmt->fetch();

    $unpaid_bills = $pdo->query("
        SELECT COUNT(*) FROM bills 
        WHERE patient_id = {$user_data['id']} 
        AND status = 'unpaid'
    ")->fetchColumn();
    
} elseif (isDoctor()) {
    $stmt = $pdo->prepare("SELECT * FROM doctors WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user_data = $stmt->fetch();
    
    $appointment_count = $pdo->query("
        SELECT COUNT(*) FROM appointments 
        WHERE doctor_id = {$user_data['id']} 
        AND status != 'Completed'
    ")->fetchColumn();
    
} elseif (isAdmin()) {
    
    $patient_count = $pdo->query("SELECT COUNT(*) FROM patients")->fetchColumn();
    $doctor_count = $pdo->query("SELECT COUNT(*) FROM doctors")->fetchColumn();
    $unpaid_bills_total = $pdo->query("
        SELECT SUM(amount) FROM bills WHERE status = 'unpaid'
    ")->fetchColumn();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Hospital System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <div class="header-container">
            <h1>Hospital Management System</h1>
            <nav>
                <span>Welcome, <?= htmlspecialchars($_SESSION['username']) ?> (<?= $_SESSION['role'] ?>)</span>
                <a href="dashboard.php" class="active">Dashboard</a>
                <?php if (isAdmin() || isDoctor()): ?>
                    <a href="patients.php">Patients</a>
                    <a href="doctors.php">Doctors</a>
                <?php endif; ?>
                <a href="appointments.php">Appointments</a>
                
                <?php if (isDoctor()): ?>
                    <a href="create_bill.php">Create Bill</a>
                <?php endif; ?>
                
                <?php if (isPatient()): ?>
                    <a href="view_bills.php">My Bills</a>
                <?php endif; ?>
                
                <?php if (isAdmin()): ?>
                    <a href="view_bills.php">Billing</a>
                    <a href="contact_view.php">Messages</a>
                <?php else: ?>
                    <a href="contact.php">Contact</a>
                <?php endif; ?>
                
                <a href="logout.php">Logout</a>
            </nav>
        </div>
    </header>
    
    <main class="container">
        <h2>Dashboard Overview</h2>
        
        <div class="dashboard-grid">
            
            <div class="card profile-card">
                <h3>Your Profile</h3>
                <?php if (isPatient()): ?>
                    <div class="profile-info">
                        <p><strong>Name:</strong> <?= htmlspecialchars($user_data['full_name']) ?></p>
                        <p><strong>Phone:</strong> <?= htmlspecialchars($user_data['phone'] ?? 'N/A') ?></p>
                        <p><strong>Blood Group:</strong> <?= htmlspecialchars($user_data['blood_group'] ?? 'N/A') ?></p>
                    </div>
                    <a href="edit_patient.php?id=<?= $user_data['id'] ?>" class="btn">Edit Profile</a>
                    
                <?php elseif (isDoctor()): ?>
                    <div class="profile-info">
                        <p><strong>Name:</strong> <?= htmlspecialchars($user_data['full_name']) ?></p>
                        <p><strong>Specialization:</strong> <?= htmlspecialchars($user_data['specialization']) ?></p>
                        <p><strong>Qualification:</strong> <?= htmlspecialchars($user_data['qualification'] ?? 'N/A') ?></p>
                    </div>
                    <a href="edit_doctor.php?id=<?= $user_data['id'] ?>" class="btn">Edit Profile</a>
                    
                <?php elseif (isAdmin()): ?>
                    <div class="profile-info">
                        <p><strong>Role:</strong> System Administrator</p>
                        <p><strong>Username:</strong> <?= htmlspecialchars($_SESSION['username']) ?></p>
                    </div>
                <?php endif; ?>
            </div>
            
            
            <div class="card stats-card">
                <h3>Quick Stats</h3>
                <?php if (isPatient()): ?>
                    <div class="stat-item">
                        <span class="stat-number"><?= $unpaid_bills ?></span>
                        <span class="stat-label">Unpaid Bills</span>
                    </div>
                    <?php
                    $appt_count = $pdo->query("
                        SELECT COUNT(*) FROM appointments 
                        WHERE patient_id = {$user_data['id']}
                        AND status != 'Completed'
                    ")->fetchColumn();
                    ?>
                    <div class="stat-item">
                        <span class="stat-number"><?= $appt_count ?></span>
                        <span class="stat-label">Upcoming Appointments</span>
                    </div>
                    
                <?php elseif (isDoctor()): ?>
                    <div class="stat-item">
                        <span class="stat-number"><?= $appointment_count ?></span>
                        <span class="stat-label">Upcoming Appointments</span>
                    </div>
                    <?php
                    $patient_count = $pdo->query("
                        SELECT COUNT(DISTINCT patient_id) 
                        FROM appointments 
                        WHERE doctor_id = {$user_data['id']}
                    ")->fetchColumn();
                    ?>
                    <div class="stat-item">
                        <span class="stat-number"><?= $patient_count ?></span>
                        <span class="stat-label">Active Patients</span>
                    </div>
                    
                <?php elseif (isAdmin()): ?>
                    <div class="stat-item">
                        <span class="stat-number"><?= $patient_count ?></span>
                        <span class="stat-label">Total Patients</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number"><?= $doctor_count ?></span>
                        <span class="stat-label">Total Doctors</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">$<?= number_format($unpaid_bills_total, 2) ?></span>
                        <span class="stat-label">Unpaid Bills Total</span>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="card actions-card">
                <h3>Quick Actions</h3>
                <div class="action-buttons">
                    <?php if (isPatient()): ?>
                        <a href="appointments.php?action=create" class="btn">Book Appointment</a>
                        <a href="appointments.php" class="btn">View Appointments</a>
                        <a href="view_bills.php" class="btn">View Bills</a>
                        <a href="medical_records.php" class="btn">Medical Records</a>
                        
                    <?php elseif (isDoctor()): ?>
                        <a href="appointments.php" class="btn">View Schedule</a>
                        <a href="patients.php" class="btn">My Patients</a>
                        <a href="create_bill.php" class="btn">Create Bill</a>
                        <a href="prescriptions.php" class="btn">Write Prescription</a>
                        
                    <?php elseif (isAdmin()): ?>
                        <a href="patients.php" class="btn">Manage Patients</a>
                        <a href="doctors.php" class="btn">Manage Doctors</a>
                        <a href="appointments.php" class="btn">View Appointments</a>
                        <a href="view_bills.php" class="btn">Billing Management</a>
                        <a href="reports.php" class="btn">Generate Reports</a>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="card activity-card">
                <h3>Recent Activity</h3>
                <?php if (isPatient()): ?>
                    <?php
                    $recent_appts = $pdo->query("
                        SELECT a.*, d.full_name as doctor_name 
                        FROM appointments a
                        JOIN doctors d ON a.doctor_id = d.id
                        WHERE a.patient_id = {$user_data['id']}
                        ORDER BY a.appointment_date DESC
                        LIMIT 3
                    ")->fetchAll();
                    ?>
                    <?php if (!empty($recent_appts)): ?>
                        <ul class="activity-list">
                            <?php foreach ($recent_appts as $apt): ?>
                            <li>
                                <strong><?= date('M j', strtotime($apt['appointment_date'])) ?></strong>
                                Appointment with Dr. <?= htmlspecialchars($apt['doctor_name']) ?>
                                <span class="status-badge <?= strtolower($apt['status']) ?>">
                                    <?= $apt['status'] ?>
                                </span>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                        <a href="appointments.php" class="btn small">View All</a>
                    <?php else: ?>
                        <p>No recent appointments found.</p>
                    <?php endif; ?>
                    
                <?php elseif (isDoctor()): ?>
                    <?php
                    $recent_patients = $pdo->query("
                        SELECT p.full_name, a.appointment_date, a.status
                        FROM appointments a
                        JOIN patients p ON a.patient_id = p.id
                        WHERE a.doctor_id = {$user_data['id']}
                        ORDER BY a.appointment_date DESC
                        LIMIT 3
                    ")->fetchAll();
                    ?>
                    <?php if (!empty($recent_patients)): ?>
                        <ul class="activity-list">
                            <?php foreach ($recent_patients as $patient): ?>
                            <li>
                                <strong><?= date('M j', strtotime($patient['appointment_date'])) ?></strong>
                                <?= htmlspecialchars($patient['full_name']) ?>
                                <span class="status-badge <?= strtolower($patient['status']) ?>">
                                    <?= $patient['status'] ?>
                                </span>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                        <a href="patients.php" class="btn small">View All Patients</a>
                    <?php else: ?>
                        <p>No recent patient activity.</p>
                    <?php endif; ?>
                    
                <?php elseif (isAdmin()): ?>
                    <?php
                    $recent_bills = $pdo->query("
                        SELECT b.*, p.full_name as patient_name
                        FROM bills b
                        JOIN patients p ON b.patient_id = p.id
                        ORDER BY b.created_at DESC
                        LIMIT 3
                    ")->fetchAll();
                    ?>
                    <?php if (!empty($recent_bills)): ?>
                        <ul class="activity-list">
                            <?php foreach ($recent_bills as $bill): ?>
                            <li>
                                <strong>$<?= number_format($bill['amount'], 2) ?></strong>
                                <?= htmlspecialchars($bill['patient_name']) ?>
                                <span class="status-badge <?= $bill['status'] ?>">
                                    <?= $bill['status'] ?>
                                </span>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                        <a href="view_bills.php" class="btn small">View All Bills</a>
                    <?php else: ?>
                        <p>No recent billing activity.</p>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </main>
    
    <footer>
        <p>&copy; <?= date('Y') ?> Hospital Management System</p>
    </footer>

    <style>
        /* Dashboard Specific Styles */
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        
        .card {
            padding: 20px;
            border-radius: 8px;
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .profile-card {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        
        .profile-info p {
            margin: 10px 0;
        }
        
        .stats-card {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 15px;
        }
        
        .stat-item {
            text-align: center;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 5px;
        }
        
        .stat-number {
            display: block;
            font-size: 1.5em;
            font-weight: bold;
            color: #2c3e50;
        }
        
        .stat-label {
            font-size: 0.9em;
            color: #7f8c8d;
        }
        
        .actions-card .action-buttons {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 10px;
        }
        
        .activity-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .activity-list li {
            padding: 10px 0;
            border-bottom: 1px solid #eee;
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
            align-items: center;
        }
        
        .activity-list li:last-child {
            border-bottom: none;
        }
        
        .status-badge {
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 0.8em;
            margin-left: auto;
        }
        
        .status-badge.pending,
        .status-badge.unpaid {
            background: #fff3cd;
            color: #856404;
        }
        
        .status-badge.confirmed,
        .status-badge.paid {
            background: #d4edda;
            color: #155724;
        }
        
        .status-badge.cancelled {
            background: #f8d7da;
            color: #721c24;
        }
        
        .header-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }
        
        nav {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }
        
        nav a {
            color: white;
            text-decoration: none;
        }
        
        nav a.active {
            font-weight: bold;
            text-decoration: underline;
        }
    </style>
</body>
</html>