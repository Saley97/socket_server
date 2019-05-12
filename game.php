<html>
<head>
    <link rel="stylesheet" href="styles/chat.css">
	<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
    <script src="js/chat.js"></script>
    <?php
    session_start();
    ?>
	<script>  
	function showMessage(messageHTML) {
		$('#chat-box').append(messageHTML);
	}

	$(document).ready(function(){
		var websocket = new WebSocket("ws://192.168.29.53:8090/Server/php_socket_server.php");
		websocket.onopen = function(event) {
            var messageJSON = {
                messageType: "initUser",
                user_name: "<?=$_SESSION["loggued_on_user"]?>",
                user_pts: "<?=$_SESSION["pts"]?>"
            };
            websocket.send(JSON.stringify(messageJSON));
		}

		websocket.onmessage = function(event) {
			var Data = JSON.parse(event.data);
			 //showMessage("<div class='"+Data.message_type+"'>"+Data.message+"</div>");
            switch (Data.message_type)
            {
                case "chat_message":
                    sendChatMessage(Data.message);
                    break;
            }
            showMessage(""+Data.message+"");
		};

		websocket.onerror = function(event){
			showMessage("<div class='error'>Problem due to some Error</div>");
		};

		websocket.onclose = function(event){
			showMessage("<div class='chat-connection-ack'>Connection Closed</div>");
		}; 
		
		$('#frmChat').on("submit",function(event){
			event.preventDefault();
			var messageJSON = {
                messageType: "chat_send_msg",
                user_name: "<?=$_SESSION["loggued_on_user"]?>",
                chat_message: $('#chat-message').val()
			};
			websocket.send(JSON.stringify(messageJSON));
		});
	});




	</script>
	</head>
	<body>
		<form name="frmChat" id="frmChat">
			<div id="chat-box"></div>
			<input type="text" name="chat-message" id="chat-message" placeholder="Message"  class="chat-input chat-message" required/>
			<input type="submit" id="btnSend" name="send-chat-message" value="Send" >
		</form>
</body>
</html>