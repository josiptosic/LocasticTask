<?php #Result view script
	session_start();
	require 'Entities.php';

	$fileName = ""; 
	$raceName = ""; 
	$raceDate = "";
		
	if (isset(	$_SESSION['fileName']) #Global variables flow control
		&& isset( $_SESSION['raceName'])
		&& isset( $_SESSION['raceDate'])) {
		$fileName = $_SESSION['fileName'];
		$raceName = $_SESSION['raceName'];
		$raceDate = $_SESSION['raceDate'];
	 } else {
		$fileName = $_POST['fileName'];
		$raceName = $_POST['raceName'];
		$raceDate = $_POST['raceDate'];
		$_SESSION['fileName'] = $_POST['fileName'];
		$_SESSION['raceName'] = $_POST['raceName'];
		$_SESSION['raceDate'] = $_POST['raceDate'];
	}
	
	$file = fopen($fileName, "r+") or die("ERR: Unable to open file!");
	$resultsArray = array();
	$textBuffer = fgetcsv ($file);
	
	while(!feof($file)){ #File validation procedure
		$textBuffer = fgetcsv ($file);
		if (!empty($textBuffer[0])||!empty($textBuffer[1])||!empty($textBuffer[2])) {
			continue;
		} else {
			unset($textBuffer);
			unset($resultsArray);
			header("ImportForm.php") or die("Couldn't load import form script!");
			echo "[ERR] File isn't formatted properly or some of the values are missing! 
					Check the file or pick another before you restart the process!";
			exit();
			break;
		}
	}

	if(!empty($resultsArray)){ #File data population
		while(!feof($file)){
			$textBuffer = fgetcsv ($file);
			array_push($resultsArray, $textBuffer);
		}
	}
	
	
	foreach($resultsArray as $obj){ #Array data population
		$results = new Results();
		if($obj[0]&&$obj[1]&&$obj[2]){
			$results->fullName = $obj[0];
			$results->distance = $obj[1];
			$results->raceTime = ParseTime($obj[2]);
			array_push($resultsArray, $results);
		} else {
			die("Unable to add entry! All fields should contain valid data!");
			continue;
		}
	}
	
	$mediumRace = new Race($fileName, $raceName, $raceDate, "medium");
	$longRace = new Race($fileName, $raceName, $raceDate, "long");
	if(isset($_SESSION['editPlacement'])) 
		(strcmp($_SESSION['editDistance'], "medium") == 0)? 
		$mediumRace->ReplaceResult($_SESSION['editPlacement']) 
		: $longRace->ReplaceResult($_SESSION['editPlacement']) ;

	
	$_SESSION["mediumRace"] = $mediumRace; #Recalculation and rendering of files
	$_SESSION["longRace"] = $longRace;
	$mediumRace->CalculatePlacement("medium");
	$mediumRace->CalculateAverageTime("medium");
	$mediumRace->PrintResults("medium");
	
	echo "<br>";
	
	$longRace->CalculatePlacement("long");
	$mediumRace->CalculateAverageTime("medium");
	$longRace->PrintResults("long");
	
	$raceArray = array();
	array_push($raceArray, $mediumRace);
	array_push($raceArray, $longRace);
	FileWrite($raceArray, $file);
	fclose($file);


?>