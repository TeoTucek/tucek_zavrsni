<?php
session_start();
session_destroy();
header("Location: admin__login.php");
exit();
?>