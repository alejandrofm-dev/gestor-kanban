<?php
class UserRepository
{
    public static function getUserById($id)
    {
        $db = DB::connect();
        $q = "SELECT * FROM users WHERE id=$id";
        $result = $db->query($q);
        $row = $result->fetch_assoc();
        return new User($id, $row['username'], $row['email'], $row['rol']);
    }
}