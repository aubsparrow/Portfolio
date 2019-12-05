<?php
		session_start();
		if (!isset($_SESSION['userName']))
		{
			die("Error with session");
		}
		$_SESSION['theScore'] = $_GET['score'];
		$servername = "localhost";

		$username = "id11159664_aubsparrow";

		$dbpassword = "Ma!ne20!6";

		$dbname = "id11159664_typinggame";

	$conn = new mysqli($servername, $username, $dbpassword, $dbname);
	
	if($conn->connect_error)
	{
		die("Connection failed");
	}

	echo "<center><h1>High Scores</h1></center>";
	echo "<h4>Welcome, " . $_SESSION['userName'] . ". Your score was: " . $_SESSION['theScore'] . "</h4>";
	$selectuserid = "SELECT ID From Users WHERE UserName = \"" . $_SESSION['userName'] . "\";";
	//echo $selectuserid;
	$getuserID = $conn->query($selectuserid);
	$row = $getuserID->fetch_assoc();
	$userID = $row['ID'];
	//echo $userID
	
	$sqlinsert = "INSERT INTO Scores(ScoreID, UserID, Score) VALUES (NULL , " . $userID . " , " . $_SESSION['theScore'] . ");";
	if ($conn->query($sqlinsert) === TRUE) {
    		 //echo "Inserted\n";
	} else {
    		echo "Error: " . $conn->error;
	}
	$sqlgettopten = "SELECT UserName, Score FROM Scores S INNER JOIN Users U ON U.ID = S.UserID ORDER BY Score DESC LIMIT 10;";

	$toptenresult = $conn->query($sqlgettopten);
	$index = 1;
	echo "<h3>High Scores</h3>";
	if($toptenresult->num_rows > 0){
		echo "<table>";
			echo"<tr>";
				echo "<th>Place</th>";
				echo "<th>Username</th>";
				echo "<th>Score</th>";
			echo"</tr>";
		while($row = mysqli_fetch_assoc($toptenresult)){
			
			echo"<tr>";
				echo "<td>" . $index . "</td>";
				echo "<td>" . $row['UserName'] . "</td>";
				echo "<td>" . $row['Score'] . "</td>";
			echo"</tr>";
			$index++;
		}
		echo"</table>";
	}else{
		echo("Query failed" . mysqli_error($conn));
	}
	
	
	$conn->close();
	
	?>
