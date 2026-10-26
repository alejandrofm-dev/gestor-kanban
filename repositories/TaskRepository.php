<?php
class TaskRepository
{
    public static function getTasksByUserId(int $userId): array
    {
        $db = DB::connect();
        $q = "SELECT * FROM tasks WHERE user_id=$userId";
        $result = $db->query($q);
        $tasks = [];
        while ($row = $result->fetch_assoc()) {
            $tasks[] = new Task($row['id'], $row['title'], $row['description'], $row['due_date'], $row['priority'], $row['status'], null);
        }
        return $tasks;
    }

    public static function getTaskById(int $id): ?Task
    {
        $db = DB::connect();
        $q = "SELECT * FROM tasks WHERE id=$id";
        $result = $db->query($q);
        if ($result && $row = $result->fetch_assoc()) {
            return new Task((int)$row['id'], $row['title'], $row['description'], $row['due_date'], $row['priority'], $row['status'], null);
        }
        return null;
    }

    public static function getSharedTasksByUserId(int $userId): array
    {
        $db = DB::connect();
        $q = "SELECT t.id, t.title, t.description, t.due_date, t.priority, t.status, st.permission 
            FROM tasks t 
            JOIN shared_tasks st ON t.id = st.task_id 
            WHERE st.user_id=$userId";
        $result = $db->query($q);
        $sharedTasks = [];
        while ($row = $result->fetch_assoc()) {
            $sharedTasks[] = new Task(
                $row['id'],
                $row['title'],
                $row['description'],
                $row['due_date'],
                $row['priority'],
                $row['status'],
                $row['permission']
            );
        }
        return $sharedTasks;
    }

    public static function getCollaboratorsByTaskId(int $taskId): array
    {
        $db = DB::connect();
        $q = "SELECT u.id, u.username, u.email, st.permission, st.shared_at 
              FROM users u 
              JOIN shared_tasks st ON u.id = st.user_id 
              WHERE st.task_id = $taskId";
        $result = $db->query($q);
        $collaborators = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $collaborators[] = $row;
            }
        }
        return $collaborators;
    }

    public static function shareTask(int $taskId, int $userId, string $permission): bool
    {
        $db = DB::connect();
        $permission = in_array($permission, ['read', 'write']) ? $permission : 'read';
        $q = "INSERT INTO shared_tasks (task_id, user_id, permission) 
              VALUES ($taskId, $userId, '$permission') 
              ON DUPLICATE KEY UPDATE permission = '$permission'";
        return (bool) $db->query($q);
    }

    public static function unshareTask(int $taskId, int $userId): bool
    {
        $db = DB::connect();
        $q = "DELETE FROM shared_tasks WHERE task_id=$taskId AND user_id=$userId";
        return (bool) $db->query($q);
    }
}