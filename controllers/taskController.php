<?php

// Redirigir a login si el usuario no ha iniciado sesión
if (!isset($_SESSION['user'])) {
    header("Location: index.php?c=user&login");
    exit();
}

// Ver new Task
if (isset($_GET['newTask'])) {
    $title = '';
    $description = '';
    $due_date = '';
    $priority = '';
    $selectedTags = [];
    require_once "views/newTask.phtml";
    exit();
}

if (isset($_GET['editTask'])) {
    $taskId = (int) $_GET['editTask'];
    $task = TaskRepository::getTaskById($taskId);
    $selectedTags = $task->getTags();
    require_once "views/editTask.phtml";
    exit();
}

// Agregar nueva tarea
if (isset($_GET["addTask"])) {
    // Si se pulsó Añadir Tag, Crear Tag o Quitar Tag
    if (isset($_POST['addTag']) || isset($_POST['removeTag']) || isset($_POST['createTag'])) {
        $tagIds = isset($_POST['tags']) && is_array($_POST['tags']) ? array_map('intval', $_POST['tags']) : [];

        if (isset($_POST['addTag']) && !empty($_POST['selected_tag'])) {
            $tagToAdd = (int) $_POST['selected_tag'];
            if (!in_array($tagToAdd, $tagIds)) {
                $tagIds[] = $tagToAdd;
            }
        } elseif (isset($_POST['createTag']) && !empty(trim($_POST['new_tag_name'] ?? ''))) {
            $tagName = trim($_POST['new_tag_name']);
            $tagColor = !empty($_POST['new_tag_color']) ? $_POST['new_tag_color'] : '#6c757d';
            $newTag = TagRepository::createTag($tagName, $tagColor);
            if ($newTag && !in_array($newTag->getId(), $tagIds)) {
                $tagIds[] = $newTag->getId();
            }
        } elseif (isset($_POST['removeTag'])) {
            $tagToRemove = (int) $_POST['removeTag'];
            $tagIds = array_values(array_filter($tagIds, fn($id) => $id !== $tagToRemove));
        }

        $selectedTags = TagRepository::getTagsByIds($tagIds);

        // Mantener valores que el usuario ya había escrito
        $title = $_POST['title'] ?? '';
        $description = $_POST['description'] ?? '';
        $due_date = $_POST['due_date'] ?? '';
        $priority = $_POST['priority'] ?? '';

        require_once "views/newTask.phtml";
        exit();
    }

    if (!empty(trim($_POST['title'] ?? ''))) {
        $tagIds = isset($_POST['tags']) && is_array($_POST['tags']) ? array_map('intval', $_POST['tags']) : [];
        if (!empty(trim($_POST['new_tag_name'] ?? ''))) {
            $newTag = TagRepository::createTag(trim($_POST['new_tag_name']), $_POST['new_tag_color'] ?? '#6c757d');
            if ($newTag && !in_array($newTag->getId(), $tagIds)) {
                $tagIds[] = $newTag->getId();
            }
        }

        $db = DB::connect();
        $title = $db->real_escape_string(trim($_POST['title']));
        $description = isset($_POST['description']) ? $db->real_escape_string(trim($_POST['description'])) : '';
        $due_date = !empty($_POST['due_date']) ? "'" . $db->real_escape_string($_POST['due_date']) . "'" : "NULL";
        $priority = in_array($_POST['priority'] ?? '', ['high', 'medium', 'low']) ? $_POST['priority'] : 'medium';
        $userId = $_SESSION['user']->getId();

        $q = "INSERT INTO tasks (title, description, due_date, priority, user_id) VALUES ('$title', '$description', $due_date, '$priority', '$userId')";
        $db->query($q);

        if ($db->insert_id) {
            $newTaskId = $db->insert_id;
            TagRepository::setTagsForTask($newTaskId, $tagIds);
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
    $taskId = (int) ($_POST['id'] ?? 0);

    // Si se pulsó Añadir Tag, Crear Tag o Quitar Tag
    if (isset($_POST['addTag']) || isset($_POST['removeTag']) || isset($_POST['createTag'])) {
        $tagIds = isset($_POST['tags']) && is_array($_POST['tags']) ? array_map('intval', $_POST['tags']) : [];

        if (isset($_POST['addTag']) && !empty($_POST['selected_tag'])) {
            $tagToAdd = (int) $_POST['selected_tag'];
            if (!in_array($tagToAdd, $tagIds)) {
                $tagIds[] = $tagToAdd;
            }
        } elseif (isset($_POST['createTag']) && !empty(trim($_POST['new_tag_name'] ?? ''))) {
            $tagName = trim($_POST['new_tag_name']);
            $tagColor = !empty($_POST['new_tag_color']) ? $_POST['new_tag_color'] : '#6c757d';
            $newTag = TagRepository::createTag($tagName, $tagColor);
            if ($newTag && !in_array($newTag->getId(), $tagIds)) {
                $tagIds[] = $newTag->getId();
            }
        } elseif (isset($_POST['removeTag'])) {
            $tagToRemove = (int) $_POST['removeTag'];
            $tagIds = array_values(array_filter($tagIds, fn($id) => $id !== $tagToRemove));
        }

        $selectedTags = TagRepository::getTagsByIds($tagIds);

        // Mantener valores modificados en el formulario
        $task = new Task(
            $taskId,
            $_POST['title'] ?? '',
            $_POST['description'] ?? '',
            !empty($_POST['due_date']) ? $_POST['due_date'] : null,
            $_POST['priority'] ?? 'medium',
            $_POST['status'] ?? 'pending'
        );
        $task->setTags($selectedTags);

        require_once "views/editTask.phtml";
        exit();
    }

    if (isset($_POST['title']) && !empty(trim($_POST['title']))) {
        $tagIds = isset($_POST['tags']) && is_array($_POST['tags']) ? array_map('intval', $_POST['tags']) : [];
        if (!empty(trim($_POST['new_tag_name'] ?? ''))) {
            $newTag = TagRepository::createTag(trim($_POST['new_tag_name']), $_POST['new_tag_color'] ?? '#6c757d');
            if ($newTag && !in_array($newTag->getId(), $tagIds)) {
                $tagIds[] = $newTag->getId();
            }
        }

        $title = $db->real_escape_string(trim($_POST['title']));
        $description = isset($_POST['description']) ? $db->real_escape_string(trim($_POST['description'])) : '';
        $due_date = !empty($_POST['due_date']) ? "'" . $db->real_escape_string($_POST['due_date']) . "'" : "NULL";
        $priority = in_array($_POST['priority'] ?? '', ['high', 'medium', 'low']) ? $_POST['priority'] : 'medium';
        $status = in_array($_POST['status'] ?? '', ['pending', 'in_progress', 'completed']) ? $_POST['status'] : 'pending';

        $q = "UPDATE tasks SET title = '$title', description = '$description', due_date = $due_date, priority = '$priority', status = '$status' WHERE id = '$taskId'";
        $db->query($q);
        if ($db->affected_rows >= 0) {
            TagRepository::setTagsForTask($taskId, $tagIds);
            $_SESSION['user'] = UserRepository::getUserById($_SESSION['user']->getId());
            header("Location: index.php");
            exit();
        }
        header("Location: index.php?c=task&editTask=" . $taskId . "&error=erroralactualizar");
        exit();
    } else {
        header("Location: index.php?c=task&editTask=" . $taskId . "&error=camposvacios");
        exit();
    }
}