<?php
/**
 * Created by PhpStorm.
 * User: Wei.Cheng
 * Date: 2018/7/18
 * Time: 上午 09:15
 */
?>
<script>
    var conn = new WebSocket('ws://localhost:8080');
    conn.onopen = function(e) {
        console.log("Connection established!");
    };

    conn.onmessage = function(e) {
        console.log(e.data);
    };

    function sendMessage(){
        console.log("Sending message...");
        var text = document.getElementById("text").value;
        conn.send(text);
        document.getElementById("text").value = "";
    }
</script>
<input type="textbox" id="text" />
<button onclick="sendMessage()">send</button>