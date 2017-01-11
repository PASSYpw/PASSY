<?php
header("Content-Type: text/plain");
include __DIR__ . "/../../include/user.inc.php";
if(!isLoggedIn()) {
    die("not_auth");
}
include __DIR__ . "/../../include/passwords.inc.php";
include __DIR__ . "/../../include/format.inc.php";
$conn = getMYSQL();
$userid = $_SESSION["userid"];
$ps = $conn->prepare("SELECT `EMAIL` FROM `users` WHERE `USERID` = (?)");
$ps->bind_param("s", $userid);
$succeeded = $ps->execute();
$result = $ps->get_result();
$ps->close();
if (!$succeeded) {
    $conn->close();
    die('<tr><td>Error: $ps->error</td><td></td><td></td><td></td><td></td></tr>');
}
if ($result->num_rows > 0)
    $user = $result->fetch_assoc()["EMAIL"];

$ps = $conn->prepare("SELECT `ID`,`USERNAME`,`WEBSITE`,`DATE` FROM `passwords` WHERE `USERID` = (?)");
$ps->bind_param("s", $userid);
$succeeded = $ps->execute();
$result = $ps->get_result();
$ps->close();
$conn->close();
if (!$succeeded)
    die('<tr><td>Error: $ps->error</td><td></td><td></td><td></td><td></td></tr>');
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $dateFormatted = formatTime($row["DATE"]);
        $username = $row["USERNAME"];
        if($username == null || strlen($username) == 0)
            $username = "<i>NONE</i>";
        $website = $row["WEBSITE"];
        $websiteEnabled = true;
        if($website == null || strlen($website) <= 8) {
            $website = "<i>NONE</i>";
            $websiteEnabled = false;
        }
        echo '<tr>';
        echo '<td>' . $username . '</td>';
        echo '<td><a href="#" data-password-id="' . $row["ID"] . '" class="btn btn-default btn-flat btn-block"><i class="material-icons">lock</i></a></td>';
        echo '<td><' . ($websiteEnabled ? 'a href="' . $website . '"' : 'span') . '>' . $website . '</></td>';
        echo '<td>' . $dateFormatted . '</td>';
        echo '<td data-tooltip="' . $user . '">' . $user . '</td>';
        echo '</tr>';
    }
} else {
    echo '<tr><td>Empty</td><td></td><td></td><td></td><td></td></tr>';
}
