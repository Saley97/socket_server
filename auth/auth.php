<?php
function auth($login, $passwd)
{
    $passwd = hash("gost-crypto", '!Y@o#U$A%r^S&U*C(K) _+'.$passwd.'+F!U#C%K&Y(o_U');
    $passFileDir = "../private";
    $passFileName = "passwd";
    $users = [];
    if (!file_exists($passFileDir))
    {
        return (false);
    }
    if (file_exists($passFileDir.'/'.$passFileName))
        $users = unserialize(file_get_contents($passFileDir.'/'.$passFileName));
    else
    {
        return (false);
    }
    foreach ($users as $user)
    {
        if ($user["login"] == $login)
            if ($user["passwd"] == $passwd)
            {
                return ($user["pts"]);
            }
            else
            {
                return (false);
            }
    }
    return (false);
}
?>
