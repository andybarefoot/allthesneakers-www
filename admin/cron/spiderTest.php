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
$thisHour = date('G');
$message .= "/Hour: ".$thisHour."<br/><br/>";

// DEFINE EMPTY ARRAYS AND VARIABLES
// the product pages found
$productPagesUrls = array();
// the product pages processed
$processedPagesUrls = array();
// the number of products processed
$productCount=0;

// INCLUDE SITE SPECIFIC FILE BASED ON DATE
include_once 'spider-adidas.php';

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
	$offset = $totalUrls/24;
	$sliceStart = floor($offset*$thisHour);
	$sliceEnd = ceil($offset);
	$productPagesUrls = array_slice($productPagesUrls, $sliceStart, $sliceEnd);
}
$message .= "Spidering products: ".$sliceStart."-".($sliceEnd+$sliceStart)."<br/>";
// Process pages
while(count($productPagesUrls)>0){
	processPages();
}

//REPORTING
// Print results to browser
echo $message;
// Send results via email
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

?>