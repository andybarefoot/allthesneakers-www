<?php

require_once '../includes/db-shoes.php';
require_once '../includes/dbactions.php';

for($i=0;$i<count($_POST['unknown']);$i++){
	$newString=$_POST['unknown'][$i]['string'];
	$newColour=$_POST['unknown'][$i]['colour'];
	if($newColour!=0){
		$sqlStmt="INSERT INTO `db73815_shoes`.`colours` (`colourID` ,`colourIsFilter` ,`colourFilterID` ,`colourName` ,`colourR` ,`colourG` ,`colourB`) VALUES (NULL , '0', '$newColour', '$newString', '0', '0', '0');";
		$thisColour=insertDataReturnID($sqlStmt);
		$sqlStmt="DELETE FROM `unknownColours` WHERE `unknownString` = '$newString';";
		$deletedColour=deleteData($sqlStmt);		
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


$sqlQuery="SELECT * FROM `shoes`,`unknownColours` WHERE  `shoeBrand`=`unknownBrand` AND `shoeLocalID`=`unknownLocalID` ORDER BY `shoes`.`shoeID` ASC";
$shoes=getData($sqlQuery);

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
<form action="unknownColours.php" method="post">
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
	echo '<span class="shoeName">'.$shoes[$i]['shoeName'].'</span><br/>';
	echo '<span class="shoeBrand"><input type="text" name="unknown['.$i.'][string]" value="'.$shoes[$i]['unknownString'].'" /></span>';
	echo '<select name="unknown['.$i.'][colour]">';
	echo '<option value="0">No Match</option>';
	for($d=0;$d<count($supportedColoursNames);$d++){
		echo '<option value="'.$supportedColoursIDs[$d].'">';
		echo $supportedColoursNames[$d];
		echo '</option>';
	}
	echo '</select>';
	echo '</div>';
	}?>
</div>
<input type="submit" value="Submit">	
</form>
</body>
</html>
