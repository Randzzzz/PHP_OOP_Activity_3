<?php  
require_once 'article.php';
require_once 'database.php';
require_once 'user.php';
require_once 'notification.php';
require_once 'editrequest.php';

$databaseObj= new Database();
$userObj = new User();
$articleObj = new Article();
$notificationObj = new Notification();
$editRequestObj = new EditRequest();

$userObj->startSession();
?>