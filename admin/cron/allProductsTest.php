<?php

// THIS SCHEDULE
$cron=1;

// POSSIBLE GET PARAMETERS
// day (value 0-6) - overides current date to set day 
// all (no value needed) - means all found sneakers will be processed

// include functions
// - function getColours($colourStrs,$localID,$shoeBrand)
// - curlGet($url)
// - returnXPathObject($item)
// - scrapeBetween($item, $start, $end)
// - saveProduct($productArray)
include_once '/nfs/c05/h04/mnt/73815/domains/allthesneakers.com/includes/commonScrapeFunctions.php';

// GET CURRENT DATE
// get Date
$thisDate = date('d/m/Y');
// get Day
if(isset($_GET["day"])){
	$thisDay=intval($_GET["day"]);
}else{
	$thisDay = date('w');
}
$message = 'Day: '.$thisDay;
// getHour
// get Day
if(isset($_GET["hour"])){
	$thisHour=intval($_GET["hour"]);
}else{
	$thisHour = date('G');
}
$message .= "/Hour: ".$thisHour."<br/><br/>";

// DEFINE EMPTY ARRAYS AND VARIABLES
// the product pages found
$productPagesUrls = array();
// the product pages processed
$processedPagesUrls = array();
// the number of products processed
$productCount=0;

// INCLUDE SITE SPECIFIC FILE BASED ON DATE
$sqlQuery="SELECT * , SUBSTRING_INDEX(  `spiderScript` ,  '-', -1 ) AS  'action'
FROM  `spiders` ,  `sites` 
WHERE  `sites`.`siteID` = SUBSTRING_INDEX(  `spiderScript` ,  '-', 1 ) 
AND  `spiders`.`spiderDay` =$thisDay
ORDER BY  `spiders`.`spiderHour` ASC";
$spiders=getData($sqlQuery);

$hours = array();
foreach ($spiders as $spider) {
	$hour=$spider['spiderHour'];
	$action=$spider['action'];
	if($action=='a'){
		$script=$spider['siteSpider'];
	}else{
		$script=$spider['siteClean'];
	}
	$hours[$hour] = $script;
}

$useScript=$hours[$thisHour];
$before=false;
$timesUsed=0;
$timeNo=1;
for($i=0;$i<24;$i++){
	$j=$thisHour+$i;
	if($j>23){
		$before=true;
		$j=$j-24;
	}
	if($hours[$j]==$useScript){
		$timesUsed++;
		if($before)$timeNo++;
	}
}

echo 'Script: '.$useScript.' '.$timeNo.'/'.$timesUsed;
include_once $useScript;

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
if(!isset($_GET["all"])){
	$offset = $totalUrls/$timesUsed;
	$sliceStart = floor($offset*($timeNo-1));
	$sliceEnd = ceil($offset);
	$productPagesUrls = array_slice($productPagesUrls, $sliceStart, $sliceEnd);
}
$message .= "Spidering products: ".$sliceStart."-".($sliceEnd+$sliceStart)."<br/>";
// Process pages
$newShoes=0;
$newSites=0;
while(count($productPagesUrls)>0){
	processPages();
}

//REPORTING
// Print results to browser
echo $message;
$lastProduct=$sliceEnd+$sliceStart;
$sqlQuery="INSERT INTO `db73815_shoes`.`scans` (
`scanID`, `scanTime`, `scanScript`, `scanFound`, `scanUnique`, 
`scanSpidered`, `scanStart`, `scanEnd`, `scanNewShoe`, `scanNewStockist`
) VALUES (
NULL, CURRENT_TIMESTAMP, '$useScript', $productCount, $totalUrls, 
$sliceEnd, $sliceStart, $lastProduct, $newShoes, $newSites
);";
$scanResult=insertData($sqlQuery);


// Send results via email
/*
$to = 'andybarefoot@gmail.com';
$subject = 'All The Sneakers: Spider '.$thisDate;
$headers = "From:  All The Sneakers: Spider\r\n";
$headers .= "Reply-To: andybarefoot@gmail.com\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
if (mail($to, $subject, $message, $headers)) {
  echo 'Your message has been sent.';
} else {
  echo 'There was a problem sending the email.';
}
*/
?>
?>