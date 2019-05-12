<?php

require_once ("src/BaseClass.class.php");
require_once ("MainSocketHandler.class.php");
require_once ("src/User.class.php");
require_once ("src/Room.class.php");

class Server extends BaseClass
{
    protected $_docFileName = "Server.doc.txt";

    static $rooms = array();
    static $currentUsers = array();
    static $handler;
    static $currentRoom = null;
    static $instance = null;

    public function __construct()
    {
        if (self::$instance != null)
            return;
        self::$instance = $this;
        self::$handler = new MainSocketHandler($this);
    }

    public function addUser($ip, $name, $userSocket, $pts)
    {
        if ($this->findUsersByIP($ip) == false
            || $this->findUsersByName($name) == false)
        {
            self::$currentUsers[] = new User($ip, $name, $userSocket, $pts);

            if (self::$currentRoom == null)
                $this->addRoom();
            elseif (self::$currentRoom->isFull())
            {
                self::$currentRoom = null;
                $this->addRoom();
            }

            self::$currentRoom->addUser(self::$currentUsers[count(self::$currentUsers) - 1]);
        }
    }

    public function findUsersByIP($ip)
    {
        $res = array();
        foreach (self::$currentUsers as $usr)
        {
            if ($usr->getIP() == $ip)
                $res[] = $usr;
        }
        if (count($res) > 0)
            return $res;
        return false;
    }

    public function findUsersByName($name)
    {
        $res = array();
        foreach (self::$currentUsers as $usr)
        {
            if ($usr->getName() == $name)
                $res[] = $usr;
        }
        if (count($res) > 0)
            return $res;
        return false;
    }

    public function addRoom()
    {
        if (self::$currentRoom == null)
        {
            $id = count(self::$rooms);
            self::$rooms[] = new Room($id);
            self::$currentRoom = self::$rooms[$id];
        }
    }

    public function getRoomByUserName($name)
    {
        foreach (self::$rooms as &$room)
        {
            $usersNames = $room->getUsersNames();
            foreach ($usersNames as $userName)
                if ($userName == $name)
                    return $room;
        }
        return false;
    }
}