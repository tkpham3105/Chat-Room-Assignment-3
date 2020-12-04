<?php

// if name is not in the post data, exit
if (!isset($_POST["name"])) {
    header("Location: error.html");
    exit;
}

require_once('xmlHandler.php');

// create the chatroom xml file handler
$xmlh = new xmlHandler("chatroom.xml");
if (!$xmlh->fileExist()) {
    header("Location: error.html");
    exit;
}

// open the existing XML file
$xmlh->openFile();

// get the 'users' element
$users_element = $xmlh->getElement("users");

// create a 'user' element
$user_element = $xmlh->addElement($users_element, "user");

// add the user name
$xmlh->setAttribute($user_element, "name", $_POST["name"]);

// Picture arrangements, move uploaded file to webserver
$pic_name = $_FILES["pic-upload"]["name"];
$pic_tmp = $_FILES["pic-upload"]["tmp_name"];
$pic_dir = "images/".$pic_name;
move_uploaded_file($pic_tmp, $pic_dir);

// add the picture dir
$xmlh->setAttribute($user_element, "pic-upload", $pic_dir);

// save the XML file
$xmlh->saveFile();

setcookie("name", $_POST["name"]);

// Cookie done, redirect to client.php (to avoid reloading of page from the client)
header("Location: client.php");

?>
