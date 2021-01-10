<?php

session_start();


// Required functions
require_once('config.php');
require_once('includes/functions.php');

// Current date & user IP
$date = date('jS F Y');
$ip   = $_SERVER['REMOTE_ADDR'];

// Database Connection
$con = mysqli_connect($dbhost, $dbuser, $dbpassword, $dbname);
if (mysqli_connect_errno()) {
    die("Unable to connect to database");
}
// Get site info
$query  = "SELECT * FROM site_info";
$result = mysqli_query($con, $query);

while ($row = mysqli_fetch_array($result)) {
    $title				= Trim($row['title']);
    $des				= Trim($row['des']);
    $baseurl			= Trim($row['baseurl']);
    $keyword			= Trim($row['keyword']);
    $site_name			= Trim($row['site_name']);
    $email				= Trim($row['email']);
    $twit				= Trim($row['twit']);
    $face				= Trim($row['face']);
    $gplus				= Trim($row['gplus']);
    $ga					= Trim($row['ga']);
    $additional_scripts	= Trim($row['additional_scripts']);
}

// Set theme and language
$query  = "SELECT * FROM interface";
$result = mysqli_query($con, $query);

while ($row = mysqli_fetch_array($result)) {
    $default_lang  = Trim($row['lang']);
    $default_theme = Trim($row['theme']);
}

require_once("langs/$default_lang");

// Page title
$p_title = $lang['login/register']; // "Login/Register";

if (isset($_GET['new_user'])) {
    $new_user = 1;
}

$username = $_SESSION['username'];

// POST Handler
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['user_change'])) {
        $new_username = htmlentities(Trim($_POST['new_username']));
        if ($new_username == "" || $new_username == null) {
            $error = $lang['usernotvalid']; //"Username not vaild";
        } else {
            $res = isValidUsername($new_username);
            if ($res == '1') {
                $query  = "SELECT * FROM users WHERE username='$new_username'";
                $result = mysqli_query($con, $query);
                if (mysqli_num_rows($result) > 0) {
                    $error = $lang['userexists']; //"Username already taken";
                } else {
                    $client_id = Trim($_SESSION['oauth_uid']);
                    $query     = "UPDATE users SET username='$new_username' WHERE oauth_uid='$client_id'";
                    mysqli_query($con, $query);
                    if (mysqli_error($con)) {
                        $error = $lang['databaseerror']; // "Unable to access database.";
                    } else {
                        $success = $lang['userchanged']; //"Username changed successfully";
                        unset($_SESSION['username']);
                        $_SESSION['username'] = $new_username;
                    }
                }
            } else {
                $error    = $lang['usernotvalid']; //"Username not vaild";
                $username = Trim($_SESSION['username']);
                goto OutPut;
            }
        }
    }
}

OutPut:
// Theme
require_once('theme/' . $default_theme . '/header.php');
require_once('theme/' . $default_theme . '/oauth.php');
require_once('theme/' . $default_theme . '/footer.php');
?>