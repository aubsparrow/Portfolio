<?php 
	session_start();
	
	$userName = $_POST["userName"]; 
	$password = $_POST["password"];
	
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
	
	$sql = "SELECT Salt FROM Users WHERE UserName = \"" . $userName . "\";";
	$result = $conn->query($sql);
	$row = $result->fetch_assoc();
	$salt = $row['Salt'];
	$password = $password.$salt;
	//echo $password . "\r\n";
	$password = hash('sha256', $password);
	//echo $password;

	$sql = "SELECT * FROM Users WHERE UserName = \"" . $userName . "\" AND Password = \"" . $password ."\";";
	$result = $conn->query($sql);
	
	
	if ($result) {
		//echo "Connected";
	} else {
		echo "Not Connected";
	}
	
	if ($result->num_rows > 0) {
		include 'typing_game_page.html';
		#echo "found";
	} else {
		echo "Not Found";
	}
	
	$sql = "SELECT * FROM Words ORDER BY RAND() LIMIT 100;";
	$result = $conn->query($sql);
	
	$wordArray = array();
	
	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {
			 array_push($wordArray, $row["Word"]);
		}
	} else {
		echo "0 results";
	}
	json_encode($wordArray);
	
	$conn->close();
	
	?>