<?php
session_start();
require_once 'db_connect.php';

// Verify admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit();
}

// Get statistics
$stmt = $pdo->query("SELECT 
    COUNT(*) as total_bookings,
    SUM(adult_tickets) as total_adult_tickets,
    SUM(child_tickets) as total_child_tickets,
    SUM(total_amount) as total_revenue
    FROM ticket_bookings");
$stats = $stmt->fetch();

// Get recent bookings
$stmt = $pdo->query("SELECT tb.*, u.username 
    FROM ticket_bookings tb 
    JOIN users u ON tb.user_id = u.id 
    ORDER BY booking_date DESC 
    LIMIT 10");
$recent_bookings = $stmt->fetchAll();

// Get users list
$stmt = $pdo->query("SELECT * FROM users ORDER BY created_at DESC");
$users = $stmt->fetchAll();

// Handle user deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_user'])) {
    $user_id = $_POST['user_id'];
    try {
        $pdo->beginTransaction();
        $pdo->exec("DELETE FROM ticket_bookings WHERE user_id = $user_id");
        $pdo->exec("DELETE FROM users WHERE id = $user_id");
        $pdo->commit();
        $success = "User deleted successfully";
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = "Error deleting user";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard - Zafari</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid">
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
            <div class="container">
                <a class="navbar-brand" href="#">Zafari Admin</a>
                <div class="navbar-nav ms-auto">
                    <a href="index.php" class="nav-link me-3">Website Home</a>
                    <a href="admin_dashboard.php" class="nav-link me-3">Admin Dashboard</a>
                    <a href="admin_logout.php" class="nav-link">Logout</a>
                </div>
            </div>
        </nav>

        <?php if(isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        <?php if(isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-3 mb-4">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h5>Total Bookings</h5>
                        <h2><?php echo $stats['total_bookings']; ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h5>Total Revenue</h5>
                        <h2><?php echo number_format($stats['total_revenue'], 3); ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <h5>Adult Tickets</h5>
                        <h2><?php echo $stats['total_adult_tickets']; ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <h5>Child Tickets</h5>
                        <h2><?php echo $stats['total_child_tickets']; ?></h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5>Recent Bookings</h5>
                    </div>
                    <div class="card-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>User</th>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Tickets</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_bookings as $booking): ?>
                                <tr>
                                    <td><?php echo $booking['id']; ?></td>
                                    <td><?php echo htmlspecialchars($booking['username']); ?></td>
                                    <td><?php echo $booking['visit_date']; ?></td>
                                    <td><?php echo $booking['time_slot']; ?></td>
                                    <td>A:<?php echo $booking['adult_tickets']; ?> C:<?php echo $booking['child_tickets']; ?></td>
                                    <td>$<?php echo number_format($booking['total_amount'], 2); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5>User Management</h5>
                    </div>
                    <div class="card-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Username</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                                    <td>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                            <button type="submit" name="delete_user" class="btn btn-danger btn-sm" 
                                                    onclick="return confirm('Are you sure you want to delete this user?')">
                                                Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
