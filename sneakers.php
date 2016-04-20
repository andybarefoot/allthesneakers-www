<?php
require_once '../includes/db-shoes.php';
require_once '../includes/dbactions.php';

// declare variables
// sneakers per page
$pageSize=40;
// regexp for checking string only contains digits and ,
$re01 = '/^[0-9,]+$/'; 

// parse GET parameters and make safe
// intro
$introNo=intval($_GET["intro"]);
// page number
$pageNo=intval($_GET["p"]);
// brand filters
$brands=$_GET["b"];
if(!preg_match($re01,$brands))$brands="";
// colour filters
$colors=$_GET["c"];
if(!preg_match($re01,$colors))$colors="";
// gender filters
$genders=$_GET["g"];
if(!preg_match($re01,$genders))$genders="";
// model filters
$models=$_GET["m"];
if(!preg_match($re01,$models))$models="";
// search term - freetext search
$search=mysql_real_escape_string($_GET["search"]);
if($search===undefined)$search="";
if($search!=="")$introNo=0;
// print "Debug Search:".$search."<br>";
// order terms
$order=substr(mysql_real_escape_string($_GET["order"]), 0, 1);


// generate pagination from page number
if($pageNo<1)$pageNo=1;
$offset=($pageNo-1)*$pageSize;
// generate brand array
$brandsArray=explode(",", $brands);
// generate colour array
$colorsArray=explode(",", $colors);
// generate gender array
$gendersArray=explode(",", $genders);
// generate model array
$modelsArray=explode(",", $models);


// Is user searching for a product ID?
$searchID = false;
// Is there a search term
if($search!==""){
	// Is it a single 'word'
	if(strpos(trim($search), ' ') === false){
		$sqlQuery="SELECT * FROM `sneakers` WHERE `shoeLocalID` = '".$search."'";
		$shoesID=getData($sqlQuery);
		if(count($shoesID)!=0){
			$searchID = true;
		}
	}
}


// SQL STRING
$sqlQuerySelect="";
// SQL STRING - SELECT 
$sqlQuerySelect.="SELECT count(*) AS `productsNum`, `shoeID`, `productURL`, `shoeThumb`, `shoeName`, `brandName`, MIN(`productPrice`) AS 'productPrice', MAX(`productID`) AS 'productID'";
// SQL STRING - SELECT FOR SEARCH
if(($search!=="") && !$searchID){
	$sqlQuerySelect.=", MATCH (`shoeName`,`shoeDesc`) AGAINST ('".$search."' IN NATURAL LANGUAGE MODE) AS score";
}
// SQL STRING - FROM
$sqlQuerySelect.=" FROM `sneakers`,`products`,`brands` ";
// SQL STRING - WHERE
$sqlQuerySelect.="WHERE  `shoeBrand`=`brandID` AND `productSneaker`=`shoeID` AND `shoeIsSneaker`=1 AND `shoeLive`=1 AND `productLive`=1 ";
// SQL STRING - WHERE - BRAND FILTERS
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
// SQL STRING - WHERE - COLOUR FILTERS
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
// SQL STRING - WHERE - GENDER FILTERS
$gNum=0;
$gStr=" AND (";
for($i=0;$i<count($gendersArray);$i++){
	if(intval($gendersArray[$i])>0){
		if($i!=0)$gStr.=" OR ";
		$gStr.="`shoeGender`=".$gendersArray[$i];
		$gNum++;
	}
}
$gStr.=") ";
if($gNum!=0)$sqlQueryFilter.=$gStr;
// SQL STRING - WHERE - MODEL FILTERS
$mNum=0;
$mStr=" AND (";
for($i=0;$i<count($modelsArray);$i++){
	if(intval($modelsArray[$i])>0){
		if($i!=0)$mStr.=" OR ";
		$mStr.="`shoeModel`=".$modelsArray[$i];
		$mNum++;
	}
}
$mStr.=") ";
if($mNum!=0)$sqlQueryFilter.=$mStr;
// SQL STRING - WHERE - SEARCH
if(($search!=="") && !$searchID){
	$sqlQueryFilter.="AND  `shoeID` IN (SELECT  `shoeID` FROM  `sneakers` WHERE MATCH (`shoeName` ,  `shoeDesc`) AGAINST ('".$search."' IN NATURAL LANGUAGE MODE)) ";
}
if($searchID){
	$sqlQueryFilter.="AND  `shoeLocalID` =  '".$search."' ";
}
// p - price High to Low
// P - price Low to High
// n - number of retailers High to Low
// l - latest (inc new retailers for existing sneakers)
// d - default (old order)
// default - latest (new sneakers only)
if(($search!=="") && !$searchID){
	$sqlQueryOrder="ORDER BY score DESC ";
}else if($order=="p"){
	$sqlQueryOrder="ORDER BY `productPrice` DESC, `sneakers`.`shoeID` DESC ";
}else if($order=="P"){
	$sqlQueryOrder="ORDER BY `productPrice` ASC, `sneakers`.`shoeID` DESC ";
}else if($order=="n"){
	$sqlQueryOrder="ORDER BY `productsNum` DESC, `sneakers`.`shoeID` DESC ";
}else if($order=="l"){
	$sqlQueryOrder="ORDER BY `productID` DESC ";
}else if($order=="d"){
	$sqlQueryOrder="ORDER BY `sneakers`.`shoeID` DESC, `products`.`productPrice` DESC ";
}else{
	$sqlQueryOrder="ORDER BY ((10 * `sneakers`.`shoeCred`)+`sneakers`.`shoeID`) DESC ";
}

