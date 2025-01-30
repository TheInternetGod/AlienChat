<?php
require '../dbOperations/IninitalizeDB.php';

// Get earliest timestamps
$getFileCreationTime = "SELECT MIN(`time`) FROM `$dataBaseName`.`Files`";
$getMessageTime = "SELECT MIN(`time`) FROM `$dataBaseName`.`Messages`";
$fileDateTimeResult = mysqli_query($connection, $getFileCreationTime);
$messageDateTimeResult = mysqli_query($connection, $getMessageTime);

// Error handling
if (!$fileDateTimeResult || !$messageDateTimeResult) {
    consolelog("Database query failed: " . mysqli_error($connection));
    return;
}

$fileDateTime = strtotime(mysqli_fetch_array($fileDateTimeResult)[0]);
$messageDateTime = strtotime(mysqli_fetch_array($messageDateTimeResult)[0]);
$dataBaseTime = false;

// Find earliest timestamp
if ($fileDateTime != false) {
    if ($messageDateTime != false) {
        $dataBaseTime = min($messageDateTime, $fileDateTime);
    } else {
        $dataBaseTime = $fileDateTime;
    }
} else {
    $dataBaseTime = $messageDateTime;
}

$timeChecker = time();
$timeDiffirence = $timeChecker - $dataBaseTime;

consolelog("Time difference in seconds: " . $timeDiffirence);

// Check if 3 hours passed (10800 seconds)
if ($timeDiffirence >= 3 * 60 * 60) {
    // Drop and recreate tables
    $dropFilesTable = "DROP TABLE IF NOT EXISTS `$dataBaseName`.`Files`";
    $dropMessagesTable = "DROP TABLE IF NOT EXISTS `$dataBaseName`.`Messages`";
    
    if (!mysqli_query($connection, $dropFilesTable) || !mysqli_query($connection, $dropMessagesTable)) {
        consolelog("Error dropping tables: " . mysqli_error($connection));
        return;
    }

    // Recreate Messages table
    $createMessages = "CREATE TABLE IF NOT EXISTS `Messages` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `sender` VARCHAR(255) NOT NULL,
        `message` TEXT NOT NULL,
        `time` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";

    // Recreate Files table
    $createFiles = "CREATE TABLE IF NOT EXISTS `Files` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `filename` VARCHAR(255) NOT NULL,
        `originalname` VARCHAR(255) NOT NULL,
        `sender` VARCHAR(255) NOT NULL,
        `time` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    if (!mysqli_query($connection, $createMessages) || !mysqli_query($connection, $createFiles)) {
        consolelog("Error creating tables: " . mysqli_error($connection));
        return;
    }

    // Delete files from uploads directory
    $uploadsPath = "../uploads";
    $files = glob($uploadsPath . "/*");
    foreach ($files as $file) {
        if (is_file($file)) {
            if (!unlink($file)) {
                consolelog("Failed to delete: " . $file);
            } else {
                consolelog("Deleted: " . $file);
            }
        }
    }
    
    consolelog("3-hour cleanup completed successfully");
}
?>