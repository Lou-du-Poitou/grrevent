<?php
require_once __DIR__ . '/../class/utils/Session.php';

Session::clear();

header('Location: connexion.php');
exit();
