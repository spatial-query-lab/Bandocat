<?php
//for admin use only
include '../../Library/SessionManager.php';
$session = new SessionManager();
if(!$session->isAdmin())
    header('Location: ../../');
//This page executes the action from the queue.php
switch($_GET['action'])
{  
	case "clearlog":
		file_put_contents("../UpdateCron/UpdateLog.txt", "");
		break;
    case "displaylog":
        echo nl2br(file_get_contents("../UpdateCron/UpdateLog.txt"));
        break;
    default: break;
}

?>