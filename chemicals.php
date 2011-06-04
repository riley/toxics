<?php

// for categories field => classification | metal | metal category | carcinogen 
$mysqli = new mysqli('localhost', 'root', 'Tr!f0rce', 'pollution');
if ($mysqli->query('DELETE FROM chemicals')) {
	if ($mysqli->query('ALTER TABLE chemicals AUTO_INCREMENT = 1')) {
		echo "<p>table chemicals reset</p>";
	} else {
		die("error resetting auto increment");
	}
} else {
	die("error deleting info in chemicals");
}

$fh = fopen("TRI_2009_US_v09.csv", "r") or die("can't open file");
$chem_ids = array();

echo '<html>';
echo '<head><link href="styles.css" type="text/css" rel="stylesheet" /></head><body>';
echo '<p>file loaded</p>';
$counter = 0;
echo "<table>";
echo "<tr><td>name</td><td>classification</td><td>metal</td><td>metal category</td><td>carcinogen</td></tr>";

while ($line = fgetcsv($fh)) {
	if ($counter++ == 0) continue;
	if (in_array($line[24], $chem_ids)) continue;
	
	// classification (PBT/NON-PBT) = 1
	// metal (YES/NO) = 2
	// metal_category (0/1) = 4
	// carcinogen (YES/NO) = 8
	echo "<tr>";
	echo "<td>".htmlentities($line[23])."</td><td>".$line[26]."</td><td>".$line[27]."</td><td>".$line[28]."</td><td>".$line[29]."</td>";
	echo "</tr>";
	
	$pbt = ($line[26] == "PBT") ? 'T' : 'F'; // classification
	$metal = ($line[27] == "YES") ? 'T' : 'F'; // metal
	$metal_category = ($line[28] == "1") ? 'T' : 'F';   // metal category
	$carcinogen = ($line[29] == "YES") ? 'T' : 'F'; // carcinogen
	
	if ($mysqli->query('INSERT INTO chemicals SET name="'.htmlentities($line[23]).'", compound_id="'.$line[24].'", pbt="'.$pbt.'", metal="'.$metal.'", metal_category="'.$metal_category.'", carcinogen="'.$carcinogen.'"')) {
		$chem_ids[] = $line[24];
	} else {
		echo "the insert didn't work";
		echo $mysqli->error;
		exit();
	}
}

echo "</table>";

if ($result = $mysqli->query('SELECT * FROM chemicals')) {
	echo "<p>".$result->num_rows." rows inserted</p>";
} else {
	echo "error selecting from chemicals";
	echo $mysqli->error;
	exit();
}

echo '</p>file closed</p>';
echo "</body></html>";
$mysqli->close();

exit();
?>