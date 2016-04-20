<?php
require_once '../../includes/db-shoes.php';
require_once '../../includes/dbactions.php';

// get searchquery
$query=filter_input(INPUT_GET, 'q', FILTER_SANITIZE_ENCODED);

$sqlQuery="SELECT brandID, brandName FROM brands WHERE brandName LIKE  '%$query%' LIMIT 0,30";
$brandTerms=getData($sqlQuery);

// $sqlQuery="SELECT shoeID, shoeName, shoeLocalID FROM sneakers WHERE shoeName LIKE '%$query%' GROUP BY shoeName LIMIT 0,30";
// $shoeTerms=getData($sqlQuery);

$sqlQuery="SELECT *, MATCH (`shoeName`,`shoeDesc`) AGAINST ('%$query%' IN NATURAL LANGUAGE MODE) AS score FROM sneakers2 ORDER BY score DESC LIMIT 0,30";
$shoeTerms=getData($sqlQuery);


$sqlQuery="SELECT shoeID, shoeName, shoeLocalID FROM sneakers WHERE shoeLocalID LIKE '$query%' GROUP BY shoeName LIMIT 0,30";
$shoeIDTerms=getData($sqlQuery);

for($i=0;$i<count($brandTerms);$i++){
	if($i==0)echo '<h5>Brand</h5>';
	echo '<a href="?searchB='.urlencode($brandTerms[$i]['brandID']).'">'.$brandTerms[$i]['brandName'].'</a> ';
	if($i==count($brandTerms)-1)echo '<br/>';
}
for($i=0;$i<count($shoeIDTerms);$i++){
	if($i==0)echo '<h5>Product ID</h5>';
	echo '<a href="?searchI='.urlencode($shoeIDTerms[$i]['shoeID']).'&term='.$query.'">'.$shoeIDTerms[$i]['shoeName'].' ('.$shoeIDTerms[$i]['shoeLocalID'].')</a> ';
	if($i==count($shoeIDTerms)-1)echo '<br/>';
}
for($i=0;$i<count($shoeTerms);$i++){
	if($i==0)echo '<h5>Names</h5>';
	if($i==5)echo '<span id="longerResults">';
	echo '<a href="?searchN='.urlencode($shoeTerms[$i]['shoeName']).'&term='.$query.'">'.$shoeTerms[$i]['shoeName'].'</a> ';
	if($i==count($shoeTerms)-1){
		if($i>=5){
			echo '</span>';
		}
		echo '<br/>';
	}
}
if(count($brandTerms)+count($shoeIDTerms)+count($shoeTerms)==0){
	echo '<h5>No results found</h5>';
}
?>