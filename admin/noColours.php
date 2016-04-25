<?php

require_once '../includes/db-shoes.php';
require_once '../includes/dbactions.php';

for($i=0;$i<count($_POST['unknown']);$i++){
	$newID=$_POST['unknown'][$i]['shoeID'];
	$newColour1=$_POST['unknown'][$i]['colour1'];
	$newColour2=$_POST['unknown'][$i]['colour2'];
	$newColour3=$_POST['unknown'][$i]['colour3'];
	if($newID!=0){
//		echo $newID.'|'.$newColour1.'|'.$newColour2.'|'.$newColour3.'<br>';	
		$sqlStmt="UPDATE `shoes` SET `shoeColour1` = '$newColour1', `shoeColour2` = '$newColour2', `shoeColour3` = '$newColour3' WHERE `shoes`.`shoeID` = '$newID' LIMIT 1 ;";
		updateData($sqlStmt);
//		$sqlStmt="INSERT INTO `db73815_shoes`.`colours` (`colourID` ,`colourIsFilter` ,`colourFilterID` ,`colourName` ,`colourR` ,`colourG` ,`colourB`) VALUES (NULL , '0', '$newColour', '$newString', '0', '0', '0');";
//		$thisColour=insertDataReturnID($sqlStmt);
//		$sqlStmt="DELETE FROM `unknownColours` WHERE `unknownString` = '$newString';";
//		$deletedColour=deleteData($sqlStmt);		
	}
}


$sqlStmt="SELECT * FROM `colours` WHERE `colourIsFilter`=1";
$coloursArray=getData($sqlStmt);

$supportedColoursNames=array();
$supportedColoursIDs=array();

for($i=0;$i<count($coloursArray);$i++){
	array_push($supportedColoursNames,$coloursArray[$i]['colourName']);
	array_push($supportedColoursIDs,$coloursArray[$i]['colourFilterID']);
}


$sqlStmt="SELECT * FROM `shoes` WHERE `shoeThumb` != 'NULL' AND `shoeLive` =1 AND `shoeColour1` =0 AND `shoeColour2` =0 AND `shoeColour3` =0";
$shoes=getData($sqlStmt);

?>

<!doctype html>
<html lang="en"> 
<head>
	<meta charset="UTF-8">
	<title>All The Sneakers</title>
	
	<script type="text/javascript" src="js/search.js"></script>
	<script type="text/javascript" src="js/lightbox.js"></script>

	<link rel="stylesheet" type="text/css" href="styles/general.css" />
	<link rel="stylesheet" type="text/css" href="styles/lightbox.css" />

</head>
<body>
<form action="noColours.php" method="post">
<div id="matches">
	<div id="atsTitle">
		<h1>All the sneakers...</h1>
	</div>
<?
for($i=0;$i<count($shoes);$i++){
/*	echo '<shoe>';
	echo '<id>'.$shoes[$i]['shoeID'].'</id>';
	echo '<brand>'.$shoes[$i]['shoeBrand'].'</brand>';
	echo '<name><![CDATA['.$shoes[$i]['shoeName'].']]></name>';
	echo '<thumb><![CDATA['.$shoes[$i]['shoeThumb'].']]></thumb>';
	echo '<image><![CDATA['.$shoes[$i]['shoeImage'].']]></image>';
	echo '<url><![CDATA['.$shoes[$i]['shoeURL'].']]></url>';
	echo '<desc><![CDATA['.$shoes[$i]['shoeDesc'].']]></desc>';
	echo '<price>'.$shoes[$i]['shoePrice'].'</price>';
	echo '<sport>'.$shoes[$i]['shoeSport'].'</sport>';
	echo '<colour1>'.$shoes[$i]['shoeColour1'].'</colour1>';
	echo '<colour2>'.$shoes[$i]['shoeColour2'].'</colour2>';
	echo '<colour3>'.$shoes[$i]['shoeColour3'].'</colour3>';
	echo '<profile>'.$shoes[$i]['shoeProfile'].'</profile>';
	echo '</shoe>';
 * 
 */
	echo '<div class="shoe">';
	echo '<a href="'.$shoes[$i]['shoeURL'].'" target="_blank"><img src="'.$shoes[$i]['shoeThumb'].'"></a>';
	echo '<select name="unknown['.$i.'][colour1]">';
	echo '<option value="0">No Match</option>';
	for($d=0;$d<count($supportedColoursNames);$d++){
		echo '<option value="'.$supportedColoursIDs[$d].'">';
		echo $supportedColoursNames[$d];
		echo '</option>';
	}
	echo '</select>';
	echo '<select name="unknown['.$i.'][colour2]">';
	echo '<option value="0">No Match</option>';
	for($d=0;$d<count($supportedColoursNames);$d++){
		echo '<option value="'.$supportedColoursIDs[$d].'">';
		echo $supportedColoursNames[$d];
		echo '</option>';
	}
	echo '</select>';
	echo '<select name="unknown['.$i.'][colour3]">';
	echo '<option value="0">No Match</option>';
	for($d=0;$d<count($supportedColoursNames);$d++){
		echo '<option value="'.$supportedColoursIDs[$d].'">';
		echo $supportedColoursNames[$d];
		echo '</option>';
	}
	echo '</select>';
	echo '<input type="hidden" name="unknown['.$i.'][shoeID]" value="'.$shoes[$i]['shoeID'].'" />';
	echo '</div>';
	}?>
</div>
<input type="submit" value="Submit">	
</form>
</body>
</html>
