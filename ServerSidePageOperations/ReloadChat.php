<?php
require '../dbOperations/IninitalizeDB.php';

$usersIdentification = $_SESSION['userID'];

if ($usersIdentification == "") {
    header("location: ../index.php");
}

// Update query to match table structure
$getMessages = "SELECT message, sender, time 
                FROM Messages 
                ORDER BY time DESC";

$result = mysqli_query($connection, $getMessages);
if (!$result) {
    consolelog("Failed To Get Data From Table Containing Messages!");
}

while ($row = mysqli_fetch_row($result)) {
    $messageFromSender = $row[0];
    $sendersName = $row[1];
    $messageTime = $row[2];

    $messageType = ($_SESSION['userName'] == $sendersName) ? "Messages__self" : "Messages__other";

    print("
        <div class='$messageType'>
            <a class='button__special button--flex'>
                " . ucwords($sendersName) . ": " . $messageFromSender . "
            </a>
        </div>
    ");
}
?>