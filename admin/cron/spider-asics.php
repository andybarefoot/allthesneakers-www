<?php

// site: http://www.asicstiger.com/
$site=25;

// Declaring arrays
// the listing pages that might contain links to products
$spiderPagesUrls = array(
	"http://www.asicstiger.com/us/en-us/mens/c/mens-shoes?q=%3Arelevance&show=All",
	"http://www.asicstiger.com/us/en-us/womens/c/womens-shoes?q=%3Arelevance&show=All"
	);

//custom function for finding products on this site
function findProducts(){
	global $message;
	global $productCount;
	global $spiderPagesUrls;
	global $productPagesUrls;
	$x=1;	
	$thisPageUrl = array_shift($spiderPagesUrls);
	$message .= $thisPageUrl."<br/>";
	echo $thisPageUrl."<br>Products found:<br/>";
	$thisPageSrc = curlGet($thisPageUrl);	// Requesting initial results page
	$thisPageXPath = returnXPathObject($thisPageSrc);	// Instantiating new XPath DOM object
	$thisPageUrls = $thisPageXPath->query('//a[@class="productMainLink"]/@href');	// Querying for href attributes of pagination
	// If results exist
	if ($thisPageUrls->length > 0) {
		// For each results page URL
		for ($i = 0; $i < $thisPageUrls->length; $i++) {
			$iUrl=$thisPageUrls->item($i)->nodeValue;
			if (strpos($iUrl,'http://') !== 0)$iUrl='http://www.asicstiger.com'.$iUrl;
			// check if a product page
			if (strpos($iUrl,'/p/') !== false){
				// check not already found
				if(!in_array($iUrl, $productPagesUrls)) {
					$productPagesUrls[] = $iUrl;	// Build results page URL and add to $resultsPages array
					echo $productCount.": ".$iUrl."<br>";
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
// ID
	$id = $productPageXpath->query('//p[@class="single-prod-meta"]/span');
	if ($id->length > 0) {
		$productArray["id"] = $id->item(0)->nodeValue;
	}	
// BRAND
	$productArray["brand"] = "Asics";
// NAME	
	$name = $productPageXpath->query('//h1[@class="single-prod-title"]');
	if ($name->length > 0) {
		$productArray["name"] = $name->item(0)->nodeValue;
	}	
// COLOUR
	$colour = $productPageXpath->query('//meta[@property="product:color"]/@content');
	if ($colour->length > 0) {
		$productArray["colour"] = explode("/",$colour->item(0)->nodeValue);
	}	
// DESC
	$desc = $productPageXpath->query('//h4[text()="Design Notes"]/following::p');
	if ($desc->length > 0) {
		$productArray["desc"] = $desc->item(0)->nodeValue;
	}	
// PRICE
	$price = $productPageXpath->query('//meta[@property="product:price:amount"]/@content');
	if ($price->length > 0) {
		$productArray["price"] = $price->item(0)->nodeValue;
	}	
// SPORT
	$productArray["sport"] = "";
// GENDER
	$productArray["gender"] = "";
// IMAGE
	$productImage = $productPageXpath->query('//img[@class="rsImg"]/@src');
	if ($productImage->length > 0) {
		$imageUrl = $productImage->item(0)->nodeValue;	// Add URL to variable
		if (strpos($imageUrl,'http://www.onitsukatiger.com') !== 0)$imageUrl='http://www.onitsukatiger.com:'.$imageUrl;
		$productArray["image"] = $imageUrl;
	}
	$productPageXpath = NULL;	// Nulling $booksPageXPath object
	$processedPagesUrls[] = $thisPageUrl;	// Build results page URL and add to $resultsPages array
	saveProduct($productArray);
	showProduct($productArray);
	sleep(rand(1, 3)/100);	// Being polite and sleeping
}

?>