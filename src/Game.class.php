<?php

require_once("src/BaseClass.class.php");
require_once("src/User.class.php");

class Game extends BaseClass
{
    protected $_docFileName = "Game.doc.txt";

    const GS_WAIT_PLAYERS = 0;
    const GS_GAME_START = 1;
    const GS_ROUND_START = 2;
    const GS_TURN_START = 3;
    const GS_GAME_END = 4;

    private $_currentGameState = null;
    private $_currentRoom = null;
    private $_users = array(null, null, null, null);

    public function __construct(Room $room)
    {
        $this->_currentRoom = $room;
        $this->_currentGameState = self::GS_WAIT_PLAYERS;
    }

    public function addUser(User $new_user)
    {
        foreach ($this->_users as &$user)
            if ($user == null)
            {
                $user = $new_user;
                if ($this->canGameStart())
                    $this->startGame();
                return;
            }
    }

    private function canGameStart()
    {
        if ($this->_currentRoom->getUsersCount() == 4)
            return true;
        return false;
    }

    private function startGame()
    {
        $this->_currentGameState = self::GS_GAME_START;
        $this->_currentRoom->sendMsgToChat("SERVER", "Game has been started! Now choose your fraction");
    }
}