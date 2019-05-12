>>>>>>>>>>>Как запустить

Запускаем сокет-сервер в корневой директории:
    php Server/php_socket_server.php

Запускаем основной php сервер в корневой директории:
    php -S localhost:8800
    (либо любой другой хост и порт)

>>>>>>>>>>>Как подключиться

В браузере

>>>>>>>>>>>Логика игры

На сокет-сервер приходит запрос (например со строки 45 в game.php):

        $('#frmChat').on("submit",function(event){
			event.preventDefault();
			var messageJSON = {
                messageType: "chat_send_msg",
                user_name: "<?=$_SESSION["loggued_on_user"]?>",
                chat_message: $('#chat-message').val()
			};
			websocket.send(JSON.stringify(messageJSON));
		});

Он его обрабатывает и рассылает ответы (со строки 41 в php_socket_server.php):

            $messageObj = json_decode($socketMessage);

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
                            $room->sendMsgToChat($messageObj->user_name, $messageObj->chat_message);
                        }
                        break;
                }

Ответы обрабатываются на клиентской стороной js`ом (websocket.onmessage)
(например со строки 45 в game.php):

        websocket.onmessage = function(event) {
			var Data = JSON.parse(event.data);
            switch (Data.message_type)
            {
                case "chat_message":
                    sendChatMessage(Data.message);
                    break;
            }
            showMessage(""+Data.message+"");
		};