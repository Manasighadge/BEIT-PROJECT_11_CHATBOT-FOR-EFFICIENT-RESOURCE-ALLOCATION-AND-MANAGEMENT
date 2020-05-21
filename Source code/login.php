<!DOCTYPE html>
<!--[if lt IE 7 ]> <html lang="en" class="no-js ie6 lt8"> <![endif]-->
<!--[if IE 7 ]>    <html lang="en" class="no-js ie7 lt8"> <![endif]-->
<!--[if IE 8 ]>    <html lang="en" class="no-js ie8 lt8"> <![endif]-->
<!--[if IE 9 ]>    <html lang="en" class="no-js ie9"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--> <html lang="en" class="no-js"> <!--<![endif]-->
    <head>
        <meta charset="UTF-8" />
        <!-- <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">  -->
        <title>ChatBot</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
        <meta name="description" content="Login and Registration Form with HTML5 and CSS3" />
        <meta name="keywords" content="html5, css3, form, switch, animation, :target, pseudo-class" />
        <meta name="author" content="Codrops" />
        <link rel="shortcut icon" href="../favicon.ico"> 
        <link rel="stylesheet" type="text/css" href="css/demo.css" />
        <link rel="stylesheet" type="text/css" href="css/style2.css" />
		<link rel="stylesheet" type="text/css" href="css/animate-custom.css" />
    </head>

<?php 
SESSION_START();
include('header.php');
$loginError = '';
if (!empty($_POST['username']) && !empty($_POST['pwd'])) {
	include ('Chat.php');
	$chat = new Chat();
	$user = $chat->loginUsers($_POST['username'], $_POST['pwd']);
	/*echo '<pre>';
	print_r($user);
	exit;*/	
	if(!empty($user)) {
		$_SESSION['username'] = $user[0]['username'];
		$_SESSION['userid'] = $user[0]['userid'];
		$_SESSION['is_admin'] = $user[0]['is_admin'];
		$chat->updateUserOnline($user[0]['userid'], 1);
		$lastInsertId = $chat->insertUserLoginDetails($user[0]['userid']);
		$_SESSION['login_details_id'] = $lastInsertId;
		if($_SESSION['is_admin']==false){
			
			$questions = $chat->get_questions();
			/*echo '<pre>';
			print_r($questions);exit();*/
			//exit;
			/*echo '<pre>';
			echo $chatMessage;
			//echo $result->hardware;
			print_r(json_decode($result,true));
			exit;*/
			/*$questions = file_get_contents('questions.php');
			echo '<pre>';
			print_r($questions);exit;*/
			header("Location:user_index.php");
		}else{

			header("Location:index.php");
		}
	} else {
		$loginError = "Invalid username or password!";
	}
}

?>
<?php include('container.php');?>

 <body>
        <div class="container">
            <!-- Codrops top bar -->
            <div class="codrops-top">
               
               
                <div class="clr"></div>
            </div><!--/ Codrops top bar -->
            <header>
                <h1>Login Chat Bot</span></h1>
				
            </header>
            <section>				
                <div id="container_demo" >
                    <!--<a class="hiddenanchor" id="toregister"></a>-->
                    <a class="hiddenanchor" id="tologin"></a>
                    <div id="wrapper">
                        <div id="login" class="animate form">
                            <form  method="post" autocomplete="on"> 
                                <h1>Log in</h1> 
								<?php if ($loginError ) { ?>
									<div class="alert alert-warning"><?php echo $loginError; ?></div>
								<?php } ?>
                                <p> 
                                    <label for="username" class="uname" data-icon="u" > Your username </label>
                                    <input id="username" name="username" required="required" type="text" placeholder="myusername"/>
                                </p>
                                <p> 
                                    <label for="password" class="youpasswd" data-icon="p"> Your password </label>
                                    <input id="pwd" name="pwd" required="required" type="password" placeholder="eg. X8df!90EO" /> 
                                </p>
                              
                                <p class="login button"> 
                                    <input type="submit" value="Login" /> 
								</p>
								<p class="change_link">  
									Not a member ?
									<a href="register.php" class="to_register"> Go and Register</a>
								</p>
                             
                            </form>
                        </div>

                      
						
                    </div>
                </div>  
            </section>
        </div>
    </body>
</html>






<?php include('footer.php');?>






