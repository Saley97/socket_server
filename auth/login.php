<?php
    include("auth.php");
    session_start();
    if ($_POST["login"] == "" || $_POST["passwd"] == "" || ($pts = auth($_POST["login"], $_POST["passwd"])) === false)
    {
?>
        <html>
            <body>
                <p>Wrong login or password</p>
                <a href="index.html">Try again</a>
            </body>
        </html>
<?php
    }
    else
    {
        $_SESSION["loggued_on_user"] = $_POST["login"];
        $_SESSION["pts"] = $pts;
?>
        <html>
        <head>
            <script src="http://code.jquery.com/jquery-1.9.1.js"></script>
            <script>
                    $('#frmStart').on("submit", function (event) {
                        location.href = "../game.php";
                    });
                });
            </script>
        </head>
        <body>
        <form id="frmStart" action="../game.php" method="post">
            <input type="submit" name="submit" value="FindGame">
        </form>
        <a href="logout.php">Logout</a>
        </body>
        </html>
<?php
    }
?>