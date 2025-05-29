<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['user_id']) || !isset($_POST['booking_id'])) {
    header('Location: profile.php');
    exit();
}

try {
    // Verify that the booking belongs to the user
    $stmt = $pdo->prepare("SELECT * FROM ticket_bookings WHERE id = ? AND user_id = ?");
    $stmt->execute([$_POST['booking_id'], $_SESSION['user_id']]);
    
    if ($stmt->rowCount() > 0) {
        // Delete the booking
        $stmt = $pdo->prepare("DELETE FROM ticket_bookings WHERE id = ? AND user_id = ?");
        $stmt->execute([$_POST['booking_id'], $_SESSION['user_id']]);
        $_SESSION['success'] = "Booking cancelled successfully.";
    }
} catch (PDOException $e) {
    $_SESSION['error'] = "Error cancelling booking.";
}

header('Location: profile.php');
exit();
