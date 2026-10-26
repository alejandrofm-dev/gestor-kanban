<?php
class TagRepository
{
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
}