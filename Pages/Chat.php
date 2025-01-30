<?php
require '../dbOperations/IninitalizeDB.php';

$usersIdentification = $_SESSION['userID'] ?? null;

if (!$usersIdentification) {
    header("Location: ../index.php");
    exit();
}

// Handle message submission
if (isset($_POST['send'])) {
    $message = trim($_POST['message'] ?? '');
    $message = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');
    
    // Handle file upload
    $file_id = null;
    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../uploads/';
        $fileName = basename($_FILES['file']['name']);
        $uploadFilePath = $uploadDir . $fileName;

        // Move the uploaded file to the uploads directory
        if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadFilePath)) {
            // Insert file information into the database
            $insertFileQuery = "INSERT INTO Files (files) VALUES (?)";
            $stmt = mysqli_prepare($connection, $insertFileQuery);
            mysqli_stmt_bind_param($stmt, "s", $fileName);
            if (mysqli_stmt_execute($stmt)) {
                $file_id = mysqli_insert_id($connection);
            } else {
                error_log("File insert failed: " . mysqli_error($connection));
            }
        } else {
            error_log("File upload failed: Could not move file to uploads directory.");
        }
    }

    if (!empty($message) || $file_id !== null) {
        $message = preg_replace_callback('/\S{16,}/', 'splitLongWords', $message);
        
        $insertMessageQuery = "INSERT INTO Messages (message, sender_id, file_id) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($connection, $insertMessageQuery);
        mysqli_stmt_bind_param($stmt, "sii", $message, $_SESSION['userID'], $file_id);
        
        if (mysqli_stmt_execute($stmt)) {
            // Successful insertion
            header("Location: Chat.php");
            exit();
        } else {
            error_log("Message insert failed: " . mysqli_error($connection));
        }
    }
}

// Fetch messages with proper file associations
$getMessages = "SELECT 
                    m.id,
                    COALESCE(m.message, '') AS message,
                    m.time,
                    u.username AS sender,
                    f.files AS filename
                FROM Messages m
                LEFT JOIN Users u ON m.sender_id = u.id
                LEFT JOIN Files f ON m.file_id = f.id
                ORDER BY m.time DESC";

$messages = mysqli_query($connection, $getMessages);

if (!$messages) {
    die("Database error: " . mysqli_error($connection));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
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
    <title>Alien Chat Room</title>
    <link href="https://fonts.googleapis.com/css2?family=Creepster&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                screens: {
                    'sm': '640px',
                    'md': '768px',
                    'lg': '1024px',
                    'xl': '1280px',
                },
                extend: {
                    colors: {
                        'matrix-green': '#00ff00',
                        'dark-terminal': '#0c0c0c',
                        'cyber-blue': '#00ffff',
                        'neon-pink': '#ff00ff'
                    },
                    fontFamily: {
                        'creepy': ['Creepster', 'cursive']
                    },
                    backgroundImage: {
                        'alien2': "url('../CSS/alien2.jpg')",
                        'flame': "url('https://i.gifer.com/K6on.gif')"
                    },
                    keyframes: {
                        'slow-flame': {
                            '0%': { backgroundPosition: '0% center' },
                            '100%': { backgroundPosition: '200% center' }
                        }
                    },
                    animation: {
                        'flame': 'slow-flame 20s linear infinite'
                    }
                }
            }
        }
    </script>
    <style>
        @media (max-width: 640px) {
            .container {
                padding: 0.5rem;
            }
            #chat-messages {
                padding: 0.5rem;
            }
            .message-input {
                flex-direction: column;
            }
            .message-input input {
                width: 100%;
                margin-bottom: 0.5rem;
            }
            .message-input button {
                width: 100%;
            }
        }
    </style>
