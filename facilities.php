<?php

$mysqli = new mysqli('localhost', 'root', 'Tr!f0rce', 'pollution');
if ($mysqli->query('DELETE FROM facilities')) {
	if ($mysqli->query('ALTER TABLE facilities AUTO_INCREMENT = 1')) {
		echo "<p>table facility reset</p>";
	} else {
		die("error resetting auto increment");
	}
} else {
	die("error deleting info");
}

$fh = fopen("TRI_2009_US_v09.csv", "r") or die("can't open file");
echo '<html>';
echo '<head><link href="styles.css" type="text/css" rel="stylesheet" /></head><body>';

$counter = 0;
$facilities = array();

//print '<table>';
while ($line = fgetcsv($fh)) {
	if (++$counter == 1) continue;
	if (in_array($line[1], $facilities)) continue;
	//print '<tr>';
	
	// 2 - facility name
	// 3 - street address
	// 4 - city
	// 5 - county
	// 6 - state
	// 7 - zip
	// 8 - latitude
	// 9 - longitude
	/*for ($i = 2; $i < 10; $i++) {
		print '<td>' . htmlentities($line[$i]) . '</td>';
	}*/
	if ($mysqli->query('INSERT INTO facilities SET facility_id="'.$line[1].'", name="'.htmlentities($line[2]).'", address="'.htmlentities($line[3]).'", city="'.$line[4].'", county="'.$line[5].'", state="'.$line[6].'", zip="'.$line[7].'", latitude="'.$line[8].'", longitude="'.$line[9].'"')) {
		$facilities[] = $line[1]; // keep track of unique facility id
	} else {
		echo "<p>there was an error inserting into the table</p>";
		echo $mysqli->error;
		exit();
	}
	//print '</tr>';
}
//print '</table>';

fclose($fh) or die("couldn't close file");
$endtime = microtime(true);
echo "<p>file closed $endtime</p>";
echo "</body></html>";
?>