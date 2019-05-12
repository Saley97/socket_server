<?php
    $passFileDir = "../private";
    $passFileName = "passwd";
    $users = [];
    $isNewUser = true;
    if ($_POST["login"] == "" || $_POST["passwd"] == "")
    {
        echo "ERROR\n";
        exit();
    }
    if (!file_exists($passFileDir))
        mkdir($passFileDir);
    if (file_exists($passFileDir.'/'.$passFileName))
        $users = unserialize(file_get_contents($passFileDir.'/'.$passFileName));
    foreach ($users as $user)
    {
        if ($user["login"] == $_POST["login"])
        {
            $isNewUser = false;
            break;
        }
    }
    if (!$isNewUser)
    {
        echo "ERROR\n";
        exit();
    }
    $users[] = [
        "login" => $_POST["login"],
        "passwd" => hash("gost-crypto", '!Y@o#U$A%r^S&U*C(K) _+'.$_POST["passwd"].'+F!U#C%K&Y(o_U'),
        "pts" => 500
    ];
    file_put_contents($passFileDir.'/'.$passFileName, serialize($users));
    header("Location: index.html");
    echo "OK\n";
?>