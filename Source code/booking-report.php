<?php 
session_start();
?>

<?php
if(isset($_SESSION['userid']) && $_SESSION['userid']) { 

	if($_SESSION['is_admin']==false){
		header("Location:user_index.php");
	}

include ('Chat.php');
$chat = new Chat();
//$result = $chat->getbookings(); 
$filename = 'booking';   
$file_ending = "xls";
//header info for browser
header("Content-Type: application/xls");    
header("Content-Disposition: attachment; filename=$filename.xls");  
header("Pragma: no-cache"); 
header("Expires: 0");
/*******Start of Formatting for Excel*******/   
//define separator (defines columns in excel & tabs in word)
$sep = "\t"; //tabbed character
//start of printing column names as names of MySQL fields
$heading = ['Date', 'Day', 'Lab no'];
$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $chat->get_question_url());
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_FAILONERROR, true);
	$questions=curl_exec($ch);
	if (curl_errno($ch)) {
	    $error_msg = curl_error($ch);
	}
	curl_close($ch);

	if (isset($error_msg)) {
		echo '<pre>';
		print_r($error_msg);
		exit;
	}

	$questions=json_decode($questions,true);
	$slots = $questions['slot']['Select Slot:'];
	$heading = array_merge($heading, $slots);
	
for ($i = 0; $i < count($heading); $i++) {
echo $heading[$i] . "\t";
}
print("\n");

$result = $chat->getbookinglist();
//echo '<pre>';
//print_r($result);
foreach ($result as $rkey => $datewise) {
		//print_r($datewise);
		foreach ($datewise as $dkey => $dvalue) {
			echo $rkey . "\t";
			echo date('l', strtotime($rkey)) . "\t";
			echo $dkey . "\t";
			//print_r($dvalue);
			foreach ($slots as $skey => $svalue) {
				if(isset($dvalue[$svalue])){
					echo $dvalue[$svalue] . "\t";
				}else{
					echo "\t";
				}
				//print_r($svalue);
			}
			echo "\n";
		}
	}

}   
?>