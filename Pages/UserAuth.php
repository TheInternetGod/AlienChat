<?php
require '../dbOperations/IninitalizeDB.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_POST['register'])) {
    $username = strtolower(trim($_POST['username']));
    $password = strtolower(trim($_POST['password']));
    $retyped_password = strtolower(trim($_POST['retyped_password']));
    
    if ($password !== $retyped_password) {
        $error = "Passwords do not match!";
    } else {
        $password = crypt($password, "salt");
        $query = "INSERT INTO Users (username, password) VALUES ('$username', '$password')";
        
        if (mysqli_query($connection, $query)) {
            header("Location: ../index.php?registered=true");
            exit();
        } else {
            $error = "Registration failed. Username may already exist.";
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Core Meta Tags -->
    <meta name="description" content="Welcome to Alien Chat Room - Your anonymous messaging platform">
    <meta name="keywords" content="alien chat, messaging, anonymous communication">
    <meta name="author" content="TheGreatOlu">
    
    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="Alien Chatroom">
    <meta property="og:description" content="Alien Chat is an anonymous chatrooom where users can interact wihout revealing their identities.">
    <meta property="og:image" content="https://alienchat.great-site.net/CSS/img1763.jpg">
    <meta property="og:url" content="https://alienchat.great-site.net">
    <meta property="og:type" content="website">
    
    <!-- Twitter Card Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:image" content="https://alienchat.great-site.net/CSS/img1763.jpg">
    
    <!-- Favicon Links -->    
<link rel="icon" type="image/png" sizes="70x70" href="../CSS/favicon-70.png">
<link rel="apple-touch-icon" sizes="144x144" href="../CSS/favicon-144.png">
<link rel="msapplication-TileImage" sizes="150x150" href="../CSS/favicon-150.png">
<link rel="msapplication-square310x310logo" sizes="310x310" href="../CSS/favicon-310.png">
<link rel="shortcut icon" href="../CSS/favicon-70.png">    
    <title>Register - ALIEN CHAT</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'matrix-green': '#00ff00',
                        'dark-terminal': '#0c0c0c',
                        'cyber-blue': '#00ffff'
                    },
                    fontFamily: {
                        'cyber': ['Share Tech Mono', 'monospace']
                    },
                    backgroundImage: {
                        'alien': "url('../CSS/alien.jpg')"
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css">
</head>
<body class="bg-alien bg-cover bg-fixed bg-center text-matrix-green font-cyber min-h-screen before:content-[''] before:absolute before:inset-0 before:bg-dark-terminal/70">
    <div class="container mx-auto p-4 relative z-10">
        <main class="max-w-md mx-auto">
            <section class="space-y-6">
                <div class="bg-black/30 border border-matrix-green/30 rounded-lg p-6 backdrop-blur-sm">
                    <form method="POST" class="space-y-4">
                        <div class="space-y-4">
                            <div class="space-y-2">
                                <label class="block text-sm text-cyber-blue">Username</label>
                                <input type="text" 
                                       name="username" 
                                       required 
                                       class="w-full bg-dark-terminal border border-matrix-green/50 p-2 rounded 
                                              text-matrix-green focus:border-cyber-blue focus:ring-1 
                                              focus:ring-cyber-blue">
                            </div>
                            <div class="space-y-2">
                                <label class="block text-sm text-cyber-blue">Password</label>
                                <input type="password" 
                                       name="password" 
                                       maxlength="15" 
                                       minlength="4" 
                                       required 
                                       class="w-full bg-dark-terminal border border-matrix-green/50 p-2 rounded 
                                              text-matrix-green focus:border-cyber-blue focus:ring-1 
                                              focus:ring-cyber-blue">
                            </div>

                            <div class="space-y-2">
                                <label class="block text-sm text-cyber-blue">Confirm Password</label>
                                <input type="password" 
                                       name="retyped_password" 
                                       maxlength="15" 
                                       minlength="4" 
                                       required 
                                       class="w-full bg-dark-terminal border border-matrix-green/50 p-2 rounded 
                                              text-matrix-green focus:border-cyber-blue focus:ring-1 
                                              focus:ring-cyber-blue">
                            </div>
                        </div>

                        <button type="submit" 
                                name="register" 
                                class="w-full flex items-center justify-center gap-2 py-2 px-4 
                                       bg-dark-terminal border border-matrix-green hover:bg-matrix-green/20 
                                       text-matrix-green rounded transition-all duration-300">
                            Create Account
                            <i class="uil uil-check-circle"></i>
                        </button>
                        
                        <a href="../index.php" 
                           class="block text-center mt-4 text-cyber-blue hover:text-matrix-green transition-colors">
                            Back to Login
                        </a>
                    </form>
                    
                    <?php if(isset($error)): ?>
                        <div class="mt-4 p-3 bg-red-900/20 border border-red-500 text-red-400 rounded">
                            <?php echo $error; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </section>
        </main>
    </div>

    <script>
        function CheckDateForWipeOut() {
            // ...existing code...
        }
    </script>
</body>
</html>