</head>
<body class="bg-alien2 bg-cover bg-fixed bg-center text-matrix-green font-mono min-h-screen before:content-[''] before:absolute before:inset-0 before:bg-dark-terminal/70">
    <div class="container mx-auto p-4 relative z-10 flex flex-col h-screen">
        <!-- Header with Flame Animation -->
        <nav class="flex justify-between items-center p-4 border-b border-matrix-green/30 bg-black/20 backdrop-blur">
            <div class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl font-creepy uppercase text-transparent 
                        bg-flame bg-clip-text [-webkit-background-clip:text] 
                        bg-[length:200%_auto] animate-flame tracking-wider">
            ALIEN CHAT
            </div>
            <div class="flex space-x-2 sm:space-x-6">
                <a href="../logout.php" 
                   class="px-2 sm:px-4 py-1 sm:py-2 bg-dark-terminal border border-matrix-green/30 
                          hover:bg-matrix-green/20 hover:text-cyber-blue 
                          transition-all duration-300 rounded-lg text-sm sm:text-base">
                    LOGOUT
                </a>
            </div>
        </nav>

        <!-- Messages Container -->
        <div class="flex-1 overflow-y-auto my-4 rounded-lg border border-matrix-green/30 bg-black/40 backdrop-blur-sm">
            <div id="chat-messages" class="p-2 sm:p-4 space-y-2 sm:space-y-4">
                <?php while ($message = mysqli_fetch_assoc($messages)) : ?>
                    <div class="p-2 sm:p-4 border border-matrix-green/30 rounded-lg bg-black/20 backdrop-blur hover:bg-matrix-green/5 transition-all duration-300">
                        <div class="flex justify-between items-center mb-1 sm:mb-2">
                            <span class="text-cyber-blue font-semibold tracking-wide text-sm sm:text-base">
                                <?= htmlspecialchars($message['sender'] ?? 'Unknown') ?>
                            </span>
                            <span class="text-xs text-matrix-green/50">
                                <?= $message['time'] ?? '' ?>
                            </span>
                        </div>
                        
                        <?php if (!empty($message['filename'])) : ?>
                            <!-- File Message -->
                            <a href="../uploads/<?= htmlspecialchars($message['filename']) ?>" 
                               class="text-matrix-green hover:text-cyber-blue transition-all duration-300 flex items-center gap-2 text-sm sm:text-base" 
                               target="_blank">
                                <i class="uil uil-file-alt"></i>
                                <?= htmlspecialchars($message['filename']) ?>
                            </a>
                        <?php else : ?>
                            <!-- Text Message -->
                            <p class="text-matrix-green/90 break-words text-sm sm:text-base">
                                <?= htmlspecialchars($message['message']) ?>
                            </p>
                        <?php endif; ?>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>

        <!-- Message Input -->
        <div class="bg-black/20 p-2 sm:p-4 rounded-lg border border-matrix-green/30 backdrop-blur">
            <form method="POST" enctype="multipart/form-data" class="flex gap-2 sm:gap-4" id="messageForm">
                <div class="flex-1 flex items-center gap-2 sm:gap-4">
                    <input type="text" 
                           name="message" 
                           class="flex-1 bg-dark-terminal/50 text-matrix-green border border-matrix-green/30 rounded px-2 sm:px-4 py-1 sm:py-2 focus:outline-none focus:border-cyber-blue transition-colors duration-300 text-sm sm:text-base"
                           placeholder="Type your message..." 
                           autocomplete="off">
                    
                    <label for="fileInput" 
                           class="cursor-pointer bg-matrix-green/20 hover:bg-matrix-green/30 text-matrix-green px-2 sm:px-6 py-1 sm:py-2 rounded transition-all duration-300 flex items-center gap-2 border border-matrix-green/30 text-sm sm:text-base">
                        <i class="uil uil-image-upload"></i> Media
                        <input type="file" 
                               id="fileInput" 
                               name="file" 
                               class="hidden" 
                               accept=".txt,.jpg"
                               onchange="handleFileSelect(this)">
                    </label>
                </div>

                <button type="submit" 
                        name="send" 
                        class="bg-matrix-green/20 hover:bg-matrix-green/30 text-matrix-green px-2 sm:px-6 py-1 sm:py-2 rounded transition-all duration-300 flex items-center gap-2 border border-matrix-green/30 text-sm sm:text-base">
                    <i class="uil uil-message"></i> Send
                </button>
            </form>
            <div id="selectedFile" class="mt-1 sm:mt-2 text-xs text-matrix-green/70 hidden"></div>
            <div id="uploadStatus" class="mt-1 sm:mt-2"></div>
        </div>
    </div>

    <script>
    function handleFileSelect(input) {
        const selectedFile = document.getElementById('selectedFile');
        if (input.files && input.files[0]) {
            const file = input.files[0];
            if (file.size > 1.5 * 1024 * 1024) {
                showError('File size must be less than 1.5MB');
                input.value = '';
                selectedFile.classList.add('hidden');
                return;
            }
            selectedFile.textContent = `Selected: ${file.name}`;
            selectedFile.classList.remove('hidden');
        }
    }

    function showError(message) {
        const status = document.getElementById('uploadStatus');
        status.innerHTML = `
            <div class="p-2 sm:p-3 bg-red-900/20 border border-red-500 text-red-400 rounded flex items-center gap-2 text-sm sm:text-base">
                <i class="uil uil-exclamation-circle"></i>
                ${message}
            </div>`;
        setTimeout(() => status.innerHTML = '', 3000);
    }

    function showSuccess(message) {
        const status = document.getElementById('uploadStatus');
        status.innerHTML = `
            <div class="p-2 sm:p-3 bg-green-900/20 border border-matrix-green text-matrix-green rounded flex items-center gap-2 text-sm sm:text-base">
                <i class="uil uil-check-circle"></i>
                ${message}
            </div>`;
        setTimeout(() => status.innerHTML = '', 3000);
    }

    // Update form event listener to submit the form normally
    document.getElementById('messageForm').addEventListener('submit', function(e) {
        const fileInput = document.getElementById('fileInput');
        if (fileInput.files && fileInput.files[0] && fileInput.files[0].size > 1.5 * 1024 * 1024) {
            e.preventDefault();
            showError('File size must be less than 1.5MB');
        }
    });
    </script>
</body>
</html>
