<?php
session_start();
require_once 'auth_status.php';
require_once 'db_connect.php';

if (!isLoggedIn()) {
    $_SESSION['redirect_after_login'] = 'book_tickets.php';
    header('Location: login.php');
    exit();
}

$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $visit_date = $_POST['visit_date'];
    $time_slot = $_POST['time_slot'];
    $adult_tickets = (int)$_POST['adult_tickets'];
    $child_tickets = (int)$_POST['child_tickets'];
    
    // Ticket prices
    $adult_price = 25.00;
    $child_price = 15.00;
    
    $total_amount = ($adult_tickets * $adult_price) + ($child_tickets * $child_price);
    
    try {
        $stmt = $pdo->prepare("INSERT INTO ticket_bookings (user_id, visit_date, time_slot, adult_tickets, child_tickets, total_amount) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$_SESSION['user_id'], $visit_date, $time_slot, $adult_tickets, $child_tickets, $total_amount]);
        $success_message = "Your booking has been completed successfully!";
    } catch (PDOException $e) {
        $error_message = "Booking error: " . $e->getMessage();
    }
}

$page_title = 'Book Tickets';
require_once 'includes/header.php';
?>

    <!-- Page Header Start -->
    <div class="container-fluid header-bg py-5 mb-5 wow fadeIn" data-wow-delay="0.1s">
        <div class="container py-5">
            <h1 class="display-4 text-white mb-3 animated slideInDown">Réserver vos Billets</h1>
            <nav aria-label="breadcrumb animated slideInDown">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item text-primary active">Booking</li>
                </ol>
            </nav>
        </div>
    </div>
    <!-- Page Header End -->

    <!-- Booking Form Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-8 wow fadeInUp" data-wow-delay="0.1s">
                    <?php if ($success_message): ?>
                        <div class="alert alert-success"><?php echo htmlspecialchars($success_message); ?></div>
                    <?php endif; ?>
                    
                    <?php if ($error_message): ?>
                        <div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div>
                    <?php endif; ?>

                    <div class="bg-light rounded p-5">
                        <form action="book_tickets.php" method="POST">
                            <div class="row g-4">
                                <div class="col-12">
                                    <h4 class="mb-4">Informations de Visite</h4>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="date" class="form-control bg-white border-0" id="visit_date" name="visit_date" required min="<?php echo date('Y-m-d'); ?>">
                                        <label for="visit_date"><i class="far fa-calendar-alt text-primary me-2"></i>Date de Visite</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <select class="form-select bg-white border-0" id="time_slot" name="time_slot" required>
                                            <option value="">Choisir un créneau</option>
                                            <option value="morning">Matin (9h-12h)</option>
                                            <option value="afternoon">Après-midi (14h-17h)</option>
                                        </select>
                                        <label for="time_slot"><i class="far fa-clock text-primary me-2"></i>Créneau Horaire</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <h4 class="mb-4 mt-3">Tickets</h4>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="number" class="form-control bg-white border-0" id="adult_tickets" name="adult_tickets" min="0" value="0" required>
                                        <label for="adult_tickets"><i class="fas fa-user text-primary me-2"></i>Adult Tickets (25)</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="number" class="form-control bg-white border-0" id="child_tickets" name="child_tickets" min="0" value="0" required>
                                        <label for="child_tickets"><i class="fas fa-child text-primary me-2"></i>Child Tickets (15)</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <button class="btn btn-primary w-100 py-3 mt-4" type="submit">
                                        <i class="fas fa-ticket-alt me-2"></i>Book Now
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-lg-4 wow fadeInUp" data-wow-delay="0.3s">
                    <div class="bg-light rounded p-5">
                        <h4 class="mb-4">Informations Importantes</h4>
                        <div class="mb-4">
                            <h6 class="mb-3"><i class="fa fa-clock text-primary me-2"></i>Heures d'Ouverture</h6>
                            <p>Lundi - Samedi: 9h00 - 18h00<br>Dimanche: Fermé</p>
                        </div>
                        <div class="mb-4">
                            <h6 class="mb-3"><i class="fa fa-ticket-alt text-primary me-2"></i>Tarifs</h6>
                            <p>Adulte: 25€<br>Enfant: 15€</p>
                        </div>
                        <div>
                            <h6 class="mb-3"><i class="fa fa-info-circle text-primary me-2"></i>Note</h6>
                            <p class="mb-0">Tickets are non-exchangeable and non-refundable. Children under 12 must be accompanied by an adult.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Booking Form End -->

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="lib/wow/wow.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/waypoints/waypoints.min.js"></script>
    
    <!-- Template Javascript -->
    <script src="js/main.js"></script>
</body>
</html>
