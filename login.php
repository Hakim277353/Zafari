<?php
session_start();
require_once 'db_connect.php';

// Process login form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Basic validation
    if (empty($username) || empty($password)) {
        $error = "Both username and password are required";
    } else {
        try {
            // Prepare and execute query to check credentials
            $stmt = $pdo->prepare("SELECT id, username, password FROM users WHERE username = ?");
            $stmt->execute([$username]);

            if ($user = $stmt->fetch()) {
                // Verify password
                if (password_verify($password, $user['password'])) {
                    // Set session variables
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['logged_in'] = true;

                    // Redirect to index page
                    header("Location: index.php");
                    exit();
                } else {
                    $error = "Invalid password";
                }
            } else {
                $error = "User not found";
            }
        } catch (PDOException $e) {
            $error = "Login error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Zafari - Connexion</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <!-- Keep existing CSS links -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
    <div class="container-xxl py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-5">
                    <div class="bg-light rounded p-4 p-sm-5">
                        <h1 class="text-center mb-4 wow fadeInUp" data-wow-delay="0.1s">Connexion</h1>

                        <?php if(isset($error)): ?>
                            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                        <?php endif; ?>

                        <?php if(isset($_SESSION['success'])): ?>
                            <div class="alert alert-success"><?php echo htmlspecialchars($_SESSION['success']); ?></div>
                            <?php unset($_SESSION['success']); ?>
                        <?php endif; ?>

                        <form action="login.php" method="POST">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" name="username" id="username"
                                       placeholder="Nom d'utilisateur" required>
                                <label for="username">Nom d'utilisateur</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="password" class="form-control" name="password" id="password"
                                       placeholder="Mot de passe" required>
                                <label for="password">Mot de passe</label>
                            </div>
                            <button type="submit" class="btn btn-primary py-3 w-100 mb-4">Se Connecter</button>
                            <p class="text-center mb-0">Vous n'avez pas de compte? <a href="register.php">S'inscrire</a></p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="lib/wow/wow.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>

    <!-- Template Javascript -->
    <script src="js/main.js"></script>
</body>
</html>