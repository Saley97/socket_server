<?php

require_once ("BaseClass.class.php");
class User extends BaseClass
{
    private $_name;
    private $_ip;
    private $_pts;
    private $_socket;
    protected $_docFileName = "User.doc.txt";

    public function __construct($ip, $name, $socket, $pts)
    {
        $this->_ip = $ip;
        $this->_name = $name;
        $this->_socket = $socket;
        $this->_pts = $pts;
    }

    public function getIP()
    {
        return $this->_ip;
    }

    public function getName()
    {
        return $this->_name;
    }

    public function getSocket()
    {
        return $this->_socket;
    }

    public function getPts()
    {
        return $this->_pts;
    }
}