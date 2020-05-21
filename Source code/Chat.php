<?php
class Chat{
    private $host  = 'localhost';
    private $user  = 'root';
    private $password   = "";
    private $database  = "chat";      
    private $chatTable = 'chat';
	private $chatUsersTable = 'chat_users';
	private $chatLoginDetailsTable = 'chat_login_details';
	private $dbConnect = false;
    public function __construct(){
        if(!$this->dbConnect){ 
            $conn = new mysqli($this->host, $this->user, $this->password, $this->database);
            if($conn->connect_error){
                die("Error failed to connect to MySQL: " . $conn->connect_error);
            }else{
                $this->dbConnect = $conn;
            }
        }
    }
	private function getData($sqlQuery) {
		$result = mysqli_query($this->dbConnect, $sqlQuery);
		if(!$result){
			die('Error in query: '. mysqli_error($this->dbConnect));
		}
		$data= array();
		while ($row = $result -> fetch_array(MYSQLI_ASSOC)) {
			$data[]=$row;            
		}
		return $data;
	}
	private function getNumRows($sqlQuery) {
		$result = mysqli_query($this->dbConnect, $sqlQuery);
		if(!$result){
			die('Error in query: '. mysqli_error($this->dbConnect));
		}
		$numRows = mysqli_num_rows($result);
		return $numRows;
	}
	public function loginUsers($username, $password){
		$sqlQuery = "
			SELECT userid, username, is_admin 
			FROM ".$this->chatUsersTable." 
			WHERE username='".$username."' AND password='".$password."'";		
        return  $this->getData($sqlQuery);
	}		
	public function chatUsers($userid, $is_admin=true){
		if($is_admin){
			$sqlQuery = "
			SELECT * FROM ".$this->chatUsersTable." 
			WHERE userid != '$userid' and is_admin=true";
		}else{
			$sqlQuery = "
			SELECT * FROM ".$this->chatUsersTable." 
			WHERE userid != '$userid' and is_admin=false";
		}
		//echo $sqlQuery;
		return  $this->getData($sqlQuery);
	}
	public function getUserDetails($userid){
		$sqlQuery = "
			SELECT * FROM ".$this->chatUsersTable." 
			WHERE userid = '$userid'";
		return  $this->getData($sqlQuery);
	}
	public function getUserAvatar($userid){
		$sqlQuery = "
			SELECT avatar 
			FROM ".$this->chatUsersTable." 
			WHERE userid = '$userid'";
		$userResult = $this->getData($sqlQuery);
		$userAvatar = '';
		foreach ($userResult as $user) {
			$userAvatar = $user['avatar'];
		}	
		return $userAvatar;
	}	
	public function updateUserOnline($userId, $online) {		
		$sqlUserUpdate = "
			UPDATE ".$this->chatUsersTable." 
			SET online = '".$online."' 
			WHERE userid = '".$userId."'";			
		mysqli_query($this->dbConnect, $sqlUserUpdate);		
	}
	public function insertChat($reciever_userid, $user_id, $chat_message) {		
		$sqlInsert = "
			INSERT INTO ".$this->chatTable." 
			(reciever_userid, sender_userid, message, status) 
			VALUES ('".$reciever_userid."', '".$user_id."', '".$chat_message."', '1')";
		//echo $sqlInsert;
		$result = mysqli_query($this->dbConnect, $sqlInsert);
		if(!$result){
			return ('Error in query: '. mysqli_error($this->dbConnect));
		} else {
			$conversation = $this->getUserChat($user_id, $reciever_userid);
			$data = array(
				"conversation" => $conversation			
			);
			return json_encode($data);	
		}
	}
	
	public function insertUser($username, $password, $emailsignup,$avatar) {		
		$sqlInsert = "
			INSERT INTO chat_users 
			(username, password, avatar,emailsignup,current_session,online) 
			VALUES ('".$username."', '".$password."', '".$avatar."','".$emailsignup."',0,0)";
		
		$result = mysqli_query($this->dbConnect, $sqlInsert);
		if(!$result){
			//echo mysqli_error($this->dbConnect);exit;
			return ('Error in query: '. mysqli_error($this->dbConnect));
		} else {
			
			return ('Inserted Successfully');
		}
	}
	
