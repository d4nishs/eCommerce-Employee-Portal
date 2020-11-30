<?php

// eCommerce Employee Portal
// Written by Danish Siddiqui
// Last Updated: 11/27/2020
// logIn.php

// require database connection
// ensure connection works.
require_once('../db-connect.php');
if (!$conn) {
    die("Data Base Connection Error" .mysqli_error());
}

// create a session class
// this class stores all user information
// required for authentication and session storage

class Session
{
    public $name;
    public $email;
    public $signUpDate;
    public $role;
}

class UserSession
{
    public $userEmail;
    public $userPass;

    public function newSession($values)
    {
        session_start();

        $this->$userEmail = $values[0];
        $_SESSION['userEmail'] = $values[0];

        $this->$userPass = $values[1];
        $_SESSION['userPass'] = $values[1];
    }
}

    if (isset($_POST['submit'])) {

        // Create a new session class instance for new log in
        $userEmail = $_POST['email'];
        $userPass = $_POST['pass'];
        $userEmail = rtrim($userEmail);

        $session = new Session();
        $session->$email = $userEmail;

        $userSession = new UserSession();
        $userSession->newSession([$_POST['email'],$_POST['pass']]);

        // Error handlers
        // If values are empty then redirct back

        if (empty($userEmail) || empty($userPass)) {
            header("Location: ../index.php?error=empty", true);
            exit();
        } else {
            // Write query to authenticate user
            $sql = "SELECT * FROM atlaiserUsers WHERE email='$userEmail' AND pass='$userPass'";

            $result = mysqli_query($conn, $sql);
            $rows = mysqli_num_rows($result);

            // if there are no rows corresponding with pass and user
            // redirect back to home page

            if ($rows < 1) {
                header("Location: ../index.php?error=invalidCredentials", true);
                exit();
            } else {
                if ($row = mysqli_fetch_assoc($result)) {
                    if (strcmp($row['pass'], $userPass) == 0 && strcmp($row['email'], $userEmail) == 0) {

                        // Log in the user here
                        $_SESSION['email'] = $row['email'];

                        $session->$name = $row['name'];
                        $_SESSION['name'] = $row['name'];

                        $session->$role = $row['role'];
                        $_SESSION['role'] = $row['role'];

                        $session->$signUpDate = $row['signUpDate'];

                        header("Location: ../dash.php", true);
                        exit();
                    } else {
                        header("Location: ../index.php?error=invalidCredentials", true);
                        exit();
                    }
                }
            }
        }
    }
