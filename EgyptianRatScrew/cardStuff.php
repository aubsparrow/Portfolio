<?php
	$name = "";
	if (isset($_POST["name"]))
	{
	  $name = $_POST["name"];
	}
	
	$servername = "localhost";

		$susername = "W01259296";

		$spassword = "Elisecs!";

		$dbname = "W01259296";

	
	$conn = new mysqli($servername, $susername, $spassword, $dbname);
	class Game {
		public $player1Name = "";
		public $player2Name = "";
		public $player1Deck = array();
		public $player2Deck = array();
		public $middleDeck = array();
		public $playerTurn = 1;
		public $p1numTriesLeft = 1;
		public $p2numTriesLeft = 1;
		public $p1facePlayed = false;
		public $p2facePlayed = false;
		public $winner = 0;
		public $gameGoing = false;
	}
	
	$game = new Game;
	$playerTurn = 1;
	$playerNum;
	
	function createDeck()
	{
		global $suits, $deck, $values;
		foreach($suits as $suit)
		{
			foreach($values as $value)
			{
				$pushedString = $suit . $value;
				array_push($deck, $pushedString);
			}
		}
	}
	
function playCard() {
		global $game;
		if($game->playerTurn == 1) {
			$temp = $game->player1Deck[0];
			array_push($game->middleDeck, $temp);
			array_splice($game->player1Deck, 0, 1);
			if(substr($temp, -1) == "Q")
			{
				$game->p2numTriesLeft = 2;
				$game->p1numTriesLeft = 1;
				$game->p1facePlayed = true;
				$game->p2facePlayed = false;
			} else if (substr($temp, -1) == "K")
			{
				$game->p2numTriesLeft = 3;
				$game->p1numTriesLeft = 1;
				$game->p1facePlayed = true;
				$game->p2facePlayed = false;
			} else if (substr($temp, -1) == "J")
			{
				$game->p2numTriesLeft = 1;
				$game->p1numTriesLeft = 1;
				$game->p1facePlayed = true;
				$game->p2facePlayed = false;
			} else if (substr($temp, -1) == "A")
			{
				$game->p2numTriesLeft = 4;
				$game->p1numTriesLeft = 1;
				$game->p1facePlayed = true;
				$game->p2facePlayed = false;
			} else
			{
				$game->p2numTriesLeft = 1;
			}
				
		}
		else if($game->playerTurn == 2) {
			
			$temp = $game->player2Deck[0];
			array_push($game->middleDeck, $temp);
			array_splice($game->player2Deck, 0, 1);
			if(substr($temp, -1) == "Q")
			{
				$game->p2numTriesLeft = 1;
				$game->p1numTriesLeft = 2;
				$game->p2facePlayed = true;
				$game->p1facePlayed = false;
			} else if (substr($temp, -1) == "K")
			{
				$game->p2numTriesLeft = 1;
				$game->p1numTriesLeft = 3;
				$game->p2facePlayed = true;
				$game->p1facePlayed = false;
			} else if (substr($temp, -1) == "J")
			{
				$game->p2numTriesLeft = 1;
				$game->p1numTriesLeft = 1;
				$game->p2facePlayed = true;
				$game->p1facePlayed = false;
			} else if (substr($temp, -1) == "A")
			{
				$game->p2numTriesLeft = 1;
				$game->p1numTriesLeft = 4;
				$game->p2facePlayed = true;
				$game->p1facePlayed = false;
			} else
			{
				$game->p1numTriesLeft = 1;
			}
		}
	}
	
	function getBoard(){
		global $game, $conn;
		$sql = "SELECT gameBoard FROM RatScrew WHERE ID = 1;";
		$result = $conn->query($sql);
		if($result->num_rows != 0) {
			$row = $result->fetch_assoc();
			$board = $row['gameBoard'];
			$game = json_decode($board);
		}
	}
	
	function shuffleCards()
	{
		global $suits, $deck, $values;
		for($i=0; $i<1000; $i++)
		{
			$randomNum=rand(0,51);
			$randomNumTwo=rand(0,51);
			$temp = $deck[$randomNum];
			
			$deck[$randomNum] = $deck[$randomNumTwo];
			$deck[$randomNumTwo] = $temp;
		}
	}
	
	function splitDeck()
	{
		global $deck, $game;
		for($i = 0; $i < count($deck); $i++)
		{
			if(($i%2) == 0)
			{
				$temp = $deck[$i];
				array_push($game->player1Deck, $temp);
			}
			else
			{
				$temp = $deck[$i];
				array_push($game->player2Deck, $temp);
			}
		}
	}
	
	function storeDeck()
	{
		global $conn, $game;
		
		$sql = "UPDATE RatScrew SET gameBoard = '" . json_encode($game) . "' WHERE ID = 1;";
		$conn->query($sql);
	}
	
	function slap($playerSlapped)
	{
		global $game;
		if(count($game->middleDeck) >= 2) {
			$topCard = $game->middleDeck[count($game->middleDeck)-1];
			$secondCard = $game->middleDeck[count($game->middleDeck)-2];
			if(substr($topCard, -1) == substr($secondCard, -1)) {
				playerGetsMiddleDeck($playerSlapped);
				$game->p1facePlayed = false;
				$game->p2facePlayed = false;
				$game->p1numTriesLeft = 1;
				$game->p2numTriesLeft = 1;
			}
			else {
				badPlayerSlap($playerSlapped);
			}
		}
		else {
			badPlayerSlap($playerSlapped);
		}
		storeDeck();
	}
	
	function badPlayerSlap($playerSlapped) {
		global $game;
		
		if($playerSlapped == 1) {
			$temp = $game->player1Deck[0];
			array_unshift($game->middleDeck, $temp);
			array_splice($game->player1Deck, 0, 1); 
			
			$temp = $game->player1Deck[0];
			array_unshift($game->middleDeck, $temp);
			array_splice($game->player1Deck, 0, 1); 
		}
		else if($playerSlapped == 2) {
			$temp = $game->player2Deck[0];
			array_unshift($game->middleDeck, $temp);
			array_splice($game->player2Deck, 0, 1); 
			
			$temp = $game->player2Deck[0];
			array_unshift($game->middleDeck, $temp);
			array_splice($game->player2Deck, 0, 1); 
		}
	}
	
	function playerGetsMiddleDeck($winner)
	{
		global $game;
		
		foreach($game->middleDeck AS $card) {
			if($winner == 1) {
				array_push($game->player1Deck, $card);
			}
			else if ($winner == 2) {
				array_push($game->player2Deck, $card);
			}
			array_splice($game->middleDeck, 0, 1);
		}
	}
	
	function deleteBoard() {
		global $conn, $game;
		
		$sql = "DELETE FROM `RatScrew` WHERE 1";
		$conn->query($sql);
	}
	
	function isLastCard($playerClick) {
		global $game;
		
		if($playerClick == 1 AND $game->p1numTriesLeft == 1)
		{
			$temp = $game->player1Deck[0];
			if(substr($temp, -1) != "Q" AND substr($temp, -1) != "K" AND substr($temp, -1) != "A" AND substr($temp, -1) != "J" AND $game->p2facePlayed == true)
			{
				return "isLast";
			}
		}
		else if($playerClick == 2 AND $game->p2numTriesLeft == 1)
		{
			$temp = $game->player2Deck[0];
			if(substr($temp, -1) != "Q" AND substr($temp, -1) != "K" AND substr($temp, -1) != "A" AND substr($temp, -1) != "J" AND $game->p1facePlayed == true)
			{
				return "isLast";
			}
		}
		else {
			return "isNotLast";
		}
	}
	
	if(isset($_GET['action']) and $_GET['action'] == "playerdeckclick"){
		$game = json_decode($_GET['content']);
		playCard();
		if($game->playerTurn == 1)
		{
			if(count($game->player1Deck) == 0) {
				$game->gameGoing = false;
				$game->winner = 2;
				playerGetsMiddleDeck(2);
			}
			else {
				$game->p1numTriesLeft = $game->p1numTriesLeft - 1;
				
				if($game->p1numTriesLeft == 0)
				{
					if ($game->p2facePlayed == true)
					{
						playerGetsMiddleDeck(2);
						$game->p2facePlayed = false;
					}
					$game->playerTurn = 2;
				}
			}
		}else if($game->playerTurn == 2)
		{
			if(count($game->player2Deck) == 0) {
				$game->gameGoing = false;
				$game->winner = 1;
				playerGetsMiddleDeck(1);
			}
			else {
				$game->p2numTriesLeft = $game->p2numTriesLeft - 1;
				
				if($game->p2numTriesLeft == 0)
				{
					if ($game->p1facePlayed == true)
					{
						playerGetsMiddleDeck(1);
						$game->p1facePlayed = false;
					}
					$game->playerTurn = 1;
				}
			}
		}
		storeDeck();
		echo json_encode($game);
	} else if(isset($_GET['action']) and $_GET['action'] == "refresh"){
		getBoard();
		echo json_encode($game);
	} else if(isset($_GET['action']) and $_GET['action'] == "isLastCard"){
		$game = json_decode($_GET['content']);
		$playerClicked = json_decode($_GET['player']);
		echo isLastCard($playerClicked);
	} else if(isset($_GET['action']) and $_GET['action'] == "slap"){
		$game = json_decode($_GET['content']);
		$playerSlapped = json_decode($_GET['player']);
		slap($playerSlapped);
		storeDeck();
		echo json_encode($game);
	}
	else if(isset($_GET['action']) and $_GET['action'] == "delete"){
		deleteBoard();
	}
	else{
	
	$deck = array();
	$suits = array("spades", "diamonds", "clubs", "hearts");
	$values = array("A", "2", "3", "4", "5", "6", "7", "8", "9", "10", "J", "Q", "K");
		
	$sql = "SELECT * FROM RatScrew WHERE ID = 1;";
	$result = $conn->query($sql);
	
	if($result->num_rows == 0)
	{
		createDeck();
		shuffleCards();
		splitDeck();
		$game->player1Name = $name;
		$playerNum = 1;
		$sql = "INSERT INTO RatScrew(ID, gameBoard) VALUES (1, '" . json_encode($game) . "');";
		$conn->query($sql);
		storeDeck();
		$playerNum = 1;
	}
	else
	{
		$sql = "SELECT gameBoard FROM RatScrew WHERE ID = 1;";
		$result = $conn->query($sql);
		$row = $result->fetch_assoc();
		$needToDecode = $row['gameBoard'];
		$game = json_decode($needToDecode);
		if(strlen($game->player2Name) <= 0) {
			$game->player2Name = $name;
			$playerNum = 2;
			$game->gameGoing = true;
			storeDeck();
		}
	}
	?>
<link href="style.css" rel="stylesheet" type="text/css" />

<html>
	<body>
		<h2 id="heading">Welcome to Egyptian Rat Screw!</h2>
		<div id="middledeck">
			<div id="middlecard1" class="middlecard"> </div>
			<div id="middlecard2" class="middlecard"> </div>
			<div id="middlecard3" class="middlecard"> </div>
			<div id="middlecard4" class="middlecard"> </div>
			<div id="middlecard5" class="middlecard"> </div>
		</div>
		<button id="slap" onclick="slap()">Slap!</button><br>
		<div id="player1">
		<div id="playerdeck" onclick="playCard(1)"><img src="images/blue_back.png"></div>
		<div id="player1label">Player 1: </div>
		</div>
		<div id="player2">
		<div id="playerdeck2" onclick="playCard(2)"><img src="images/blue_back.png"></div>
		<div id="player2label">Player 2: </div>
		</div>
		<h3 id="playerTurn">Player Turn: </h3>
		<h1 id="playerWin"></h1>
		<!-- TO REMOVE --><button id="delete" onclick="deleteBoard()">Delete Game</button><br>
		<script>
			
			var timer = setInterval(timerTick, 100);
			var cards = new Array();
			var game = <?php echo json_encode($game); ?>;
			var playerNum = parseInt(<?php echo $playerNum ?>);
			refreshBoard();
			
			function playCardRequest() {
				var xmlhttp = new XMLHttpRequest();
				xmlhttp.onreadystatechange = function() {
					if(this.readyState == 4 && this.status == 200){
						game = JSON.parse(this.responseText);
						refreshBoard();
					}
				}
				xmlhttp.open("GET","cardStuff.php?action=playerdeckclick&content=" + JSON.stringify(game), true);
				xmlhttp.send();
			}
			
			function playCard(clickedDeck) {
				if(game.player1Deck.length > 0 && playerNum == 1 && game.playerTurn == 1 && clickedDeck == 1 && game.gameGoing)
				{
					var xmlhttp = new XMLHttpRequest();
					xmlhttp.onreadystatechange = function() {
						if(this.readyState == 4 && this.status == 200){
							if(this.responseText == "isLast") {
								var tempMiddle = game.middleDeck;
								var tempP1 = game.player1Deck;
								tempMiddle.push(tempP1[0]);
								
								document.getElementById("player1label").innerHTML = "Player 1: " + game.player1Name + "<br>Cards left: " + (tempP1.length - 1);
								
								if(tempMiddle.length == 0) {
									for (var i = 1; i <= 5; i++) {
										document.getElementById("middlecard" + i).innerHTML = "";
									}
								}
								else {
									if(tempMiddle.length < 5) {
										for(var i = 0; i < tempMiddle.length; i++) {
											document.getElementById("middlecard" + (i+1)).innerHTML = "<img src=\"cards/" + tempMiddle[i] + ".png\" height=\"170\" width=\"120\">";
										}
									}
									else {
										for(var i = 0; i < 5; i++) {
											document.getElementById("middlecard" + (5-i)).innerHTML = "<img src=\"cards/" + tempMiddle[tempMiddle.length - 1 - i] + ".png\" height=\"170\" width=\"120\">";
										}
									}
								}
								
								wait(2000);
								window.alert(tempMiddle);
							}
							else {
								playCardRequest();
							}
						}
					}
					xmlhttp.open("GET","cardStuff.php?action=isLastCard&content=" + JSON.stringify(game) + "&player=" + playerNum, true);
					xmlhttp.send();
					
				}
				else if (game.player2Deck.length > 0 && playerNum == 2 && game.playerTurn == 2 && clickedDeck == 2 && game.gameGoing)
				{
					var xmlhttp = new XMLHttpRequest();
					xmlhttp.onreadystatechange = function() {
						if(this.readyState == 4 && this.status == 200){
							if(this.responseText == "isLast") {
								var tempMiddle = game.middleDeck;
								var tempP2 = game.player2Deck;
								tempMiddle.push(tempP2[0]);
								
								document.getElementById("player2label").innerHTML = "Player 2: " + game.player2Name + "<br>Cards left: " + (tempP2.length - 1);
								
								if(tempMiddle.length == 0) {
									for (var i = 1; i <= 5; i++) {
										document.getElementById("middlecard" + i).innerHTML = "";
									}
								}
								else {
									if(tempMiddle.length < 5) {
										for(var i = 0; i < tempMiddle.length; i++) {
											document.getElementById("middlecard" + (i+1)).innerHTML = "<img src=\"cards/" + tempMiddle[i] + ".png\" height=\"170\" width=\"120\">";
										}
									}
									else {
										for(var i = 0; i < 5; i++) {
											document.getElementById("middlecard" + (5-i)).innerHTML = "<img src=\"cards/" + tempMiddle[tempMiddle.length - 1 - i] + ".png\" height=\"170\" width=\"120\">";
										}
									}
								}
								
								wait(2000);
								window.alert(tempMiddle);
							}
							else {
								playCardRequest();
							}
						}
					}
					xmlhttp.open("GET","cardStuff.php?action=isLastCard&content=" + JSON.stringify(game) + "&player=" + playerNum, true);
					xmlhttp.send();
				}
			}
			
			function refreshBoard(){
				document.getElementById("player1label").innerHTML = "Player 1: " + game.player1Name + "<br>Cards left: " + game.player1Deck.length;
				
				document.getElementById("player2label").innerHTML = "Player 2: " + game.player2Name + "<br>Cards left: " + game.player2Deck.length;
				
				document.getElementById("playerTurn").innerHTML = "Player Turn: " + game.playerTurn;
				
				if(game.middleDeck.length == 0) {
					for (var i = 1; i <= 5; i++) {
						document.getElementById("middlecard" + i).innerHTML = "";
					}
				}
				else {
					if(game.middleDeck.length < 5) {
						for(var i = 0; i < game.middleDeck.length; i++) {
							document.getElementById("middlecard" + (i+1)).innerHTML = "<img src=\"cards/" + game.middleDeck[i] + ".png\" height=\"170\" width=\"120\">";
						}
					}
					else {
						for(var i = 0; i < 5; i++) {
							document.getElementById("middlecard" + (5-i)).innerHTML = "<img src=\"cards/" + game.middleDeck[game.middleDeck.length - 1 - i] + ".png\" height=\"170\" width=\"120\">";
						}
					}
				}
				
			}
			
			function timerTick(){
				var xmlhttp = new XMLHttpRequest();
				xmlhttp.onreadystatechange = function() {
					if(this.readyState == 4 && this.status == 200){
						game = JSON.parse(this.responseText);
						refreshBoard();
						if(!game.gameGoing && game.winner != 0)
						{
							endGame();
						}
					}
				}
				xmlhttp.open("GET","cardStuff.php?action=refresh", true);
				xmlhttp.send();
			}
			
			function slap(){
				if(game.gameGoing) {
					var xmlhttp = new XMLHttpRequest();
						xmlhttp.onreadystatechange = function() {
						if(this.readyState == 4 && this.status == 200){
							game = JSON.parse(this.responseText);
							refreshBoard();
						}
					}
					xmlhttp.open("GET","cardStuff.php?action=slap&content=" + JSON.stringify(game) + "&player=" + playerNum, true);
					xmlhttp.send();
				}
			}
			
			function deleteBoard(){
				var xmlhttp = new XMLHttpRequest();
					xmlhttp.onreadystatechange = function() {
					if(this.readyState == 4 && this.status == 200){
						document.getElementById("heading").innerHTML = "Game was deleted";
					}
				}
				xmlhttp.open("GET","cardStuff.php?action=delete", true);
				xmlhttp.send();
			}
			
			function endGame() {
				document.getElementById("playerWin").innerHTML = "<strong>PLAYER " + game.winner + " WINS!!!</strong>";
				document.getElementById("playerTurn").innerHTML = "";
				clearInterval(timer);
			}
			
			function wait(ms){
				var start = new Date().getTime();
				var end = start;
				while(end < start + ms) {
					end = new Date().getTime();
				}
			}
				
		</script>
		

	</body>
</html>
<?php } ?>