	public function getUserChat($from_user_id, $to_user_id) {
		$fromUserAvatar = $this->getUserAvatar($from_user_id);	
		$toUserAvatar = $this->getUserAvatar($to_user_id);			
		$sqlQuery = "
			SELECT * FROM ".$this->chatTable." 
			WHERE (sender_userid = '".$from_user_id."' 
			AND reciever_userid = '".$to_user_id."') 
			OR (sender_userid = '".$to_user_id."' 
			AND reciever_userid = '".$from_user_id."') 
			ORDER BY timestamp ASC";
		$userChat = $this->getData($sqlQuery);	
		$conversation = '<ul>';
		foreach($userChat as $chat){
			$user_name = '';
			if($chat["sender_userid"] == $from_user_id) {
				$conversation .= '<li class="sent">';
				$conversation .= '<img width="22px" height="22px" src="userpics/'.$fromUserAvatar.'" alt="" />';
			} else {
				$conversation .= '<li class="replies">';
				$conversation .= '<img width="22px" height="22px" src="userpics/'.$toUserAvatar.'" alt="" />';
			}			
			$conversation .= '<p>'.$chat["message"].'</p>';			
			$conversation .= '</li>';
		}		
		$conversation .= '</ul>';
		return $conversation;
	}
	public function showUserChat($from_user_id, $to_user_id) {		
		$userDetails = $this->getUserDetails($to_user_id);
		$toUserAvatar = '';
		foreach ($userDetails as $user) {
			$toUserAvatar = $user['avatar'];
			$userSection = '<img src="userpics/'.$user['avatar'].'" alt="" />
				<p>'.$user['username'].'</p>';
				/*'<div class="social-media">
					<i class="fa fa-facebook" aria-hidden="true"></i>
					<i class="fa fa-twitter" aria-hidden="true"></i>
					 <i class="fa fa-instagram" aria-hidden="true"></i>
				</div>';*/
		}		
		// get user conversation
		$conversation = $this->getUserChat($from_user_id, $to_user_id);	
		//print_r($conversation);
		// update chat user read status		
		$sqlUpdate = "
			UPDATE ".$this->chatTable." 
			SET status = '0' 
			WHERE sender_userid = '".$to_user_id."' AND reciever_userid = '".$from_user_id."' AND status = '1'";
		mysqli_query($this->dbConnect, $sqlUpdate);		
		// update users current chat session
		$sqlUserUpdate = "
			UPDATE ".$this->chatUsersTable." 
			SET current_session = '".$to_user_id."' 
			WHERE userid = '".$from_user_id."'";
		mysqli_query($this->dbConnect, $sqlUserUpdate);		
		$data = array(
			"userSection" => $userSection,
			"conversation" => $conversation			
		 );
		 echo json_encode($data);		
	}	
	public function getUnreadMessageCount($senderUserid, $recieverUserid) {
		$sqlQuery = "
			SELECT * FROM ".$this->chatTable."  
			WHERE sender_userid = '$senderUserid' AND reciever_userid = '$recieverUserid' AND status = '1'";
		$numRows = $this->getNumRows($sqlQuery);
		$output = '';
		if($numRows > 0){
			$output = $numRows;
		}
		return $output;
	}	
	public function updateTypingStatus($is_type, $loginDetailsId) {		
		$sqlUpdate = "
			UPDATE ".$this->chatLoginDetailsTable." 
			SET is_typing = '".$is_type."' 
			WHERE id = '".$loginDetailsId."'";
		mysqli_query($this->dbConnect, $sqlUpdate);
	}		
	public function fetchIsTypeStatus($userId){
		$sqlQuery = "
		SELECT is_typing FROM ".$this->chatLoginDetailsTable." 
		WHERE userid = '".$userId."' ORDER BY last_activity DESC LIMIT 1"; 
		$result =  $this->getData($sqlQuery);
		$output = '';
		foreach($result as $row) {
			if($row["is_typing"] == 'yes'){
				$output = ' - <small><em>Typing...</em></small>';
			}
		}
		return $output;
	}		
	public function insertUserLoginDetails($userId) {		
		$sqlInsert = "
			INSERT INTO ".$this->chatLoginDetailsTable."(userid) 
			VALUES ('".$userId."')";
		mysqli_query($this->dbConnect, $sqlInsert);
		$lastInsertId = mysqli_insert_id($this->dbConnect);
        return $lastInsertId;		
	}	
	public function updateLastActivity($loginDetailsId) {		
		$sqlUpdate = "
			UPDATE ".$this->chatLoginDetailsTable." 
			SET last_activity = now() 
			WHERE id = '".$loginDetailsId."'";
		mysqli_query($this->dbConnect, $sqlUpdate);
	}	
	public function getUserLastActivity($userId) {
		$sqlQuery = "
			SELECT last_activity FROM ".$this->chatLoginDetailsTable." 
			WHERE userid = '$userId' ORDER BY last_activity DESC LIMIT 1";
		$result =  $this->getData($sqlQuery);
		foreach($result as $row) {
			return $row['last_activity'];
		}
	}

	public function get_question_url() {
		return 'http://localhost/chatbot/questions.php';
	}

	public function get_questions(){
		$level='hardware';
		if(isset($_POST['level'])){
			$level = $_POST['level'];
		}
		//echo $level;exit;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->get_question_url());
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FAILONERROR, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, ['level'=>$level]); 
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

