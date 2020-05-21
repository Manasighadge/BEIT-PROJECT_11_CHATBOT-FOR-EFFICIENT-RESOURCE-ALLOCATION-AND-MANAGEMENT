<?php 
session_start();
?>

<?php
if(isset($_SESSION['userid']) && $_SESSION['userid']) { 
	
	if($_SESSION['is_admin']==true){
		header("Location:index.php");
	}
include('header.php');
?>
<title>Welcome User</title>
<link rel='stylesheet prefetch' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.6.2/css/font-awesome.min.css'>
<link href="css/style.css" rel="stylesheet" id="bootstrap-css">
<script src="js/chat-user.js"></script>
<style>
.modal-dialog {
    width: 400px;
    margin: 30px auto;	
}

</style>
<?php include('container.php');?>
<div class="container">		
	<!-- <h1>Example: Build Live Chat System with Ajax, PHP & MySQL</h1>	 -->	
	<br>		
	 	
		<div class="chat">	
			<div id="frame">		
				<div id="sidepanel">
					<div id="profile">
					<?php
					include ('Chat.php');
					$chat = new Chat();
					$loggedUser = $chat->getUserDetails($_SESSION['userid']);
					echo '<div class="wrap">';
					$currentSession = '';
					foreach ($loggedUser as $user) {
						$currentSession = $user['current_session'];
						echo '<img id="profile-img" src="userpics/'.$user['avatar'].'" class="online" alt="" />';
						echo  '<p>'.$user['username'].'</p>';
							echo '<i class="fa fa-chevron-down expand-button" aria-hidden="true"></i>';
							echo '<div id="status-options">';
							echo '<ul>';
								echo '<li id="status-online" class="active"><span class="status-circle"></span> <p>Online</p></li>';
								echo '<li id="status-away"><span class="status-circle"></span> <p>Away</p></li>';
								echo '<li id="status-busy"><span class="status-circle"></span> <p>Busy</p></li>';
								echo '<li id="status-offline"><span class="status-circle"></span> <p>Offline</p></li>';
							echo '</ul>';
							echo '</div>';
							echo '<div id="expanded">';			
							echo '<a href="logout.php">Logout</a>';
							echo '</div>';
					}
					echo '</div>';
					?>
					</div>
					<!-- <div id="search">
						<label for=""><i class="fa fa-search" aria-hidden="true"></i></label>
						<input type="text" placeholder="Search contacts..." />					
					</div> -->
					<div id="contacts">	
					<?php
					echo '<ul>';
					$chatUsers = $chat->chatUsers($_SESSION['userid']);
					foreach ($chatUsers as $user) {
						$status = 'online';						
						if($user['online']) {
							$status = 'online';
						}
						$activeUser = 'active';
						if($user['userid'] == $currentSession) {
							$activeUser = "active";
						}
						echo '<li id="'.$user['userid'].'" class="contact '.$activeUser.'" data-touserid="'.$user['userid'].'" data-tousername="'.$user['username'].'">';
						echo '<div class="wrap">';
						echo '<span id="status_'.$user['userid'].'" class="contact-status '.$status.'"></span>';
						echo '<img src="userpics/'.$user['avatar'].'" alt="" />';
						echo '<div class="meta">';
						echo '<p class="name">'.$user['username'].'</p>';
						echo '<p class="preview"><span id="isTyping_'.$user['userid'].'" class="isTyping"></span></p>';
						echo '</div>';
						echo '</div>';
						echo '</li>'; 
					}
					echo '</ul>';
					?>
					</div>
					<!-- <div id="bottom-bar">	
						<button id="addcontact"><i class="fa fa-user-plus fa-fw" aria-hidden="true"></i> <span>Add contact</span></button>
						<button id="settings"><i class="fa fa-cog fa-fw" aria-hidden="true"></i> <span>Settings</span></button>					
					</div> -->
				</div>			
				<div class="content" id="content"> 
					<div class="contact-profile" id="userSection">	
					<?php
					$userDetails = $chat->getUserDetails(1);
					foreach ($userDetails as $user) {										
						echo '<img src="userpics/'.$user['avatar'].'" alt="" />';
						echo '<p>'.$user['username'].'</p>';
							/*
							echo '<div class="social-media">';
								echo '<i class="fa fa-facebook" aria-hidden="true"></i>';
								echo '<i class="fa fa-twitter" aria-hidden="true"></i>';
								 echo '<i class="fa fa-instagram" aria-hidden="true"></i>';
							echo '</div>';*/
					}	
					?>						
					</div>
					<div class="messages" id="conversation">		
					<?php
					echo $chat->getUserChat($_SESSION['userid'], $currentSession);						
					?>
					</div>
					<div class="message-input" id="replySection" style="display: none;">				
						<div class="message-input" id="replyContainer">
							<div class="wrap">
								<input type="text" class="chatMessage" id="chatMessage<?php echo $currentSession; ?>" placeholder="Write your message..." />
								<button class="submit chatButton" id="chatButton<?php echo $currentSession; ?>"><i class="fa fa-paper-plane" aria-hidden="true"></i></button>	
							</div>
						</div>					
					</div>
				</div>
			</div>
		</div>
	<script type="text/javascript">
		$(document).on('change', '.date', function(e){
				//alert("hii");
				var id = this.id;
				var spanId = $(this).closest('span').attr('id');
				var message = $("#"+spanId+' input[type=date]').val();
				if(confirm("Are you sure you want to proceed with this date?")){
					$.ajax({
						url:'chat_action.php',
						method:"POST",
						data:{to_user_id:1, action:'insert_date', message:message},
						dataType: "json",
						success:function(response) {
							//alert("hii");
							/*updateUserChat();
							return false;
							var resp = $.parseJSON(response);			
							$('#conversation').html(resp.conversation);				
							$(".messages").animate({ scrollTop: $('.messages').prop("scrollHeight")}, 1000);*/
						},
						complete:function(data){
							updateUserChat();			
							$(".messages").animate({ scrollTop: $('.messages').prop("scrollHeight")}, 1000);
						},
					});
				}else{
					$("#"+spanId+' input[type=date]').val('');
				}
			});

		$(document).ready(function(){
			//alert($('.messages').height());
			//$(".messages").animate({ scrollTop: $('.messages').height() }, "fast");
			$(document).on('click', ".options", function(e){
				//alert("hii");
				var url = $(this).attr('href');
				var message = $(this).attr('data-message');
				e.preventDefault();
				$.ajax({
					url:url,
					method:"POST",
					data:{to_user_id:1, action:'insert_answer', message:message},
					dataType: "json",
					success:function(response) {
						/*alert("hii");
						return false;*/
						console.log(resp);
						/*var resp = $.parseJSON(response);			
						$('#conversation').html(resp.conversation);	*/
						//alert("in success");
						
					},
					complete:function(data){
						updateUserChat();			
						$(".messages").animate({ scrollTop: $('.messages').prop("scrollHeight")}, 1000);
					},
				});	
				return false;
			});

			$(document).on('click', "#bookingpurpose", function(){
				var spanId=$(this).closest('span').attr('id');
				var message = "input|"+$("#"+spanId+' input[type=text]').val();
				$.ajax({
					url:'chat_action.php',
					method:"POST",
					data:{to_user_id:1, action:'insert_answer', message:message},
					dataType: "json",
					success:function(response) {
						/*alert("hii");
						return false;*/
						console.log(resp);
						/*var resp = $.parseJSON(response);			
						$('#conversation').html(resp.conversation);	*/
						//alert("in success");
						
					},
					complete:function(data){
						updateUserChat();			
						$(".messages").animate({ scrollTop: $('.messages').prop("scrollHeight")}, 1000);
					},
				});
			})
			
		})
	</script>
	<!-- <br>
	<br>	
	<div style="margin:50px 0px 0px 0px;">
		<a class="btn btn-default read-more" style="background:#3399ff;color:white" href="http://www.phpzag.com/build-live-chat-system-with-ajax-php-mysql/">Back to Tutorial</a>		
	</div>	 -->
</div>	
<?php include('footer.php');?>
<?php } else { //echo "hii";
		header("Location:login.php");
		//header('login.php');
	 ?>
		<!-- <br>
		<br>
		<strong><a href="login.php"><h3>Login To Access Chat System</h3></a></strong> -->		
	<?php } ?>