<?php
session_start();
require '../dbOperations/IninitalizeDB.php';
header('Content-Type: application/json');

try {
    $userId = $_SESSION['userID'] ?? null;
    $username = $_SESSION['userName'] ?? null;
    
    if (!$userId || !$username) {
        throw new Exception("Authentication required");
    }

    $message = trim($_POST['message'] ?? '');
    $hasFile = isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK;

    if (!$hasFile && empty($message)) {
        throw new Exception("Message or file required");
    }

    mysqli_begin_transaction($connection);

    try {
        $fileId = null;
        $fileNameNew = null;

        if ($hasFile) {
            $file = $_FILES['file'];
            
            if ($file['size'] > 1.5 * 1024 * 1024) {
                throw new Exception("File too large");
            }

            $fileExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if (!in_array($fileExt, ['txt', 'jpg'])) {
                throw new Exception("Invalid file type");
            }

            $fileNameNew = uniqid('', true) . ".$fileExt";
            $uploadDir = "../uploads/";
            
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            if (!move_uploaded_file($file['tmp_name'], $uploadDir . $fileNameNew)) {
                throw new Exception("File upload failed");
            }

            $stmt = mysqli_prepare($connection, "INSERT INTO Files (files, users_id) VALUES (?, ?)");
            mysqli_stmt_bind_param($stmt, "si", $fileNameNew, $userId);
            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception(mysqli_error($connection));
            }
            $fileId = mysqli_insert_id($connection);
        }

        $stmt = mysqli_prepare($connection, 
            "INSERT INTO Messages (message, sender_id, file_id) VALUES (?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "sii", $message, $userId, $fileId);
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception(mysqli_error($connection));
        }

        mysqli_commit($connection);

        ob_clean(); // Clear any previous output
        echo json_encode([
            'status' => 'success',
            'message' => $message,
            'sender' => $username,
            'time' => date('Y-m-d H:i:s'),
            'filename' => $fileNameNew
        ]);
        exit;

    } catch (Exception $e) {
        mysqli_rollback($connection);
        throw $e;
    }

} catch (Exception $e) {
    ob_clean(); // Clear any previous output
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
    exit;
}
?>
