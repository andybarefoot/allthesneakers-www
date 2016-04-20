<?php
require_once '../../includes/db-shoes.php';
require_once '../../includes/dbactions.php';

$sqlQuery="SELECT count(*) AS `brandCount`, `brandID`, `brandName`, `brandTier` 
FROM `sneakers`,`products`,`brands` 
WHERE `shoeBrand`=`brandID` AND `productSneaker`=`shoeID` AND `shoeLive`=1 AND `productLive`=1 
GROUP BY `brandID` 
ORDER BY `brandName` ASC";
$brands=getData($sqlQuery);
?>

<ul>Brand (<a id="brandToggleLink" href="javascript:toggleBrandFilters();">more</a>)
<?
foreach ($brands as $brand) {
	$brandCount=$brand['brandCount'];
	$brandID=$brand['brandID'];
	$brandName=$brand['brandName'];
	$brandTier=$brand['brandTier'];
	if($brandTier==2){
		echo '<li class="brand2">';
	} else{
		echo '<li>';
	}
	echo '<input type="checkbox" value="'.$brandID.'" id="b_'.$brandID.'"/><label for="b_'.$brandID.'">'.$brandName.'</label></li>';
}
?>
</ul>