<?php

require_once("src/BaseClass.class.php");

class MainSocketHandler extends BaseClass {

    protected $_docFileName = "MainHandler.doc.txt";
//    public $server;
//
//    public function __construct(Server &$server)
//    {
//        $this->server = $server;
//    }

    function send($message) /**  Отправляем сообщение каждому клиенту сокета*/
    {
		global $clientSocketArray;
		$messageLength = strlen($message);
		foreach($clientSocketArray as $clientSocket)
		{
			@socket_write($clientSocket,$message,$messageLength);
		}
		return true;
	}

    function sendRoomClient(Room $room, $message) /**  Отправляем сообщение каждому клиенту комнаты*/
    {
        $sockets = $room->getUsersSockets();
        $messageLength = strlen($message);
        foreach ($sockets as $socket)
        {
            if ($socket != null)
                socket_write($socket, $message, $messageLength);
        }
    }

	function unseal($socketData) /** Декодируем сообщение полученное из сокета */
    {
		$length = ord($socketData[1]) & 127;
		if($length == 126) {
			$masks = substr($socketData, 4, 4);
			$data = substr($socketData, 8);
		}
		elseif($length == 127) {
			$masks = substr($socketData, 10, 4);
			$data = substr($socketData, 14);
		}
		else {
			$masks = substr($socketData, 2, 4);
			$data = substr($socketData, 6);
		}
		$socketData = "";
		for ($i = 0; $i < strlen($data); ++$i) {
			$socketData .= $data[$i] ^ $masks[$i%4];
		}
		return $socketData;
	}

	function seal($socketData) /** Кодируем сообщение для отправки в сокет */
    {
		$b1 = 0x80 | (0x1 & 0x0f);
		$length = strlen($socketData);
		
		if($length <= 125)
			$header = pack('CC', $b1, $length);
		elseif($length > 125 && $length < 65536)
			$header = pack('CCn', $b1, 126, $length);
		elseif($length >= 65536)
			$header = pack('CCNN', $b1, 127, $length);
		return $header.$socketData;
	}

	function doHandshake($received_header,$client_socket_resource, $host_name, $port)  /**  Здороваемся с клиентом */
    {
		$headers = array();
		$lines = preg_split("/\r\n/", $received_header);
		foreach($lines as $line)
		{
			$line = chop($line);
			if(preg_match('/\A(\S+): (.*)\z/', $line, $matches))
			{
				$headers[$matches[1]] = $matches[2];
			}
		}
		$secKey = $headers['Sec-WebSocket-Key'];
		$secAccept = base64_encode(pack('H*', sha1($secKey . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11')));
		$buffer  = "HTTP/1.1 101 Web Socket Protocol Handshake\r\n" .
            "Upgrade: websocket\r\n" .
            "Connection: Upgrade\r\n" .
            "WebSocket-Origin: $host_name\r\n" .
            "WebSocket-Location: ws://$host_name:$port/Server/php-socket.php\r\n".
            "Sec-WebSocket-Accept:$secAccept\r\n\r\n";
		socket_write($client_socket_resource,$buffer,strlen($buffer));
	}
	
	function newConnectionACK($client_ip_address) /** Отправить на фронт сообщение о коннекте юзера*/
    {
		$message = 'New client ' . $client_ip_address.' joined';
		$messageArray = array('message'=>$message,'message_type'=>'chat-connection-ack');
		$ACK = $this->seal(json_encode($messageArray));
		return $ACK;
	}
	
	function connectionDisconnectACK($client_ip_address) /** Отправить на фронт сообщение о дисконнекте юзера*/
    {
		$message = 'Client ' . $client_ip_address.' disconnected';
		$messageArray = array('message'=>$message,'message_type'=>'chat-connection-ack');
		$ACK = $this->seal(json_encode($messageArray));
		return $ACK;
	}
	
	function createChatBoxMessage($chat_user, $chat_box_message)
    {
		$message = $chat_user . ": <div class='chat-box-message'>" . $chat_box_message . "</div>";
		$messageArray = array('message'=>$message,'message_type'=>"chat_send_msg");
		$chatMessage = $this->seal(json_encode($messageArray));
		return $chatMessage;
	}

    function createRoomMessageData($room_message, $messageType) /** Отправить на фронт сообщение от юзера*/
    {
        $message = $room_message;
        $messageArray = array('message'=>$message,'message_type'=>$messageType);
        $jsonData = json_encode($messageArray);
        $dataMessage = $this->seal($jsonData);
        return $dataMessage;
    }
}
?>