$sqlQueryGroup="GROUP BY `shoeLocalID` ";
$sqlQueryLimit="LIMIT ".$offset.",".$pageSize;

$sqlQuery=$sqlQuerySelect.$sqlQueryFilter.$sqlQueryGroup.$sqlQueryOrder.$sqlQueryLimit;
// print "SQL:".$sqlQuery."<br>";



// $sqlQuery="SELECT * FROM `shoes`,`brands` WHERE  `shoeBrand`=`brandID` AND `shoeLive`=1 ORDER BY `shoes`.`shoeColour1` DESC,`shoes`.`shoeColour2` DESC,`shoes`.`shoeColour3` DESC LIMIT 0,30";
// $sqlQuery="SELECT * FROM `shoes`,`brands` WHERE  `shoeBrand`=`brandID` AND `shoeLive`=1 AND `shoeBrand`='1' ORDER BY `shoes`.`shoeColour1` DESC,`shoes`.`shoeColour2` DESC,`shoes`.`shoeColour3` DESC";
$shoes=getData($sqlQuery);

// get quick links
$sqlQuery="SELECT * FROM `models`,`brands` 
WHERE `modelBrand`=`brandID` AND `modelFilter`=1 
ORDER BY `brandName` ASC, `modelName` ASC";
$models=getData($sqlQuery);

// add introbox
if($introNo==1){
?>
<div id="introBox">
	<div id="introContent">
		<h2>...a search engine of sneaker sites.</h2>
		<form id="searchForm" action="/" method="GET">
			<input type="search" name="search" id="searchField" placeholder="Search..." />
		</form>
		<div id="introLinks">
			<h3>Popular Models:</h3>
<?
$thisID=0;
foreach ($models as $model) {
	$modelID=$model['modelID'];
	$modelName=$model['modelName'];
	$brandID=$model['brandID'];
	$brandName=$model['brandName'];
	if($thisID!=$brandID){
		if($thisID!=0){
			echo '</p>';
		}
		echo '<p>'.$brandName.':';
		$thisID=$brandID;
	}
	echo '<a class="quickLink" id="q_'.$modelID.'">'.$modelName.'</a> ';
}
		echo '</p>';
?>
		</div>
	</div>
</div>
<?
}


//echo $sqlQuery; 
for($i=0;$i<count($shoes);$i++){
	$urlProductName = preg_replace('/^-+|-+$/', '', strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $shoes[$i]['shoeName'])));
	$urlBrandName = preg_replace('/^-+|-+$/', '', strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $shoes[$i]['brandName'])));
	$sneakerName=$shoes[$i]['shoeName'];
	while(strlen($sneakerName)>50){
		$sneakerName=substr($sneakerName,0,strrpos($sneakerName,' ')).'...';
	}
	echo '<div class="shoe" id="'.$shoes[$i]['shoeID'].'">';
	echo '<a class="fancybox fancybox.ajax" rel="group" href="/sneaker/'.$shoes[$i]['shoeID'].'/'.$urlBrandName.'/'.$urlProductName.'"><img alt="'.$shoes[$i]['brandName'].': '.preg_replace("/[^A-Za-z0-9 ]/", '', $sneakerName).'" src="'.$shoes[$i]['shoeThumb'].'"></a>';
	echo '<div class="shoeDetails"><span class="shoeName">'.$sneakerName.'</span><br/>';
	echo '<span class="shoeBrand">'.$shoes[$i]['brandName'].'</span>';
	echo '<span class="shoePrice">$'.$shoes[$i]['productPrice'].'</span>';
	echo '</div></div>';
}

if((count($shoes)==0)&&($pageNo==1)){
	echo '<div class="shoe"><div id="noResults">NO MATCHING SNEAKERS FOUND</div></div>';
}
?>
