<?php
// get the name from cookie
$name = "";
if (isset($_COOKIE["name"])) {
    $name = $_COOKIE["name"];
}
print "<?xml version=\"1.0\" encoding=\"utf-8\"?>";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>Message Page</title>
        <link rel="stylesheet" type="text/css" href="style.css" />
        <script language="javascript" type="text/javascript">
        //<![CDATA[
        var loadTimer = null;
        var request;
        var datasize;
        var lastMsgID;
        function load() {
            var username = document.getElementById("username");
            if (username.value == "") {
                loadTimer = setTimeout("load()", 100);
                return;
            }
            loadTimer = null;
            datasize = 0;
            lastMsgID = 0;
            var node = document.getElementById("chatroom");
            node.style.setProperty("visibility", "visible", null);
            getUpdate();
        }
        function unload() {
            var username = document.getElementById("username");
            if (username.value != "") {
                //request = new ActiveXObject("Microsoft.XMLHTTP");
                request = new XMLHttpRequest();
                request.open("POST", "logout.php", true);
                request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                request.send(null);
                username.value = "";
            }
            if (loadTimer != null) {
                loadTimer = null;
                clearTimeout("load()", 100);
            }
        }           
        // create a HTTP request object
        function getUpdate() {
            //request = new ActiveXObject("Microsoft.XMLHTTP");
            request = new XMLHttpRequest();
            request.onreadystatechange = stateChange;
            request.open("POST", "server.php", true);
            request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            request.send("datasize=" + datasize);
        }
        
        // parse XML message to DOM
        function stateChange() {
            if (request.readyState == 4 && request.status == 200 && request.responseText) {
                var xmlDoc;
                try {
                    xmlDoc =new XMLHttpRequest();
                    xmlDoc.loadXML(request.responseText);
                } 
                catch (e) {
                    var parser = new DOMParser();
                    xmlDoc = parser.parseFromString(request.responseText, "text/xml");
                }
                datasize = request.responseText.length;
                updateChat(xmlDoc);
                getUpdate();
            }
        }
            
        // update the chat area
        function updateChat(xmlDoc) {

            //point to the message nodes
            var messages = xmlDoc.getElementsByTagName("message");
            
            // create a string for the messages
            for (var i = lastMsgID; i < messages.length; i++) {
                var msg = messages.item(i);
                showMessage(msg.getAttribute("name"), msg.textContent, msg.getAttribute("textcolor"));
            }
            lastMsgID = messages.length;
        }
        function showMessage(nameStr, contentStr, textcolor){

            var node = document.getElementById("chatroom");
            
            // Create div element to contain one entry (name and message)
            var oneEntryNode = document.createElement("div");
            oneEntryNode.setAttribute("class", "oneEntry");
          

            // Create the div element for name and put text
            var nameNode = document.createElement("div");
            nameNode.setAttribute("class", "nameEntry");
            nameNode.appendChild(document.createTextNode(nameStr+":"));
            oneEntryNode.appendChild(nameNode);
            
            // Replace all message that content hyperlinks with html links
            contentStr = contentStr.replace(/((http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?)/g,
                '<a target="blank" href="$1">$1</a>');
            
            // Create the div element for message and put text
            var msgNode = document.createElement("div");
            msgNode.setAttribute("class", "msgEntry");
            msgNode.style.color = textcolor;
            msgNode.innerHTML = contentStr;
            oneEntryNode.appendChild(msgNode);

            node.appendChild(oneEntryNode);
        }
        //]]>
        </script>
    </head>

    <body style="text-align: left" onload="load()" onunload="unload()">
         <div id="chatroom"/>
         <form action="">
            <input type="hidden" value="<?php print $name; ?>" id="username" />
        </form>

    </body>
</html>
