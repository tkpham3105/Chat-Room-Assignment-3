<?php 

require_once('xmlHandler.php');

if (!isset($_COOKIE["name"])) {
    header("Location: error.html");
    exit;
}

// create the chatroom xml file handler
$xmlh = new xmlHandler("chatroom.xml");
if (!$xmlh->fileExist()) {
    header("Location: error.html");
    exit;
}

// get user name from cookie
$name = $_COOKIE["name"];
print($name);

// open the existing XML file
$xmlh->openFile();

// get the 'users' element
$users_element = $xmlh->getElement("users");

// get all 'user' nodes
$user_element = $xmlh->getChildNodes("user");

if($user_element != null) {
    // delete the current user from the users element
    foreach ($user_element as $user) {
        $username = $xmlh->getAttribute($user, "name");
        if ($username == $name) {
            // Remove picture from our images folder
            $uploadpic = $xmlh->getAttribute($user, "pic-upload");
            unlink($uploadpic);

            // Remove the user element completely
            $xmlh->removeElement($users_element, $user);
        }
    }
}

// save the XML file
$xmlh->saveFile();

// set the name to the cookie
setcookie("name","");

// Cookie done, redirect to client.php (to avoid reloading of page from the client)
header("Location: login.html");

?>
