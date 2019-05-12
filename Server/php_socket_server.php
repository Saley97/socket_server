<?php
define('HOST_NAME',"192.168.29.53");
define('PORT',"8090");
$null = NULL;

require_once("Server.class.php");
$server = new Server();
$serverHandler = Server::$handler;

$socketResource = socket_create(AF_INET, SOCK_STREAM, SOL_TCP); /** Создали сокет IPV4-поток-TCP */

socket_set_option($socketResource, SOL_SOCKET, SO_REUSEADDR, 1); /** Параметры сокета - на уровне сокета-адреса можно использовать повторно*/
socket_bind($socketResource, 0, PORT); /**  привязать сокет к адресу и порту (0 - ко всем адресам??)*/
socket_listen($socketResource); /** Сокет слушает все входящие соединения*/

$clientSocketArray = array($socketResource);  /** Создать массив клиентов сокета-сервера. Добавить туда его же */
while (true) { /** cycle */
	$newSocketArray = $clientSocketArray; /** Массив новых сокетов-клиентов */
	socket_select($newSocketArray, $null, $null, 0, 10);  /** Оставляем те из которых можно читать */
	
	if (in_array($socketResource, $newSocketArray)) /** Если сокет остался?? Проверяем что сервер не сдох?*/
	{
		$newSocket = socket_accept($socketResource); /**  Ожидает сокет-клиент */
		$clientSocketArray[] = $newSocket; /**  Добавляем в массив подключенных клиетов */
		
		$header = socket_read($newSocket, 1024); /**  Читаем хедер клиента */
		$serverHandler->doHandshake($header, $newSocket, HOST_NAME, PORT); /**  Здороваемся с клиентом */
		
		socket_getpeername($newSocket, $client_ip_address); /** Узнаем имя клиента */
//		$connectionACK = $serverHandler->newConnectionACK($client_ip_address); /** Кодируем сообщение о коннекте для отправки */
//
//		$serverHandler->send($connectionACK); /** Отправляем сообщение о коннекте всем клиент*/
		
		$newSocketIndex = array_search($socketResource, $newSocketArray); /**  Убираем из массива НОВЫХ сокетов сокет-СЕРВЕР*/
		unset($newSocketArray[$newSocketIndex]);                          /** */
	}
	
	foreach ($newSocketArray as $key => $newSocketArrayResource) {	/** По всем доступным сокетам-клиентам*/
		while(socket_recv($newSocketArrayResource, $socketData, 1024, 0) >= 1){ /** Получаем данные */
			$socketMessage = $serverHandler->unseal($socketData); /** Декодируем полученные данные в JSON */
			$messageObj = json_decode($socketMessage); /** Декодируем полученные данные в объект */

			/** Отвечаем на сообщения в зависимости от $messageObj->messageType */
            if ($messageObj != null && property_exists($messageObj, "messageType"))
            {
                switch ($messageObj->messageType)
                {
                    case "initUser":
                        $server->addUser($client_ip_address, $messageObj->user_name, $newSocketArrayResource, $messageObj->user_pts);
                        break;
                    case "chat_send_msg":
                        $room = $server->getRoomByUserName($messageObj->user_name);
                        if ($room != null) {
                            //$str = $server->getRoomByUserName($messageObj->user_name)->draw();                      /** Готовим ответ*/
                            //$chat_box_message = $serverHandler->createRoomMessageData($str, "answer");    /** Создаем ответ*/
//                            $chat_box_message = $serverHandler->createChatBoxMessage($messageObj->user_name, $messageObj->chat_message);    /** Создаем ответ*/
//                            $serverHandler->sendRoomClient($room, $chat_box_message);   /**  Отправляем сообщение каждому клиенту комнаты*/
                            $room->sendMsgToChat($messageObj->user_name, $messageObj->chat_message);
                        }
                        break;
                }
            }

			break 2;
		}

		/** Если не удалось ничего прочитать - разрываем соединение и сообщаем об этом в чат*/
		$socketData = @socket_read($newSocketArrayResource, 1024, PHP_NORMAL_READ);
		if ($socketData === false) { 
			socket_getpeername($newSocketArrayResource, $client_ip_address);
//			$connectionACK = $serverHandler->connectionDisconnectACK($client_ip_address);
//			$serverHandler->send($connectionACK);
			$newSocketIndex = array_search($newSocketArrayResource, $clientSocketArray);
			unset($clientSocketArray[$newSocketIndex]);			
		}
	}
}
socket_close($socketResource); /** Закрываем сокет сервер */