<?php

// site: http://store.kix-files.com
$site = "17";

// Declaring arrays
// the listing pages that might contain links to products
$spiderPagesUrls = array(
	"http://store.kix-files.com/collections/all?page=1",
	"http://store.kix-files.com/collections/all?page=2",
	"http://store.kix-files.com/collections/all?page=3",
	"http://store.kix-files.com/collections/all?page=4",
	"http://store.kix-files.com/collections/all?page=5",
	"http://store.kix-files.com/collections/all?page=6",
	"http://store.kix-files.com/collections/all?page=7",
	"http://store.kix-files.com/collections/all?page=8",
	"http://store.kix-files.com/collections/all?page=9",
	"http://store.kix-files.com/collections/all?page=10",
	"http://store.kix-files.com/collections/all?page=11",
	"http://store.kix-files.com/collections/all?page=12",
	"http://store.kix-files.com/collections/all?page=13",
	"http://store.kix-files.com/collections/all?page=14",
	"http://store.kix-files.com/collections/all?page=15",
	"http://store.kix-files.com/collections/all?page=16",
	"http://store.kix-files.com/collections/all?page=17",
	"http://store.kix-files.com/collections/all?page=18",
	"http://store.kix-files.com/collections/all?page=19",
	"http://store.kix-files.com/collections/all?page=20",
	"http://store.kix-files.com/collections/all?page=21",
	"http://store.kix-files.com/collections/all?page=22"
	);

//custom function for finding products on this site
function findProducts(){
	print "Finding from:";
	$x=1;	
	global $productCount;
	global $spiderPagesUrls;
	global $productPagesUrls;
	$thisPageUrl = array_shift($spiderPagesUrls);
	print $thisPageUrl."<br>Products found:<br/>";
	$thisPageSrc = curlGet($thisPageUrl);	// Requesting initial results page
	$thisPageXPath = returnXPathObject($thisPageSrc);	// Instantiating new XPath DOM object
	$thisPageUrls = $thisPageXPath->query('//div[contains(concat(" ", normalize-space(@class), " "), " thumbnail ")]/a/@href');	// Querying for href attributes of pagination
	// If results exist
	if ($thisPageUrls->length > 0) {
		// For each results page URL
		for ($i = 0; $i < $thisPageUrls->length; $i++) {
			$iUrl=$thisPageUrls->item($i)->nodeValue;
			if (strpos($iUrl,'http://') !== 0)$iUrl='http://store.kix-files.com'.$iUrl;
			// check if a product page
			if (strpos($iUrl,'/products/') !== false){
				// check not already found
				if(!in_array($iUrl, $productPagesUrls)) {
					$productPagesUrls[] = $iUrl;	// Build results page URL and add to $resultsPages array
					print $productCount.": ".$iUrl."<br>";
					$productCount++;
				}
			}
		}
	}
	echo "Finished finding on this page<br/><br/>";
}

function processPages(){
	global $site;
	global $productPagesUrls;
	global $processedPagesUrls;
	$productArray = array();
	$thisPageUrl = array_shift($productPagesUrls);
	$productPage = curlGet($thisPageUrl);
	$productPageXpath = returnXPathObject($productPage);	// Instantiating new XPath DOM object	$resultsPageSrc = curlGet($resultsPage);	// Requesting results page
// SITE
	$productArray["site"] = $site;
// URL	
	$productArray["url"] = $thisPageUrl;
// NAME	
	$name = $productPageXpath->query('//h1[@itemprop="name"]');
	if ($name->length > 0) {
		$productArray["name"] = $name->item(0)->nodeValue;
	}	
// BRAND
	$productArray["brand"] = $productArray["name"];
// ID
// COLOUR
	$idCol = $productPageXpath->query('//p[@class="custom_meta"]');
	if ($idCol->length > 0) {
		foreach($idCol as $idCols){
			if(strpos($idCols->nodeValue, "Item Number:")!==false){
				$productArray["id"] = substr($idCols->nodeValue, 12);
			}
			if(strpos($idCols->nodeValue, "Color:")!==false){
				$productArray["colour"] =  explode("/",substr($idCols->nodeValue, 7));
			}
		}
	}	

// DESC
	$productArray["desc"] =  "";
// PRICE
	$price = $productPageXpath->query('//span[@itemprop="price"]/@content');
	if ($price->length > 0) {
		$productArray["price"] = $price->item(0)->nodeValue;
	}	
// SPORT
	$productArray["sport"] = "";
// GENDER
	$genderStr = "";
	$productArray["gender"] = $genderStr;
// IMAGE
	$productImage = $productPageXpath->query('//a[@class="fancybox"]/@href');
	if ($productImage->length > 0) {
		$imageUrl = $productImage->item(0)->nodeValue;	// Add URL to variable
		if (strpos($imageUrl,'http://') !== 0)$imageUrl='http:'.$imageUrl;
		$productArray["image"] = $imageUrl;
	}
	$productPageXpath = NULL;	// Nulling $booksPageXPath object
	$processedPagesUrls[] = $thisPageUrl;	// Build results page URL and add to $resultsPages array
	if($productArray["price"]!=""){
		saveProduct($productArray);
	}
//	showProduct($productArray);
	unset($productArray);
	sleep(rand(1, 3)/100);	// Being polite and sleeping
}

?>