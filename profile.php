<?php
session_start();
require_once 'db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

try {
    // Get user information
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();

    // Get user reservations
    $stmt = $pdo->prepare("SELECT * FROM ticket_bookings WHERE user_id = ? ORDER BY booking_date DESC");
    $stmt->execute([$_SESSION['user_id']]);
    $reservations = $stmt->fetchAll();

    // Handle profile update form
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $new_password = trim($_POST['new_password']);

        if (!empty($new_password)) {
            $password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ?, password = ? WHERE id = ?");
            $stmt->execute([$username, $email, $password, $_SESSION['user_id']]);
        } else {
            $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
            $stmt->execute([$username, $email, $_SESSION['user_id']]);
        }
        
        $success = "Profile updated successfully!";
        
        // Refresh user data
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch();
    }
} catch (PDOException $e) {
    $error = "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Mon Profil - Zafari</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    
    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500&family=Quicksand:wght@600;700&display=swap" rel="stylesheet">

    <!-- CSS Libraries -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="lib/animate/animate.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Page Header Start -->
    <div class="container-fluid header-bg py-5 mb-5 wow fadeIn" data-wow-delay="0.1s">
        <div class="container py-5">
            <h1 class="display-4 text-white mb-3 animated slideInDown">Mon Profil</h1>
            <nav aria-label="breadcrumb animated slideInDown">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item text-primary active">Profile</li>
                </ol>
            </nav>
        </div>
    </div>
    <!-- Page Header End -->

    <!-- Profile Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="row g-4">
                <!-- Profile Information -->
                <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.1s">
                    <div class="bg-light rounded p-4">
                        <h3 class="mb-4">Profile Information</h3>
                        <?php if(isset($success)): ?>
                            <div class="alert alert-success"><?php echo $success; ?></div>
                        <?php endif; ?>
                        <?php if(isset($error)): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>
                        
                        <form action="profile.php" method="POST">
                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" id="username" name="username" 
                                               value="<?php echo htmlspecialchars($user['username']); ?>" required>
                                        <label for="username">Username</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-floating">
                                        <input type="email" class="form-control" id="email" name="email" 
                                               value="<?php echo htmlspecialchars($user['email']); ?>" required>
                                        <label for="email">Email</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-floating">
                                        <input type="password" class="form-control" id="new_password" name="new_password">
                                        <label for="new_password">New Password (leave empty to keep current)</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <button class="btn btn-primary w-100 py-3" type="submit" name="update_profile">
                                        Update Profile
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Reservations -->
                <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.3s">
                    <div class="bg-light rounded p-4">
                        <h4 class="mb-4">Mes Réservations</h4>
                        <?php if(empty($reservations)): ?>
                            <p>You have no bookings.</p>
                        <?php else: ?>
                            <?php foreach($reservations as $reservation): ?>
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <h5 class="card-title">Booking #<?php echo $reservation['id']; ?></h5>
                                        <p class="card-text">
                                            Visit Date: <?php echo date('m/d/Y', strtotime($reservation['visit_date'])); ?><br>
                                            Time Slot: <?php echo $reservation['time_slot']; ?><br>
                                            Adult Tickets: <?php echo $reservation['adult_tickets']; ?><br>
                                            Child Tickets: <?php echo $reservation['child_tickets']; ?><br>
                                            Total Amount: $<?php echo number_format($reservation['total_amount'], 2); ?>
                                        </p>
                                        <form action="cancel_booking.php" method="POST" style="display: inline;">
                                            <input type="hidden" name="booking_id" value="<?php echo $reservation['id']; ?>">
                                            <button type="submit" class="btn btn-danger btn-sm">Annuler</button>
                                        </form>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Profile End -->

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="lib/wow/wow.min.js"></script>
    <script src="js/main.js"></script>
</body>
</html>
