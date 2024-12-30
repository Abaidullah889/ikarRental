<?php
session_start();
include_once("userstorage.php");
include_once("utils.php");
include_once("auth.php");

session_start();

$auth = new Auth(new UserStorage());
$auth->logout();
redirect("login.php");