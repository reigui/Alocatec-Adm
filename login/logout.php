<?php
require_once 'login.php';
Store::logout();
header("Location: ../index.php");
exit();