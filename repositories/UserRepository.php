<?php
class UserRepository
{
    public static function getUserById($id): ?User
    {
        $db = DB::connect();
        $q = "SELECT * FROM users WHERE id=$id";
        $result = $db->query($q);
        if ($result && $row = $result->fetch_assoc()) {
            return new User((int)$row['id'], $row['username'], $row['email'], $row['rol']);
        }
        return null;
    }

    public static function getAllUsers(): array
    {
        $db = DB::connect();
        $q = "SELECT * FROM users";
        $result = $db->query($q);
        $users = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $users[] = new User((int)$row['id'], $row['username'], $row['email'], $row['rol']);
            }
        }
        return $users;
    }
}