<?php
require_once '../../includes/db-shoes.php';
require_once '../../includes/dbactions.php';

$sqlQuery="SELECT * FROM `models`,`brands` 
WHERE `modelBrand`=`brandID` AND `modelFilter`=1 
ORDER BY `brandName` ASC, `modelName` ASC";
$models=getData($sqlQuery);
?>

<ul>Model
<?
$thisID=0;
foreach ($models as $model) {
	$modelID=$model['modelID'];
	$modelName=$model['modelName'];
	$brandID=$model['brandID'];
	$brandName=$model['brandName'];
	if($thisID!=$brandID){
		echo '<li class="subHeader">'.$brandName.'</li>';
		$thisID=$brandID;
	}
	echo '<li><input type="checkbox" value="'.$modelID.'" id="m_'.$modelID.'"/><label for="m_'.$modelID.'">'.$modelName.'</label></li>';
}
?>
</ul>