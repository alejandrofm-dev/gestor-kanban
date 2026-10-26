<?php
require_once "models/Task.php";
require_once "models/Tag.php";
require_once "models/User.php";
require_once "repositories/TaskRepository.php";
require_once "repositories/TagRepository.php";
require_once "repositories/UserRepository.php";

session_start();

// Acceso a los controladores
if (isset($_GET['c'])) {
    require_once "controllers/" . $_GET["c"] . "Controller.php";
}

// Carga de la vista por defecto.
require_once "views/mainView.phtml";