<?php

// POSSIBLE GET PARAMETERS
// day (value 0-6) - overides current date to set day 
// all (no value needed) - means all found sneakers will be processed

// include functions
// - function getColours($colourStrs,$localID,$shoeBrand)
// - curlGet($url)
// - returnXPathObject($item)
// - scrapeBetween($item, $start, $end)
// - saveProduct($productArray)
if(basename(__DIR__)=="cronTest"){
	include_once '../../includes/commonScrapeFunctions.php';
}else if(basename(__DIR__)=="cron"){
	include_once '../includes/commonScrapeFunctions.php';
}else if(basename(__DIR__)=="admin"){
	include_once '../../includes/commonScrapeFunctions.php';
}

// GET CURRENT DATE
// get Date
date_default_timezone_set('UTC');
$thisDate = date('d/m/Y');
// get Day
if(isset($_GET["day"])){
	$thisDay=intval($_GET["day"]);
}else{
	$thisDay = date('w');
}
$message = 'Day: '.$thisDay;
// getHour
$thisHour = date('G');
$message .= "/Hour: ".$thisHour."<br/><br/>";

// Get requested spider
$chosenSpider = intval($_POST["spiderID"]);
$sqlQuery='SELECT * FROM `sites` WHERE `siteID` = '.$chosenSpider.' AND `siteSpider`!=""';
$thisSpider=getData($sqlQuery);
if(count($thisSpider)>0){
	$thisSpiderFile = $thisSpider[0]['siteSpider'];
}else{
	$thisSpiderFile=false;
}
// Get requested scope
$sInt = intval($_POST["startInt"]);
$nInt = intval($_POST["numberInt"]);

// DEFINE EMPTY ARRAYS AND VARIABLES
// the product pages found
$productPagesUrls = array();
// the product pages processed
$processedPagesUrls = array();
// the number of products processed
$productCount=0;

// get live spiders
$sqlQuery='SELECT * FROM `sites` where `siteSpider`!="" ORDER BY `siteName` ASC';
$spiders=getData($sqlQuery);

echo '<form action="spiderTest.php" method="POST">';
echo '<select name="spiderID">';
	echo '<option value="0">Select a site to spider</option>';
//list spiders in drop-down 
for($i=0;$i<count($spiders);$i++){
	$sID = $spiders[$i]['siteID'];
	$sName = $spiders[$i]['siteName'];
	if($sID==$chosenSpider){
		echo '<option selected value="'.$sID.'">'.$sName.'</option>';
	} else {
		echo '<option value="'.$sID.'">'.$sName.'</option>';
	}
}
echo '</select>';
echo 'Start: <input type="text" value="'.$sInt.'" name="startInt">';
echo 'Number: <input type="text" value="'.$nInt.'" name="numberInt">';
echo '<input type="submit" value="Submit">';
echo '</form>';

// IF SPIDER WAS CHOSEN
if($thisSpiderFile){
	echo 'SPIDER CHOSEN: '.$thisSpiderFile.'<br/><br/>';
	// INCLUDE SITE SPECIFIC FILE BASED ON REQUIRED SPIDER
	$fileLocStr = '../../cron/'.$thisSpiderFile;
	include_once $fileLocStr;
	// RUN THE SPIDER
	$message .= "Pages searched:<br/>";
	// Spidering the specified URLs
	while(count($spiderPagesUrls)>0){
		findProducts();
	}
	$message .= "<br/>Total products: ".$productCount."<br/>";
	// Removing duplicates from Product URL array and reindex
	$productPagesUrls = array_values(array_unique($productPagesUrls));
	$totalUrls = count($productPagesUrls);
	$message .= "Unique products: ".$totalUrls."<br/>";
	// Define subset to process this time
	$sliceStart=$sInt;
	$sliceEnd=$nInt;
	$productPagesUrls = array_slice($productPagesUrls, $sliceStart, $sliceEnd);
	$message .= "Spidering products: ".$sliceStart."-".($sliceEnd+$sliceStart)."<br/>";
	// Process pages
	while(count($productPagesUrls)>0){
		processPages();
	}

	//REPORTING
	// Print results to browser
	echo $message;
}else{
	echo 'NO SPIDER CHOSEN';
}



?>