<?php
require_once dirname(__DIR__) . '/config.php';
$_SESSION = [];
session_destroy();
header('Location: ' . base_url() . '/admin/login.php');
exit;
