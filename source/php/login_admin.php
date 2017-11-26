<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>AITEC HS2017 - PHP WebChat</title>

        <link rel="stylesheet" type="text/css" href="http://localhost/AITEC/source/js/jScrollPane/jScrollPane.css" />
        <link rel="stylesheet" type="text/css" href="http://localhost/AITEC/source/css/page.css" />
        <link rel="stylesheet" type="text/css" href="http://localhost/AITEC/source/css/chat.css" />

    </head>
    <body>
        <div id="chatContainer">
            <div id="chatTopBar" class="rounded">
                <div id="admin">
                    <form action="http://localhost/AITEC/source/php/logout.php">
                        <input type="submit" class="blueButton" value="Logout">
                    </form>
                </div>
                <h2>Login</h2>
            </div>
            <div id="chatLineHolderAdmin">
                <?php
                require('config.php');
                require('functions.php');
                
                ini_set('display_errors', 1);
                error_reporting(E_ALL ^ E_NOTICE);

                $user = trim($_POST['user']);
                $passwd = trim($_POST['passwd']);

                if(($_SERVER["REQUEST_METHOD"] == "POST") && (!empty($user)) && (!empty($passwd))){
                    if(!$stat = $conn->prepare("SELECT * FROM admin WHERE username=?")){
                        echo "Prepare failed: (" . $conn->errno . ") " . $conn->error;
                    }

                    if(!$stat -> bind_param("s", $user)){
                        echo "Binding parameters failed: (" . $stat->errno . ") " . $stat->error;
                    }

                    if(!$stat->execute()){
                        echo "Execute failed: (" . $stat->errno . ") " . $stat->error;
                    } else {
                        $result = $stat->get_result()->fetch_assoc();
                        $passcode = $result[passcode];
                        $salt = $result[salt];
                        $password = hash('sha512', $passwd . $salt);
                        if($password == $passcode){
                            sec_session_start();
                            $_SESSION['logon'] = 1;
                            header("Refresh: 1; URL=http://localhost/AITEC/source/php/show_users.php");
                            exit;
                        } else {
                            echo "Ungültige Logindaten";
                            header("Refresh: 2; URL=http://localhost/AITEC/source/login_admin.html");
                        }
                    }
                    $stat -> close();
                } else {
                    echo "Bitte geben Sie einen gültigen Benutzernamen und ein gültiges Passwort ein";
                    header("Refresh: 1; URL=http://localhost/AITEC/source/php/login_admin.html");
                }

                $conn->close();
                ?>
            </div>
            <div id="chatBottomBar" class="rounded">
            </div>
        </div>

        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
        <script src="http://localhost/AITEC/source/js/jScrollPane/jquery.mousewheel.js"></script>
        <script src="http://localhost/AITEC/source/js/jScrollPane/jScrollPane.min.js"></script>
        <script src="http://localhost/AITEC/source/js/script.js"></script>
    </body>

</html>
