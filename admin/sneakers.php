<?php
require_once '../../includes/db-shoes.php';
require_once '../../includes/dbactions.php';

// set pagination
$pageNo=intval($_GET["p"]);
if($pageNo<1)$pageNo=1;

$pageSize=40;
$offset=($pageNo-1)*$pageSize;

$sqlQuerySelect="SELECT count(*) AS `productsNum`, `shoeID`, `productURL`, `shoeThumb`, `shoeName`, `brandName`, MIN(`productPrice`) AS 'productPrice'
FROM `sneakers`,`products`,`brands` 
WHERE  `shoeBrand`=`brandID` AND `productSneaker`=`shoeID` AND `shoeLive`=1 AND `productLive`=1 ";

// get brand filters
$brands=$_GET["b"];
$brandsArray=explode(",", $brands);
$bNum=0;
$bStr=" AND (";
for($i=0;$i<count($brandsArray);$i++){
	if(intval($brandsArray[$i])>0){
		if($i!=0)$bStr.=" OR ";
		$bStr.="`brandID`=".$brandsArray[$i];
		$bNum++;
	}
}
$bStr.=") ";

if($bNum!=0)$sqlQueryFilter.=$bStr;

// get color filters
$colors=$_GET["c"];
$colorsArray=explode(",", $colors);
$cNum=0;
$cStr=" AND (";
for($i=0;$i<count($colorsArray);$i++){
	if(intval($colorsArray[$i])>0){
		if($i!=0)$cStr.=" OR ";
		$cStr.="`shoeColour1`=".$colorsArray[$i];
		$cNum++;
	}
}
$cStr.=") ";

if($cNum!=0)$sqlQueryFilter.=$cStr;

$sqlQueryGroup="GROUP BY `shoeLocalID` ";
//$sqlQueryOrder="ORDER BY `productsNum` DESC, `sneakers`.`shoeID` ASC, `products`.`productPrice` DESC ";
$sqlQueryOrder="ORDER BY `sneakers`.`shoeID` DESC, `products`.`productPrice` DESC ";
$sqlQueryLimit="LIMIT ".$offset.",".$pageSize;

$sqlQuery=$sqlQuerySelect.$sqlQueryFilter.$sqlQueryGroup.$sqlQueryOrder.$sqlQueryLimit;


// $sqlQuery="SELECT * FROM `shoes`,`brands` WHERE  `shoeBrand`=`brandID` AND `shoeLive`=1 ORDER BY `shoes`.`shoeColour1` DESC,`shoes`.`shoeColour2` DESC,`shoes`.`shoeColour3` DESC LIMIT 0,30";
// $sqlQuery="SELECT * FROM `shoes`,`brands` WHERE  `shoeBrand`=`brandID` AND `shoeLive`=1 AND `shoeBrand`='1' ORDER BY `shoes`.`shoeColour1` DESC,`shoes`.`shoeColour2` DESC,`shoes`.`shoeColour3` DESC";
$shoes=getData($sqlQuery);

// echo $sqlQuery; 
for($i=0;$i<count($shoes);$i++){
	$urlProductName = preg_replace('/^-+|-+$/', '', strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $shoes[$i]['shoeName'])));
	$urlBrandName = preg_replace('/^-+|-+$/', '', strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $shoes[$i]['brandName'])));
	$sneakerName=$shoes[$i]['shoeName'];
	while(strlen($sneakerName)>50){
		$sneakerName=substr($sneakerName,0,strrpos($sneakerName,' ')).'...';
	}
	echo '<div class="shoe" id="'.$shoes[$i]['shoeID'].'">';
	echo '<a class="fancybox fancybox.ajax" rel="group" href="/sneaker/'.$shoes[$i]['shoeID'].'/'.$urlBrandName.'/'.$urlProductName.'"><img alt="'.$shoes[$i]['brandName'].': '.preg_replace("/[^A-Za-z0-9 ]/", '', $sneakerName).'" src="http://www.allthesneakers.com'.$shoes[$i]['shoeThumb'].'"></a>';
	echo '<div class="shoeDetails"><span class="shoeName">'.$sneakerName.'</span><br/>';
	echo '<span class="shoeBrand">'.$shoes[$i]['brandName'].'</span>';
	echo '<span class="shoePrice">$'.$shoes[$i]['productPrice'].'</span>';
	echo '</div><div class="adminLayer">ADMIN</div></div>';
}
?>
