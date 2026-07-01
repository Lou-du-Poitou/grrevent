<?php
require_once './class/utils/Session.php';

Session::clear();

header('Location: connexion.php');
exit();
