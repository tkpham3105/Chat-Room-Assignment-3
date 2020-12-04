<?php
print "<?xml version=\"1.0\" encoding=\"utf-8\"?>";
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>Online User List</title>
        <link rel="stylesheet" type="text/css" href="style.css" />
        <script language="javascript" type="text/javascript">
        //<![CDATA[
        var loadTimer = null;
        var request;
        var datasize;

        function load() {
            loadTimer = null;
            datasize = 0;
            getUpdate();
        }

        function unload() {
            if (loadTimer != null) {
                loadTimer = null;
                clearTimeout("load()", 100);
            }
        }

        function getUpdate() {
            request =new XMLHttpRequest();
            request.onreadystatechange = stateChange;
            request.open("POST", "server.php", true);
            request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            request.send("datasize=" + datasize);
        }

        function stateChange() {
            if (request.readyState == 4 && request.status == 200 && request.responseText) {
                var xmlDoc;
                try {
                    xmlDoc = new XMLHttpRequest();
                    xmlDoc.loadXML(request.responseText);
                } catch (e) {
                    var parser = new DOMParser();
                    xmlDoc = parser.parseFromString(request.responseText, "text/xml");
                }
                datasize = request.responseText.length;
                updateOnlineUserList(xmlDoc);
                getUpdate();
            }
        }
        function updateOnlineUserList(xmlDoc) {

            clearUserList();

            var node = document.getElementById("onlinelist");
            var title = document.createElement("h1");
            title.appendChild(document.createTextNode("Online User List"));
            node.appendChild(title);

            //point to the user nodes
            var users = xmlDoc.getElementsByTagName("user");

            for (var i = 0; i < users.length; i++) {
                var user = users.item(i);
                showUserList(user.getAttribute("name"), user.getAttribute("pic-upload"));
            }
        }

        function clearUserList() {
            var node = document.getElementById("onlinelist");
            // remove all child
            while (node.hasChildNodes()) {
                node.removeChild(node.lastChild);
            }
        }

        function showUserList(nameStr, pictureStr) {

            var node = document.getElementById("onlinelist");

            // Create div element to contain one entry (pciture and name)
            var oneUserListNode = document.createElement("div");
            oneUserListNode.setAttribute("class", "oneUserList");

            // Create the div element for picture
            var picNode = document.createElement("div");
            picNode.setAttribute("class", "picEntry");
            var image = document.createElement("img");
            image.src = pictureStr;
            image.setAttribute("height", 100);
            image.setAttribute("width", 100);
            picNode.appendChild(image);
            oneUserListNode.appendChild(picNode);

            // Create the div element for name and put text
            var nameNode = document.createElement("div");
            nameNode.setAttribute("class", "onlineNameEntry");
            nameNode.appendChild(document.createTextNode(nameStr));
            oneUserListNode.appendChild(nameNode);

            node.appendChild(oneUserListNode);
        }
        //]]>
        </script>
    </head>
    <body onload="load()" onunload="unload()">
        <div id="onlinelist">
            <h1>Online User List</h1>
        </div>
    </body>
</html>