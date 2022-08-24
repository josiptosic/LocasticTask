<?php #Entities OOP class base
	
	class Race{
		
		public $raceName;
		public $date;
		public $resultsArray;
		
		function __construct($fileName, $inRaceName, $inDate, $inDistance){
			$this->SetRaceName($inRaceName);
			$this->SetRaceDate($inDate);
			$this->resultsArray = array();
			$this->ParseResults($fileName, $inDistance);
			$this->CalculateAverageTime($inDistance);
			$this->CalculatePlacement($inDistance);
		}
		
		function SetRaceName($inRaceName){
			$this->raceName = $inRaceName;
		}
		
		function SetRaceDate($inRaceDate){
			$this->date = strtotime($inRaceDate);
		}
		
		function ParseResults ($fileName, $inDistance){
			$file = fopen($fileName, "r") or die("ERR: Unable to open file: [$filename]");
			$tempArray = array();
			#$this->resultsArray = array();

			while(!feof($file)){
				
				$textBuffer = fgetcsv ($file);
				
				array_push($tempArray ,$textBuffer);
			}

			foreach($tempArray as $obj){
				if (strcmp($obj[1], $inDistance) == 0) {
					$results = new Results($obj[0], $obj[1], $this->ParseTime($obj[2]));
					array_push($this->resultsArray, $results);
					}
			}
		}
		
		function ParseTime($strTime){
			$seconds = 0;
			$minutes = 0;
			$hours = 0;
			sscanf($strTime, "%d:%d:%d", $hours, $minutes, $seconds);
			return $seconds + ($minutes * 60) + ($hours * 3600);
		}
		
		function CalculateAverageTime($inDistance){
			$cumulative = 0;
			$iterator = 0;

			foreach($this->resultsArray as $resultsObj){
				if(strcmp($inDistance, $resultsObj->distance) == 0){
					$cumulative += $resultsObj->raceTime;
				$iterator++;
				}
			}
			
			if ($iterator > 0) return $cumulative / $iterator;
			else echo "There are no relevant results in the array.";
		}
			
		function my_sort($a,$b){
			$firstElement = intval($a->raceTime);
			$secondElement = intval($b->raceTime);
			if ($firstElement == $secondElement) return 0;
			else return ($firstElement < $secondElement)? -1:1;
		}
		
		function CalculatePlacement($inDistance){
			uasort($this->resultsArray, array($this, 'my_sort'));;
			$iterator = 1;
			foreach($this->resultsArray as $resultsObj){
				if (strcmp($inDistance, $resultsObj->distance) == 0){
					$resultsObj->placement = $iterator;
					$iterator++;
				}
			}
			return $iterator;
		}
		
		function ReplaceResult($inPlacement){
			$iterator = 1;
			foreach($this->resultsArray as $resultsObj){
				if ($iterator == $inPlacement){
					$resultsObj->fullName = $_POST['editFullName'];
					$resultsObj->raceTime = $this->ParseTime($_POST['editRaceTime']);
					break;
				} else $iterator += 1;
			}
			
		}
		
		function PrintResults($inDistance){
			echo "<p>";
			echo "<h3>$this->raceName race $inDistance distance</h3><br>";
			echo "Average: ".date("h:i:s", ($this->CalculateAverageTime($inDistance)))."<br>";
			echo "<br>";
			echo "<table style=\"font-size: 12px; border: 1px solid; \">
					<tr>
						<th>Full name</th>
						<th>Finish time</th>
						<th>Placement</th>
						<th></th>
					</tr>";
			
			foreach($this->resultsArray as $obj){
				echo "<tr>";
				echo "<td>$obj->fullName</td>";
				$totalSeconds = intval($obj->raceTime);
				$hours = floor($totalSeconds / 3600);
				$minutes = floor($totalSeconds / 60 % 60);
				$seconds = floor($totalSeconds % 60);
				$time = strtotime("$hours:$minutes:$seconds");
				echo "<td>".date("h:i:s", $time)."</td>";
				echo "<td>".$obj->placement."</td>";
				echo "<td>	<form 	action = \"Edit.php\"
									method = \"post\">
								<input 	type = \"submit\" 
										value = \"Edit\">
								<input 	type = \"hidden\" 
										name = \"editPlacement\" 
										value = \"".$obj->placement."\">
								<input 
										type = \"hidden\" 
										name = \"editRaceObj\" 
										value = \"".$this->raceName."\">
								<input 
										type = \"hidden\" 
										name = \"editDistance\" 
										value = \"".$obj->distance."\">
							</form></td></tr>";
				
			}
			echo "</table>";
			echo "</p>";
		}
	}
	
	class Results{
		
		public $fullName;
		public $raceTime;
		public $distance;
		public $placement;
		
		function __construct($inFullName, $inDistance, $inRaceTime){
			$this->fullName = $inFullName;
			$this->raceTime = $inRaceTime;
			$this->distance = $inDistance;
		}
		
		function SetPlacement($inPlacement){
			$this->placement = $inPlacement;
		}
		
	}
	
	function FileWrite($raceArray, $file){
		$entryArray = array();
		foreach($raceArray as $raceObj){
			foreach ($raceObj->resultsArray as $resultsObj){
				$totalSeconds = intval($resultsObj->raceTime);
				$hours = floor($totalSeconds / 3600);
				$minutes = floor($totalSeconds / 60 % 60);
				$seconds = floor($totalSeconds % 60);
				$time = ("$hours:$minutes:$seconds");
				array_push($entryArray, $resultsObj->fullName.",".$resultsObj->distance .",".$time);
			}
		}
		$strData = implode("\n", $entryArray);
		fwrite($file, $strData);
	}
	
	
?>