<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$currentDate = date_create()->format('Y-m-d H:i:s');
$dataBaseName = 'chat_app';


if (!file_exists('../uploads')) {
    mkdir('../uploads', 0777, true);
    consolelog("Created uploads directory");
} else {
    consolelog("uploads directory already exists");
}

function println(string $string = '')
{
    print($string . PHP_EOL);
}

function consolelog($data)
{
    echo "<script>console.log('" . $data . "')</script>";
}

function importNavName(){
    print("<a href='#' class='nav__logo'>" . ucwords($_SESSION['userName']) . "</a>");
}

function printOutMessages($result, $usersIdentification){
    while ($row = mysqli_fetch_row($result)) {
        $messageFromSender = $row[0] ?? '';
        $sendersName = $row[1] ?? '';
        $messageTime = $row[2] ?? '';

        // Compare sender name instead of ID
        $messageType = ($_SESSION['userName'] == $sendersName) ? 
                      "Messages__self" : "Messages__other";

        print("
            <div class='$messageType'>
                <a class='button__special button--flex'>
                    " . ucwords($sendersName) . ": " . $messageFromSender . "
                </a>
            </div>
        ");
    }
}

function splitLongWords($matches) {
    $word = $matches[0];
    if (strlen($word) > 20) {
        $split_word = wordwrap($word, 20, " ", true);
        return $split_word;
    } else {
        return $word;
    }
}

function deleteDirectory($dir)
{
    if (!file_exists($dir)) {
        return true;
    }

    if (!is_dir($dir)) {
        return unlink($dir);
    }

    foreach (scandir($dir) as $item) {
        if ($item == '.' || $item == '..') {
            continue;
        }

        if (!deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
            return false;
        }
    }

    return rmdir($dir);
}

?>