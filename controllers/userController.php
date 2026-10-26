<?php

// Ver Login
if (isset($_GET['login'])) {
    require_once "views/login.phtml";
    exit();
}

// Ver register
if (isset($_GET['register'])) {
    require_once "views/register.phtml";
    exit();
}
if (isset($_GET["logout"])) {
    session_destroy();
    header("Location: index.php");
    exit();
}

if (isset($_GET["enter"])) {
    if (isset($_POST["username"]) && isset($_POST["password"])) {
        $db = DB::connect();
        $username = $_POST["username"];
        $password = md5($_POST["password"]);
        $q = "SELECT * FROM users WHERE (username='$username' OR email='$username') AND password='$password'";
        $result = $db->query($q);
        if ($row = $result->fetch_assoc()) {
            $_SESSION["user"] = new User($row["id"], $row["username"], $row["email"], $row["rol"]);
            header("Location: index.php");
            exit();
        } else {
            header("Location: index.php?login&error=datosincorrectos");
            exit();
        }
    } else {
        header("Location: index.php?login&error=camposvacios");
        exit();
    }
}

if (isset($_GET["newUser"])) {
    if (isset($_POST["username"]) && isset($_POST["email"]) && isset($_POST["password"]) && isset($_POST["password_confirm"])) {
        $db = DB::connect();
        if ($_POST['password'] == $_POST['password_confirm']) {
            $username = $_POST["username"];
            $email = $_POST["email"];
            $password = md5($_POST["password"]);
            $q = "SELECT * FROM users WHERE username='$username' OR email='$email'";
            $result = $db->query($q);
            if ($result->num_rows > 0) {
                header("Location: index.php?register&error=yaexiste");
                exit();
            }
            $q = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$password')";
            $db->query($q);
            $_SESSION["user"] = new User($db->insert_id, $username, $email, "user");
            header("Location: index.php");
            exit();
        } else {
            header("Location: index.php?register&error=contrasenasdiferentes");
            exit();
        }
    } else {
        header("Location: index.php?register&error=camposvacios");
        exit();
    }
}