		$questionArray=json_decode($result,true);
		/*echo '<pre>';
		print_r($questionArray);exit;*/
		//print_r($questionArray[$level]);exit;
		$chatMessage = $this->create_option_message($questionArray[$level], $level);
		if(trim($chatMessage)!=''){
			//if($level!='slot')
				return $this->insertChat($_SESSION['userid'], 1, $chatMessage);
			/*else
				return $this->insertChat(1, $_SESSION['userid'], $chatMessage);*/

		}else{
			return true;
		}
	}

	public function create_option_message($options=[], $level='hardware'){
		if(empty($options)){
			return false;
		}
		//print_r($options);
		$str='';
		$class='options';
		$str.= '<span id="'.$level.rand().'" class="optgroup">';
		if($level=='date'){
			$class=$level;
		}

		/*$labOption = [];
		if($level=='labs'){
			$labOption = $this->getavailableLabs();
			echo '<pre>';
			print_r($labOption);
			exit;
		}*/

		if($level=='hardware' || $level=='software' || $level=='capacity'){
			$options=[];
			$options[$level] = $this->getavailableLabs($level);
		}

		if($level=='date'){
			/*$options=[];
			$options[$level] = $this->getavailableSlots();*/
		}

		if($level=='labs'){
			$options=[];
			$options[$level] = $this->getavailableLabs($level);
		}

		$bookedSlot = [];
		if($level=='slot'){
			$slt = $this->getbookedSlots();
			/*print_r($slt);
			exit;*/
		}

		foreach ($options as $key => $questions) {
			/*if($level=='labs'){
				$str.="Available ".$key.'<br>';
			}else{
			}*/
				$str.=$key.'<br>';
			foreach ($questions as $qkey => $option) {
				if($level=='date' || $level=='input'){
					$str.=''.$option.'';
				}else{
					if($level=='slot'){
						if(in_array($option, $slt)){
						$str.='<i class="fa fa-hand-o-right" style="color:red"></i> <strike>'.$option.'</strike><br>';

						}else{
							$str.='<i class="fa fa-hand-o-right" style="color:red"></i> <a class="'.$class.' '.$level.'" href="chat_action.php" data-message="'.$level.'|'.$option.'">'.$option.'</a><br>';

						}
					}else{

						$str.='<i class="fa fa-hand-o-right" style="color:red"></i> <a class="'.$class.' '.$level.'" href="chat_action.php" data-message="'.$level.'|'.$option.'">'.$option.'</a><br>';
					}
				}
			}

		}

		return $str.'</span>';
	}

	function getavailableLabs($level){
		$select = '';
		if($level=='hardware'){
			$select = 'hardware';
		}
		if($level=='software'){
			$select = 'software';
		}

		if($level=='capacity'){
			$select = 'capacity';
		}

		if($level=='labs'){
			$select = 'lab_no';
		}

		$sql = 'Select distinct '.$select.' from labs where is_active=true';
		if(isset($_SESSION['hardware'])){
			$sql.=' and hardware like "'.$_SESSION['hardware'].'"';	
		}

		if(isset($_SESSION['software'])){
			$sql.=' and software like "'.$_SESSION['software'].'"';
		}

		if(isset($_SESSION['capacity'])){
			$sql.=' and capacity="'.$_SESSION['capacity'].'"';
		}
		//echo $sql;
		$labs = mysqli_query($this->dbConnect, $sql);
		$data= array();
		//echo '<pre>';
		while ($row = $labs -> fetch_array(MYSQLI_ASSOC)) {
			//print_r($row);
			$data[]=($level=='labs')?$row['lab_no']:$row[$level];            
		}
		return $data;
	}

	function getbookedSlots(){
		/*echo '<pre>';
		print_r($_SESSION);*/
		$sql = 'Select slot from lab_bookings where is_active=true and date like "'.$_SESSION['date'].'" and lab_no = "'.$_SESSION['labs'].'"';
		$labs = mysqli_query($this->dbConnect, $sql);
		$data= array();
		//echo '<pre>';
		while ($row = $labs -> fetch_array(MYSQLI_ASSOC)) {
			//print_r($row);
			$data[]=$row['slot'];            
		}
		return $data;
	}

	public function requestBooking() {		
		$sqlInsert = "
			INSERT INTO lab_bookings 
			(`user_id`, `date`, `slot`, `lab_no`, `booked_for`, `created_by`,`created`) 
			VALUES ('".$_SESSION['userid']."', '".$_SESSION['date']."', '".$_SESSION['slot']."', '".$_SESSION['labs']."', '".$_SESSION['input']."', '".$_SESSION['userid']."', '".date('Y-m-d H:i:s')."')";
		//echo $sqlInsert;
		$result = mysqli_query($this->dbConnect, $sqlInsert);
		if(!$result){
			return ('Error in query: '. mysqli_error($this->dbConnect));
		} else {
			unset($_SESSION['date']);
			unset($_SESSION['labs']);
			unset($_SESSION['slot']);
			unset($_SESSION['hardware']);
			unset($_SESSION['software']);
			unset($_SESSION['capacity']);
			$conversation = $this->getUserChat($_SESSION['userid'], 1);
			$data = array(
				"conversation" => $conversation			
			);
			return json_encode($data);	
		}
	}	

	function getbookinglist(){
		$sql = 'Select * from lab_bookings where is_active=true order by date ASC,lab_no asc';
		$labs = mysqli_query($this->dbConnect, $sql);
		$data= array();
		//echo '<pre>';
		while ($row = $labs -> fetch_array(MYSQLI_ASSOC)) {
			//print_r($row);
			$data[$row['date']][$row['lab_no']][$row['slot']]=$row['booked_for'];            
		}
		return $data;
	}
}
?>