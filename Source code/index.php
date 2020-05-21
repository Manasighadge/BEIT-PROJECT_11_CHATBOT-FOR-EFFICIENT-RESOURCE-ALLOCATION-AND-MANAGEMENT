<!DOCTYPE html>
<html>
<head>
	<title>Login</title>
</head>
<body>
	<h1>Form validation</h1>
	<form action="validation.php" method="post" name="myform">
		<label>Moodle id</label><br>
		<input type="tel" name="moodle" placeholder="Enter Moodle id"><br>
		<label>Name</label><br>
		<input type="text" name="name" placeholder="Enter your name"><br>
		<label>CGPA</label><br>
		<input type="number" step="any" name="cgpa" placeholder="Enter CGPA"><br>
		<label>KT</label><br>
		<input type="number" name="kt" placeholder="Live KTs"><br>
		<label>Mobile number</label><br>
		<input type="tel" name="mobile" placeholder="Enter your mobile number"><br><br>
		<input type="submit" name="submit" value="SUBMIT">
		
	</form>
</body>
</html>