<?php
require_once __DIR__ . '/auth.php';
require_admin();

$_SESSION = [];
session_destroy();
redirect('login.php');
