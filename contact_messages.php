<?php
require '../../auth.php';
require '../../db.php';

if (!isAdmin()) {
    header('Location: /index.php');
    exit();
}

$stmt = $pdo->query("SELECT * FROM contact_messages ORDER BY created_at DESC");
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (isset($_GET['mark_as_read'])) {
    $id = (int)$_GET['mark_as_read'];
    $pdo->prepare("UPDATE contact_messages SET status = 'read' WHERE id = ?")->execute([$id]);
    header('Location: contact_messages.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Contact Messages - Admin</title>
    <link rel="stylesheet" href="../../style.css">
</head>
<body>
    <?php include '../header.php'; ?>
    
    <main class="container">
        <h2>Contact Messages</h2>
        
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Subject</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($messages as $message): ?>
                <tr class="<?= $message['status'] === 'unread' ? 'unread' : '' ?>">
                    <td><?= $message['id'] ?></td>
                    <td><?= htmlspecialchars($message['name']) ?></td>
                    <td><?= htmlspecialchars($message['email']) ?></td>
                    <td><?= htmlspecialchars($message['subject']) ?></td>
                    <td><?= date('M j, Y g:i a', strtotime($message['created_at'])) ?></td>
                    <td><?= ucfirst($message['status']) ?></td>
                    <td>
                        <a href="view_message.php?id=<?= $message['id'] ?>" class="btn small">View</a>
                        <?php if ($message['status'] === 'unread'): ?>
                            <a href="?mark_as_read=<?= $message['id'] ?>" class="btn small">Mark as Read</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </main>
    
    <?php include '../footer.php'; ?>
</body>
</html>