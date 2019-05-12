function sendChatMessage(message)
{
    let chat = document.querySelector("#chat-box");
    chat.innerHTML += "<div class='chat-box-html'>"+message+"</div>\n";
}