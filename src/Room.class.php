<?php

require_once ("BaseClass.class.php");
require_once ("User.class.php");
require_once ("Game.class.php");

class Room extends BaseClass
{
    protected $_docFileName = "Room.doc.txt";
    private $_id;
    private $_users = array(null, null, null, null);
    private $_game = null;

    public function __construct($id)
    {
        $this->_id = $id;
        $this->_game = new Game($this);
    }

    public function addUser(User &$new_user)
    {
        foreach ($this->_users as &$user)
            if ($user == null)
            {
                $user = $new_user;
                $this->_game->addUser($new_user);
                return;
            }
    }

    public function isFull()
    {
        foreach ($this->_users as $user)
            if ($user == null)
                return false;
        return true;
    }

    public function getUsersNames()
    {
        $names = array(null, null, null, null);
        foreach ($this->_users as $key => $user)
            if ($user != null)
                $names[$key] = $user->getName();
        return $names;
    }

    public function draw()
    {
        $names = $this->getUsersNames();
        $pts = $this->getUsersPts();
        $res ="<p>".$names[0].
            " (".$pts[0].")".
            "</p>\n<p>". $names[1].
            " (".$pts[1].")".
            "</p>\n<p>".$names[2].
            " (".$pts[2].")".
            "</p>\n<p>".$names[3].
            " (".$pts[3].")".
            "</p>\n";
        return $res;
    }

    public function getUsersSockets()
    {
        $sockets = array(null, null, null, null);
        foreach ($this->_users as $key => $user)
            if ($user != null)
                $sockets[$key] = $user->getSocket();
        return $sockets;
    }

    public function getUsersIPs()
    {
        $ips = array(null, null, null, null);
        foreach ($this->_users as $key => $user)
            if ($user != null)
                $ips[$key] = $user->getIP();
        return $ips;
    }

    public function getUsersPts()
    {
        $pts = array(null, null, null, null);
        foreach ($this->_users as $key => $user)
            if ($user != null)
                $pts[$key] = $user->getPts();
        return $pts;
    }

    public function getUsersCount()
    {
        $count = 0;
        foreach ($this->_users as $user)
            if ($user != null)
                $count++;
        return $count;
    }

    public function sendMsgToChat($msg_owner, $msg_content)
    {
        $chat_box_message = Server::$handler->createChatBoxMessage($msg_owner, $msg_content);
        Server::$handler->sendRoomClient($this, $chat_box_message);
    }
}