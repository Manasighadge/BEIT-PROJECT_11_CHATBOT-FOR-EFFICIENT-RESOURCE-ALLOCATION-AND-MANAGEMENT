<?php
session_start();
//print_r($_POST);
include ('Chat.php');
$chat = new Chat();
if($_POST['action'] == 'update_user_list') {
	$chatUsers = $chat->chatUsers($_SESSION['userid']);
	$data = array(
		"profileHTML" => $chatUsers,	
	);
	echo json_encode($data);	
}
if($_POST['action'] == 'insert_chat') {
	$chat->insertChat($_POST['to_user_id'], $_SESSION['userid'], $_POST['chat_message']);

}
if($_POST['action'] == 'show_chat') {
	$chat->showUserChat($_SESSION['userid'], $_POST['to_user_id']);
}
if($_POST['action'] == 'update_user_chat') {
	$conversation = $chat->getUserChat($_SESSION['userid'], $_POST['to_user_id']);
	$data = array(
		"conversation" => $conversation			
	);
	echo json_encode($data);
}
if($_POST['action'] == 'update_unread_message') {
	$count = $chat->getUnreadMessageCount($_POST['to_user_id'], $_SESSION['userid']);
	$data = array(
		"count" => $count			
	);
	echo json_encode($data);
}


if($_POST['action'] == 'update_typing_status') {
	$chat->updateTypingStatus($_POST["is_type"], $_SESSION["login_details_id"]);
}
if($_POST['action'] == 'show_typing_status') {
	$message = $chat->fetchIsTypeStatus($_POST['to_user_id']);
	$data = array(
		"message" => $message			
	);
	echo json_encode($data);
}
if($_POST['action']=='insert_answer'){
	/*echo '<pre>';
	//print_r($_GET);
	print_r($_POST);*/
	//exit;
	$postData = $_POST;
	$message = explode('|', $postData['message']);
	$_SESSION[$message[0]] = $message[1];
	//echo $message[0];
	if($message[0]=='input'){
		$postData['message'] = 'Required For : '.$message[1];
	}else {
		$postData['message'] = str_replace('|', ' : ', $postData['message']);
	}
	//echo $postData['message'];exit;
	$ins=$chat->insertChat($_POST['to_user_id'], $_SESSION['userid'], $postData['message']);
	if($message[0]=='input'){
		$successMsg = $chat->insertChat(1, $_SESSION['userid'], '<span style="font-size: 28px">Slot Booked Successfully</span>');
		$booking = $chat->requestBooking();
		return $chat->get_questions();
	}

	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $chat->get_question_url());
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_FAILONERROR, true);
	$result=curl_exec($ch);
	if (curl_errno($ch)) {
	    $error_msg = curl_error($ch);
	}
	curl_close($ch);

	if (isset($error_msg)) {
		echo '<pre>';
		print_r($error_msg);
		exit;
	}

	$search = $message[0];
	$questions=json_decode($result,true);
	//print_r($questions);exit;
	$flag=0;
	$searchFound=0;
	$nextq = '';
	foreach ($questions as $qkey => $question) {
		//next($questions);
		if($qkey==$search){
			$searchFound = 1;
			//break;
		}

		//echo $searchFound." ".$search." ".$qkey."<br>";
		if($searchFound && $qkey!=$search){
			$nextq = $qkey;
			break;
		}
	}
	/*echo $nextq;
	exit;*/
	$chatMessage = $chat->create_option_message($questions[$nextq], $nextq);
	//print_r($chatMessage);exit;
	if(trim($chatMessage)!=''){
		$chat->insertChat($_SESSION['userid'], 1, str_replace('"', '\"', $chatMessage));
	}

	
}
	if($_POST['action'] == 'insert_date') {

		/*print_r($_POST);
		exit;*/
		/*$slots = $chat->getdatewiseSlots($_POST['message']);
		exit;*/
		$message=$chat->insertChat($_POST['to_user_id'], $_SESSION['userid'], "<b>Booking For:</b> ".$_POST['message']);
		$_SESSION['date'] = $_POST['message'];
		$_POST['level'] = 'labs';
		//print_r($_POST);exit;
		$slot = $chat->get_questions();
		//print_r($slot);
		/*$chatMessage = $chat->create_option_message([''], $nextq);
		//print_r($chatMessage);
		if(trim($chatMessage)!=''){
			$chat->insertChat($_SESSION['userid'], 1, str_replace('"', '\"', $chatMessage));
		}*/

	}	
?>