<?php
class TagRepository
{
    public static function getAllTags(): array
    {
        $db = DB::connect();
        $q = "SELECT * FROM tags";
        $result = $db->query($q);
        $tags = [];
        while ($row = $result->fetch_assoc()) {
            $tags[] = new Tag($row['id'], $row['name'], $row['color']);
        }
        return $tags;
    }

    public static function getTagsByTaskId(int $taskId): array
    {
        $db = DB::connect();
        $q = "SELECT tg.* FROM tags tg JOIN task_tags tt ON tg.id = tt.tag_id WHERE tt.task_id=$taskId";
        $result = $db->query($q);

        $tags = [];
        while ($row = $result->fetch_assoc()) {
            $tags[] = new Tag($row['id'], $row['name'], $row['color']);
        }
        return $tags;
    }

    public static function setTagsForTask(int $taskId, array $tagIds): void
    {
        $db = DB::connect();
        $db->query("DELETE FROM task_tags WHERE task_id = $taskId");
        foreach ($tagIds as $tagId) {
            $tagId = (int)$tagId;
            if ($tagId > 0) {
                $db->query("INSERT IGNORE INTO task_tags (task_id, tag_id) VALUES ($taskId, $tagId)");
            }
        }
    }

    public static function getTagsByIds(array $ids): array
    {
        if (empty($ids)) {
            return [];
        }
        $db = DB::connect();
        $cleanIds = array_map('intval', $ids);
        $idList = implode(',', $cleanIds);
        $q = "SELECT * FROM tags WHERE id IN ($idList)";
        $result = $db->query($q);
        $tagsMap = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $tagsMap[$row['id']] = new Tag($row['id'], $row['name'], $row['color']);
            }
        }
        $orderedTags = [];
        foreach ($cleanIds as $id) {
            if (isset($tagsMap[$id])) {
                $orderedTags[] = $tagsMap[$id];
            }
        }
        return $orderedTags;
    }

    public static function getTagByName(string $name): ?Tag
    {
        $db = DB::connect();
        $nameEscaped = $db->real_escape_string(trim($name));
        $q = "SELECT * FROM tags WHERE name = '$nameEscaped'";
        $result = $db->query($q);
        if ($result && $row = $result->fetch_assoc()) {
            return new Tag((int) $row['id'], $row['name'], $row['color']);
        }
        return null;
    }

    public static function createTag(string $name, string $color = '#6c757d'): ?Tag
    {
        $name = trim($name);
        if (empty($name)) {
            return null;
        }

        $existing = self::getTagByName($name);
        if ($existing) {
            return $existing;
        }

        $db = DB::connect();
        $nameEscaped = $db->real_escape_string($name);
        $colorEscaped = !empty($color) ? $db->real_escape_string($color) : '#6c757d';

        $q = "INSERT INTO tags (name, color) VALUES ('$nameEscaped', '$colorEscaped')";
        $db->query($q);
        if ($db->insert_id) {
            return new Tag((int) $db->insert_id, $name, $colorEscaped);
        }
        return null;
    }
}