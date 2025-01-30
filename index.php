<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$_SESSION['userName'] = "";
$_SESSION['userID'] = "";

require 'dbOperations/IninitalizeDB.php';

// Handle Registration
if (isset($_POST['register'])) {
    $username = strtolower(trim($_POST['name']));
    $password = strtolower(trim($_POST['password']));
    $password = crypt($password, "salt");
    
    $query = "INSERT INTO Users (username, password) VALUES ('$username', '$password')";
    if (mysqli_query($connection, $query)) {
        $success = "Registration successful! Please login.";
    } else {
        $error = "Registration failed. Username may already exist.";
    }
}

// Handle Login
if (isset($_POST['login'])) {
    $userSubmittedName = strtolower(trim($_POST['name']));
    $verifyPassword = strtolower(trim($_POST['password']));
    $verifyPassword = crypt($verifyPassword, "salt");

    $selectUserQuery = "SELECT `id`,`username`,`password` FROM `Users`";
    $result = mysqli_query($connection, $selectUserQuery);
    
    if (!$result) {
        $error = "Login failed. Please try again.";
    } else {
        $loginSuccess = false;
        while ($row = mysqli_fetch_row($result)) {
            if ($row[1] === $userSubmittedName && $row[2] === $verifyPassword) {
                $_SESSION['userName'] = $row[1];
                $_SESSION['userID'] = $row[0];
                $loginSuccess = true;
                header("Location: Pages/Chat.php");
                exit();
            }
        }
        if (!$loginSuccess) {
            $error = "Invalid username or password.";
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
<link rel="icon" type="image/png" sizes="70x70" href="/CSS/favicon-70.png">
<link rel="apple-touch-icon" sizes="144x144" href="/CSS/favicon-144.png">
<link rel="msapplication-TileImage" sizes="150x150" href="/CSS/favicon-150.png">
<link rel="msapplication-square310x310logo" sizes="310x310" href="/CSS/favicon-310.png">
<link rel="shortcut icon" href="/CSS/favicon-70.png">
    <title>ALIEN CHAT</title>
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
                        'alien': "url('CSS/alien.jpg')" // Fix: Remove '../' as index.php is in root
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-alien bg-cover bg-fixed bg-center text-matrix-green font-cyber min-h-screen before:content-[''] before:absolute before:inset-0 before:bg-dark-terminal/70">
    <div class="container mx-auto p-4 relative z-10">
        <div class="max-w-md mx-auto bg-dark-gray rounded-lg shadow-lg p-6 border border-terminal-green">
            <h2 class="text-2xl font-mono text-terminal-green mb-6 text-center">Alien Chat</h2>
            
            <form method="POST" class="space-y-4">
                <div>
                    <input type="text" 
                           name="name" 
                           class="w-full bg-dark-terminal border border-matrix-green/50 p-2 rounded text-matrix-green focus:border-cyber-blue focus:ring-1 focus:ring-cyber-blue"
                           placeholder="Username" 
                           required>
                </div>
                <div>
                    <input type="password" 
                           name="password" 
                           class="w-full bg-dark-terminal border border-matrix-green/50 p-2 rounded text-matrix-green focus:border-cyber-blue focus:ring-1 focus:ring-cyber-blue"
                           placeholder="Password" 
                           required>
                </div>
                <div class="flex space-x-4">
                    <button type="submit" 
                            name="login" 
                            class="w-1/2 bg-dark-terminal border border-matrix-green hover:bg-matrix-green/20 
                                   text-matrix-green p-2 rounded-md transition-all duration-300">
                        Login
                    </button>
                    <a href="Pages/UserAuth.php" 
                       class="w-1/2 bg-dark-terminal border border-matrix-green hover:bg-matrix-green/20 
                              text-matrix-green p-2 rounded-md transition-all duration-300 
                              text-center inline-flex items-center justify-center">
                        Register
                    </a>
                </div>
            </form>
            
            <?php if(isset($error)): ?>
                <div class="mt-4 p-3 bg-red-900/20 border border-red-500 text-red-400 rounded">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <?php if(isset($success)): ?>
                <div class="mt-4 p-3 bg-green-900/20 border border-matrix-green text-matrix-green rounded">
                    <?php echo $success; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
