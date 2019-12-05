<?php
	
	session_start();
	$userName = $_POST["userName"]; 
	$password = $_POST["password"];
	$salt = "";
	
	//echo " Username" . $userName;


	$_SESSION["userName"] = $userName; //session variables
	$_SESSION["password"] = $password;
	
	$servername = "localhost";

		$username = "id11159664_aubsparrow";

		$dbpassword = "Ma!ne20!6";

		$dbname = "id11159664_typinggame";

	$conn = new mysqli($servername, $username, $dbpassword, $dbname);
	
	if($conn->connect_error)
	{
		die("Connection failed");
	}
	
	$sql = "SELECT * FROM Users WHERE UserName = \"" . $userName . "\";";
	$result = $conn->query($sql);
	
	if($result->num_rows == 0)
	{
		for($i=0; $i<32; $i++)
		{
			$randomNum=rand(10,99);
			$salt=$salt.$randomNum;
		}
		
		$password=$password . $salt;
		//echo $password . "\r\n";
		$password=hash('sha256', $password);
		//echo $password;
		
		$sql = "INSERT INTO Users(ID, UserName, Password, Salt) VALUES (NULL,\"" . $userName . "\",\"" . $password . "\", \"" . $salt . "\");";
		
		if ($conn->query($sql) == TRUE) {
			include 'typing_game_page.html';
			//echo "Inserted";
		} else {
			echo "Error Inserting " . $conn->error;
		}
	}
	else
	{
		echo "Username is already taken";
	}
	$conn->close();