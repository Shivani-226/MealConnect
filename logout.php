<?php
session_start();
session_destroy();
header("Location: admin_login.html");
header("Location: available_recipients.php");
exit;
?>