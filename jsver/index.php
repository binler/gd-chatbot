<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="test.css">
    <script type="text/javascript" src="https://code.jquery.com/jquery-3.3.1.js"></script>
    <title>Botman</title>
</head>
<body>

 <div class='chat-wrapper'>

   <div class='chat-message padding chat-area'>

   </div>

 </div>

 <div class="area-chat">
     <input type="text" class="ipt-message" name="message" value="" placeholder="Input message">
 </div>
    <script>
    $('.ipt-message').keypress(function (e) {
        if (e.which == 13) {
            display_sender($(this).val());
            var datatest = {driver: 'web', userId: 'jh5yal', message: $(this).val()}
            $.ajax({
                url: 'http://localhost:8000',
                data: datatest,
                type: 'POST',
                dataType: 'JSON'
            }).done(function(result){
                var msg_reply = result.messages;
                display_reply(msg_reply);
                $('.ipt-message').val('');
            })
            return false;
        }
    });

    function display_reply(messages) {
        let areaChat = $('.chat-area');
        for (var i = 0; i < messages.length; i++) {
            areaChat.append('<div class="chat-message chat-message-recipient"><div class="chat-message-wrapper"><div class="chat-message-content">' +
                '<p>' + messages[i].text + '</p>' +
             '</div></div></div>');
        }
    }

    function display_sender(text) {
        let areaChat = $('.chat-area');
        areaChat.append('<div class="chat-message chat-message-sender"><div class="chat-message-wrapper"><div class="chat-message-content">' +
            '<p>' + text + '</p>' +
         '</div></div></div>');
    }

    </script>
</body>
</html>
