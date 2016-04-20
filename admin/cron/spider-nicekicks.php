<?php

// site: http://shopnicekicks.com/
$site = "18";

// Declaring arrays
// the listing pages that might contain links to products
$spiderPagesUrls = array(
 	"http://shopnicekicks.com/collections/new-arrivals?page=0",
 	"http://shopnicekicks.com/collections/new-arrivals?page=1",
	"http://shopnicekicks.com/collections/new-arrivals?page=2",
	"http://shopnicekicks.com/collections/new-arrivals?page=3",
	"http://shopnicekicks.com/collections/new-arrivals?page=4",
	"http://shopnicekicks.com/collections/new-arrivals?page=5",
	"http://shopnicekicks.com/collections/new-arrivals?page=6",
	"http://shopnicekicks.com/collections/new-arrivals?page=7",
	"http://shopnicekicks.com/collections/new-arrivals?page=8",
	"http://shopnicekicks.com/collections/new-arrivals?page=9",
	"http://shopnicekicks.com/collections/new-arrivals?page=10",
	"http://shopnicekicks.com/collections/new-arrivals?page=11",
	"http://shopnicekicks.com/collections/new-arrivals?page=12",
	"http://shopnicekicks.com/collections/new-arrivals?page=13",
	"http://shopnicekicks.com/collections/new-arrivals?page=14",
	"http://shopnicekicks.com/collections/new-arrivals?page=15",
	"http://shopnicekicks.com/collections/new-arrivals?page=16",
	"http://shopnicekicks.com/collections/new-arrivals?page=17",
	"http://shopnicekicks.com/collections/new-arrivals?page=18",
	"http://shopnicekicks.com/collections/new-arrivals?page=19",
	"http://shopnicekicks.com/collections/new-arrivals?page=20",
	"http://shopnicekicks.com/collections/new-arrivals?page=21",
	"http://shopnicekicks.com/collections/new-arrivals?page=22",
	"http://shopnicekicks.com/collections/new-arrivals?page=23",
	"http://shopnicekicks.com/collections/new-arrivals?page=24",
	"http://shopnicekicks.com/collections/new-arrivals?page=25",
	"http://shopnicekicks.com/collections/new-arrivals?page=26",
	"http://shopnicekicks.com/collections/new-arrivals?page=27",
	"http://shopnicekicks.com/collections/new-arrivals?page=28",
	"http://shopnicekicks.com/collections/new-arrivals?page=29",
	"http://shopnicekicks.com/collections/new-arrivals?page=30",
	"http://shopnicekicks.com/collections/new-arrivals?page=31",
	"http://shopnicekicks.com/collections/new-arrivals?page=32",
	"http://shopnicekicks.com/collections/new-arrivals?page=33",
	"http://shopnicekicks.com/collections/new-arrivals?page=34",
	"http://shopnicekicks.com/collections/new-arrivals?page=35",
	"http://shopnicekicks.com/collections/new-arrivals?page=36",
	"http://shopnicekicks.com/collections/new-arrivals?page=37",
	"http://shopnicekicks.com/collections/new-arrivals?page=38",
	"http://shopnicekicks.com/collections/new-arrivals?page=39",
	"http://shopnicekicks.com/collections/new-arrivals?page=40"
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
	$thisPageUrls = $thisPageXPath->query('//div[@itemprop="itemListElement"]/a/@href');	// Querying for href attributes of pagination
	// If results exist
	if ($thisPageUrls->length > 0) {
		// For each results page URL
		for ($i = 0; $i < $thisPageUrls->length; $i++) {
			$iUrl=$thisPageUrls->item($i)->nodeValue;
			if (strpos($iUrl,'http://') !== 0)$iUrl='http://shopnicekicks.com/'.$iUrl;
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
// COLOUR
	$colName = $productPageXpath->query('//h1[@itemprop="name"]');
	if ($colName->length > 0) {
		$productArray["name"] = strstr($colName->item(0)->nodeValue, '-', true);
		$productArray["colour"] = explode("/",strstr($colName->item(0)->nodeValue, '-'));
	}	
// BRAND
	$brand = $productPageXpath->query('//p[@class="vendor"]/span/a');
	if ($brand->length > 0) {
		$productArray["brand"] = $brand->item(0)->nodeValue;
	}	
// ID
// DESC
	$productArray["desc"] =  "";
	$idDesc = $productPageXpath->query('//div[@itemprop="description"]/p');
	if ($idDesc->length > 0) {
		foreach($idDesc as $idDescs){
			if(strpos($idDescs->nodeValue, "Style Code:")!==false){
				$productArray["id"] = substr($idDescs->nodeValue, 11);
			}else{
				$productArray["desc"] .=  $idDescs->nodeValue;
			}
		}
	}	
// PRICE
	$price = $productPageXpath->query('//span[@class="current_price"]');
	if ($price->length > 0) {
		$productArray["price"] = $price->item(0)->nodeValue;
	}	
// SPORT
	$productArray["sport"] = "";
// GENDER
	$genderStr = "";
	$productArray["gender"] = $genderStr;
// IMAGE
	$productImage = $productPageXpath->query('//ul[@class="slides"]/li/a[@class="fancybox"]/@href');
	if ($productImage->length > 0) {
		$imageUrl = $productImage->item(0)->nodeValue;	// Add URL to variable
		if (strpos($imageUrl,'http://') !== 0)$imageUrl='http:'.$imageUrl;
		$productArray["image"] = $imageUrl;
	}
	$productPageXpath = NULL;	// Nulling $booksPageXPath object
	$processedPagesUrls[] = $thisPageUrl;	// Build results page URL and add to $resultsPages array
	saveProduct($productArray);
//	showProduct($productArray);
	unset($productArray);
	sleep(rand(1, 3)/100);	// Being polite and sleeping
}

?>