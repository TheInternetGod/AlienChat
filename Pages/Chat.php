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
    
    if (!empty($message)) {
        $message = preg_replace_callback('/\S{16,}/', 'splitLongWords', $message);
        
        $insertMessageQuery = "INSERT INTO Messages (message, sender_id) VALUES (?, ?)";
        $stmt = mysqli_prepare($connection, $insertMessageQuery);
        mysqli_stmt_bind_param($stmt, "si", $message, $_SESSION['userID']);
        
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
</head>
<body class="bg-alien2 bg-cover bg-fixed bg-center text-matrix-green font-mono min-h-screen before:content-[''] before:absolute before:inset-0 before:bg-dark-terminal/70">
    <div class="container mx-auto p-4 relative z-10 flex flex-col h-screen">
        <!-- Header with Flame Animation -->
        <nav class="flex justify-between items-center p-4 border-b border-matrix-green/30 bg-black/20 backdrop-blur">
            <div class="text-4xl md:text-5xl lg:text-6xl font-creepy uppercase text-transparent 
                        bg-flame bg-clip-text [-webkit-background-clip:text] 
                        bg-[length:200%_auto] animate-flame tracking-wider">
                ALIEN CHAT
            </div>
            <div class="flex space-x-6">
                <a href="../logout.php" 
                   class="px-4 py-2 bg-dark-terminal border border-matrix-green/30 
                          hover:bg-matrix-green/20 hover:text-cyber-blue 
                          transition-all duration-300 rounded-lg">
                    LOGOUT
                </a>
            </div>
        </nav>

        <!-- Messages Container -->
        <div class="flex-1 overflow-y-auto my-4 rounded-lg border border-matrix-green/30 bg-black/40 backdrop-blur-sm">
            <div id="chat-messages" class="p-4 space-y-4">
                <?php while ($message = mysqli_fetch_assoc($messages)) : ?>
                    <div class="p-4 border border-matrix-green/30 rounded-lg bg-black/20 backdrop-blur hover:bg-matrix-green/5 transition-all duration-300">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-cyber-blue font-semibold tracking-wide">
                                <?= htmlspecialchars($message['sender'] ?? 'Unknown') ?>
                            </span>
                            <span class="text-xs text-matrix-green/50">
                                <?= $message['time'] ?? '' ?>
                            </span>
                        </div>
                        
                        <?php if (!empty($message['filename'])) : ?>
                            <!-- File Message -->
                            <a href="../uploads/<?= htmlspecialchars($message['filename']) ?>" 
                               class="text-matrix-green hover:text-cyber-blue transition-all duration-300 flex items-center gap-2" 
                               target="_blank">
                                <i class="uil uil-file-alt"></i>
                                <?= htmlspecialchars($message['filename']) ?>
                            </a>
                        <?php else : ?>
                            <!-- Text Message -->
                            <p class="text-matrix-green/90 break-words">
                                <?= htmlspecialchars($message['message']) ?>
                            </p>
                        <?php endif; ?>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>

        <!-- Message Input -->
        <div class="bg-black/20 p-4 rounded-lg border border-matrix-green/30 backdrop-blur">
            <form method="POST" enctype="multipart/form-data" class="flex gap-4" id="messageForm">
                <div class="flex-1 flex items-center gap-4">
                    <input type="text" 
                           name="message" 
                           class="flex-1 bg-dark-terminal/50 text-matrix-green border border-matrix-green/30 rounded px-4 py-2 focus:outline-none focus:border-cyber-blue transition-colors duration-300"
                           placeholder="Type your message..." 
                           autocomplete="off">
                    
                    <label for="fileInput" 
                           class="cursor-pointer bg-matrix-green/20 hover:bg-matrix-green/30 text-matrix-green px-6 py-2 rounded transition-all duration-300 flex items-center gap-2 border border-matrix-green/30">
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
                        class="bg-matrix-green/20 hover:bg-matrix-green/30 text-matrix-green px-6 py-2 rounded transition-all duration-300 flex items-center gap-2 border border-matrix-green/30">
                    <i class="uil uil-message"></i> Send
                </button>
            </form>
            <div id="selectedFile" class="mt-2 text-xs text-matrix-green/70 hidden"></div>
            <div id="uploadStatus" class="mt-2"></div>
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
            <div class="p-3 bg-red-900/20 border border-red-500 text-red-400 rounded flex items-center gap-2">
                <i class="uil uil-exclamation-circle"></i>
                ${message}
            </div>`;
        setTimeout(() => status.innerHTML = '', 3000);
    }

    function showSuccess(message) {
        const status = document.getElementById('uploadStatus');
        status.innerHTML = `
            <div class="p-3 bg-green-900/20 border border-matrix-green text-matrix-green rounded flex items-center gap-2">
                <i class="uil uil-check-circle"></i>
                ${message}
            </div>`;
        setTimeout(() => status.innerHTML = '', 3000);
    }

    async function handleFormSubmit(e) {
        e.preventDefault();
        const form = e.target;
        const formData = new FormData(form);
        
        try {
            // Pre-append message for instant feedback
            const previewData = {
                message: formData.get('message'),
                sender: '<?= $_SESSION['userName'] ?>',
                time: new Date().toLocaleString(),
                filename: formData.get('file')?.name || null
            };
            appendMessage(previewData);
            
            // Clear form immediately
            form.reset();
            document.getElementById('selectedFile').classList.add('hidden');

            // Send to server
            const response = await fetch('../ServerSidePageOperations/SimpleUpload.php', {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            if (result.status !== 'success') {
                throw new Error(result.message);
            }
        } catch(error) {
            console.error('Error:', error);
        }
    }

    function appendMessage(data) {
        const chatMessages = document.getElementById('chat-messages');
        const messageDiv = document.createElement('div');
        messageDiv.className = 'p-4 border border-matrix-green/30 rounded-lg bg-black/20 backdrop-blur hover:bg-matrix-green/5 transition-all duration-300';
        
        messageDiv.innerHTML = `
            <div class="flex justify-between items-center mb-2">
                <span class="text-cyber-blue font-semibold tracking-wide">
                    ${data.sender}
                </span>
                <span class="text-xs text-matrix-green/50">
                    ${data.time}
                </span>
            </div>
            ${data.filename ? 
                `<a href="../uploads/${data.filename}" 
                    class="text-matrix-green hover:text-cyber-blue transition-all duration-300 flex items-center gap-2" 
                    target="_blank">
                    <i class="uil uil-file-alt"></i>${data.filename}
                </a>` : 
                `<p class="text-matrix-green/90 break-words">${data.message}</p>`
            }`;
        
        chatMessages.insertBefore(messageDiv, chatMessages.firstChild);
        chatMessages.scrollTop = 0;
    }

    // Update form event listener
    document.getElementById('messageForm').addEventListener('submit', handleFormSubmit);
    </script>
</body>
</html>
