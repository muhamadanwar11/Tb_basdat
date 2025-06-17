<?php
session_start();
session_destroy();
header("Location: login.php"); // atau index.php jika tidak punya login
exit;
