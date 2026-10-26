<?php

// Redirigir a login si el usuario no ha iniciado sesión
if (!isset($_SESSION['user'])) {
    header("Location: index.php?c=user&login");
    exit();
}

// Ver new Task
if (isset($_GET['newTask'])) {
    require_once "views/newTask.phtml";
    exit();
}

if (isset($_GET['editTask'])) {
    $taskId = $_GET['editTask'];
    $task = TaskRepository::getTaskById($taskId);
    require_once "views/editTask.phtml";
    exit();
}

// Agregar nueva tarea
if (isset($_GET["addTask"])) {
    if (!empty(trim($_POST['title'] ?? ''))) {
        $db = DB::connect();
        $title = $db->real_escape_string(trim($_POST['title']));
        $description = isset($_POST['description']) ? $db->real_escape_string(trim($_POST['description'])) : '';
        $due_date = !empty($_POST['due_date']) ? "'" . $db->real_escape_string($_POST['due_date']) . "'" : "NULL";
        $priority = in_array($_POST['priority'] ?? '', ['high', 'medium', 'low']) ? $_POST['priority'] : 'medium';
        $userId = $_SESSION['user']->getId();

        $q = "INSERT INTO tasks (title, description, due_date, priority, user_id) VALUES ('$title', '$description', '$due_date', '$priority', '$userId')";
        $db->query($q);

        if ($db->insert_id) {
            $_SESSION['user'] = UserRepository::getUserById($userId);
            header("Location: index.php");
            exit();
        }

        header("Location: index.php?c=task&newTask&error=erroralinsertar");
        exit();
    } else {
        header("Location: index.php?c=task&newTask&error=camposvacios");
        exit();
    }
}

if (isset($_GET['updateTask'])) {
    $db = DB::connect();
    if (isset($_POST['title']) && !empty(trim($_POST['title']))) {
        $taskId = $_POST['id'];
        $title = $db->real_escape_string(trim($_POST['title']));
        $description = isset($_POST['description']) ? $db->real_escape_string(trim($_POST['description'])) : '';
        $due_date = !empty($_POST['due_date']) ? "'" . $db->real_escape_string($_POST['due_date']) . "'" : "NULL";
        $priority = in_array($_POST['priority'] ?? '', ['high', 'medium', 'low']) ? $_POST['priority'] : 'medium';
        $status = in_array($_POST['status'] ?? '', ['pending', 'in_progress', 'completed']) ? $_POST['status'] : 'pending';

        $q = "UPDATE tasks SET title = '$title', description = '$description', due_date = $due_date, priority = '$priority', status = '$status' WHERE id = '$taskId'";
        $db->query($q);
        if ($db->affected_rows >= 0) {
            header("Location: index.php");
            exit();
        }
        header("Location: index.php?c=task&editTask&error=erroralactualizar");
        exit();
    } else {
        header("Location: index.php?c=task&editTask&error=camposvacios");
        exit();
    }
}