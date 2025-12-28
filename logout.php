<?php
require_once 'init.php';

$authController = new AuthController();
$authController->logout();

header('Location: login.php');
exit();
?>
