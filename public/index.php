<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require '../src/bootstrap.php';


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}