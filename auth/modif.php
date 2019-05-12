<?php
    $passFileDir = "../private";
    $passFileName = "passwd";
    $users = [];
    $isNewUser = true;
    if ($_POST["login"] == "" || $_POST["oldpw"] == "" || $_POST["newpw"] == "" || $_POST["oldpw"] == $_POST["newpw"])
    {
        echo "ERROR\n";
        exit();
    }
    if (!file_exists($passFileDir))
    {
        echo "ERROR\n";
        exit();
    }
    if (file_exists($passFileDir.'/'.$passFileName))
        $users = unserialize(file_get_contents($passFileDir.'/'.$passFileName));
    else
    {
        echo "ERROR\n";
        exit();
    }
    foreach ($users as $user)
    {
        if ($user["login"] == $_POST["login"])
        {
            $isNewUser = false;
            $currentUser = $user;
            break;
        }
    }
    if ($isNewUser)
    {
        echo "ERROR\n";
        exit();
    }
    $curUserOldPassHash = hash("gost-crypto", '!Y@o#U$A%r^S&U*C(K) _+'.$_POST["oldpw"].'+F!U#C%K&Y(o_U');
    if ($curUserOldPassHash != $currentUser["passwd"])
    {
        echo "ERROR\n";
        exit();
    }
    $currentUser["passwd"] = hash("gost-crypto", '!Y@o#U$A%r^S&U*C(K) _+'.$_POST["newpw"].'+F!U#C%K&Y(o_U');
    foreach ($users as &$user)
    {
        if ($user["login"] == $_POST["login"])
        {
            $user = $currentUser;
            break;
        }
    }
    file_put_contents($passFileDir.'/'.$passFileName, serialize($users));
    header("Location: index.html");
    echo "OK\n";
?>