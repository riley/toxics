<?php

$mysqli = new mysqli('localhost', 'root', 'Tr!f0rce', 'pollution');
if ($mysqli->query('DELETE FROM facility_chemical')) {
	echo "<p>facility_chemical table reset</p>";
} else {
	echo $mysqli->error;
	exit();
}

$facility_ids = array();
$counter = 0;
$fh = fopen("TRI_2009_US_v09.csv", "r") or die("can't open file");
while ($line = fgetcsv($fh)) {
	if ($counter++ == 0) continue;
	$facility = $line[1];
	$facility_ids[$facility][] = array($line[24], $line[82]);
}

foreach ($facility_ids as $id=>$chemicals) { // where $id is the id from "facilities" table
	//echo "<ul>$id";
	foreach ($chemicals as $chemical) {
		if ($result = $mysqli->query('SELECT id FROM facilities WHERE facility_id="'.$id.'"')) {
			while ($row = $result->fetch_object()) {
				$this_id = $row->id;
			}
		} else {
			echo "there was something wrong with the SELECT query\n";
			echo $mysqli->error;
			exit();
		}
		if ($result = $mysqli->query('SELECT id FROM chemicals WHERE compound_id="'.$chemical[0].'"')) {
			while ($row = $result->fetch_object()) {
				$chem_id = $row->id;
			}
		} else {
			echo $mysqli->error;
			exit();
		}
		//echo "<li>".$chemical." - ".$chem_id."</li>";
		if ($mysqli->query('INSERT INTO facility_chemical SET facilityid="'.$this_id.'", chemicalid="'.$chem_id.'", amount="'.$chemical[1].'"')) {
		
		} else {
			echo "<h1>The insert failed here</h1>";
			echo $mysqli->error;
			exit();
		}
	}
	//echo "</ul>";
}
echo "done " . date("D, d M Y H:i:s T");
$mysqli->close();
exit();

?>