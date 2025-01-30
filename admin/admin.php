<?php
require '../dbOperations/IninitalizeDB.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Admin credentials - stored as hashed password for security
define('ADMIN_USERNAME', 'AlienAdmin2024X');
define('ADMIN_PASSWORD_HASH', password_hash('Xg#K9$mP2@vN5*L8qR3', PASSWORD_BCRYPT));

// Check if admin is logged in
function isAdminLoggedIn() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: admin.php');
    exit();
}

// Handle message deletion
if (isAdminLoggedIn() && isset($_POST['delete_messages'])) {
    // Delete all messages
    $deleteMessages = "TRUNCATE TABLE Messages";
    if (mysqli_query($connection, $deleteMessages)) {
        $successMessage = "All messages have been deleted successfully.";
    } else {
        $errorMessage = "Error deleting messages: " . mysqli_error($connection);
    }
}

// Handle login
if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    if ($username === ADMIN_USERNAME && password_verify($password, ADMIN_PASSWORD_HASH)) {
        $_SESSION['admin_logged_in'] = true;
        header('Location: admin.php');
        exit();
    } else {
        $errorMessage = "Invalid credentials.";
        // Log failed attempt
        error_log("Failed admin login attempt from IP: " . $_SERVER['REMOTE_ADDR']);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Alien Chat</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'matrix-green': '#00ff00',
                        'dark-terminal': '#0c0c0c',
                        'cyber-blue': '#00ffff'
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-dark-terminal text-matrix-green min-h-screen">
    <div class="container mx-auto p-8">
        <?php if (!isAdminLoggedIn()): ?>
            <!-- Login Form -->
            <div class="max-w-md mx-auto bg-black/30 p-8 rounded-lg border border-matrix-green/30">
                <h1 class="text-2xl mb-6 text-center">Admin Login</h1>
                
                <?php if (isset($errorMessage)): ?>
                    <div class="bg-red-900/20 border border-red-500 text-red-400 p-3 mb-4 rounded">
                        <?php echo htmlspecialchars($errorMessage); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" class="space-y-4">
                    <div>
                        <input type="text" 
                               name="username" 
                               required 
                               class="w-full bg-dark-terminal border border-matrix-green/50 p-2 rounded"
                               placeholder="Username">
                    </div>
                    <div>
                        <input type="password" 
                               name="password" 
                               required 
                               class="w-full bg-dark-terminal border border-matrix-green/50 p-2 rounded"
                               placeholder="Password">
                    </div>
                    <button type="submit" 
                            name="login" 
                            class="w-full bg-matrix-green/20 hover:bg-matrix-green/30 border border-matrix-green 
                                   text-matrix-green p-2 rounded">
                        Login
                    </button>
                </form>
            </div>
        <?php else: ?>
            <!-- Admin Dashboard -->
            <div class="max-w-2xl mx-auto bg-black/30 p-8 rounded-lg border border-matrix-green/30">
                <div class="flex justify-between items-center mb-8">
                    <h1 class="text-2xl">Admin Dashboard</h1>
                    <a href="?logout" 
                       class="bg-red-900/20 text-red-400 px-4 py-2 rounded hover:bg-red-900/30 transition-colors">
                        Logout
                    </a>
                </div>

                <?php if (isset($successMessage)): ?>
                    <div class="bg-green-900/20 border border-matrix-green text-matrix-green p-3 mb-4 rounded">
                        <?php echo htmlspecialchars($successMessage); ?>
                    </div>
                <?php endif; ?>

                <div class="space-y-6">
                    <div class="bg-black/40 p-6 rounded-lg border border-matrix-green/20">
                        <h2 class="text-xl mb-4">Message Management</h2>
                        <form method="POST" onsubmit="return confirm('Are you sure you want to delete ALL messages?');">
                            <button type="submit" 
                                    name="delete_messages" 
                                    class="bg-red-900/20 text-red-400 px-4 py-2 rounded hover:bg-red-900/30 transition-colors">
                                Delete All Messages
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>