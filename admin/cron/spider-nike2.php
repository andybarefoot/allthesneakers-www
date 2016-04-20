<?php

// site: http://www.nike.com/us/en_us/
$site = "5";

// Declaring arrays
// the listing pages that might contain links to products
$spiderPagesUrls = array(
	"http://store.nike.com/html-services/gridwallData?gridwallPath=sportswear-shoes%2FbrkZc8eZ1js&country=US&lang_locale=en_US&sortOrder=viewAll%7Casc",
	"http://store.nike.com/html-services/gridwallData?country=US&lang_locale=en_US&gridwallPath=sportswear-shoes/brkZc8eZ1js&pn=2&sortOrder=viewAll|asc",
	"http://store.nike.com/html-services/gridwallData?country=US&lang_locale=en_US&gridwallPath=sportswear-shoes/brkZc8eZ1js&pn=3&sortOrder=viewAll|asc",
	"http://store.nike.com/html-services/gridwallData?country=US&lang_locale=en_US&gridwallPath=sportswear-shoes/brkZc8eZ1js&pn=4&sortOrder=viewAll|asc",
	"http://store.nike.com/html-services/gridwallData?country=US&lang_locale=en_US&gridwallPath=sportswear-shoes/brkZc8eZ1js&pn=5&sortOrder=viewAll|asc",
	"http://store.nike.com/html-services/gridwallData?country=US&lang_locale=en_US&gridwallPath=sportswear-shoes/brkZc8eZ1js&pn=6&sortOrder=viewAll|asc",
	"http://store.nike.com/html-services/gridwallData?country=US&lang_locale=en_US&gridwallPath=sportswear-shoes/brkZc8eZ1js&pn=7&sortOrder=viewAll|asc",
	"http://store.nike.com/html-services/gridwallData?country=US&lang_locale=en_US&gridwallPath=sportswear-shoes/brkZc8eZ1js&pn=8&sortOrder=viewAll|asc",
	"http://store.nike.com/html-services/gridwallData?country=US&lang_locale=en_US&gridwallPath=sportswear-shoes/brkZc8eZ1js&pn=9&sortOrder=viewAll|asc",
	"http://store.nike.com/html-services/gridwallData?country=US&lang_locale=en_US&gridwallPath=sportswear-shoes/brkZc8eZ1js&pn=10&sortOrder=viewAll|asc",
	"http://store.nike.com/html-services/gridwallData?gridwallPath=jordan-shoes%2FbrkZc8dZ1js&country=US&lang_locale=en_US&sortOrder=viewAll%7Casc",
	"http://store.nike.com/html-services/gridwallData?country=US&lang_locale=en_US&gridwallPath=jordan-shoes/brkZc8dZ1js&pn=2&sortOrder=viewAll|asc",
	"http://store.nike.com/html-services/gridwallData?country=US&lang_locale=en_US&gridwallPath=jordan-shoes/brkZc8dZ1js&pn=3&sortOrder=viewAll|asc",
	"http://store.nike.com/html-services/gridwallData?gridwallPath=skateboarding-shoes%2FbrkZ9yqZ1js&country=US&lang_locale=en_US&sortOrder=viewAll%7Casc",
	"http://store.nike.com/html-services/gridwallData?country=US&lang_locale=en_US&gridwallPath=skateboarding-shoes/brkZ9yqZ1js&pn=2&sortOrder=viewAll|asc"
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
	$regexp = "\"pdpUrl\":\"(.*)\"";
	if(preg_match_all("/$regexp/siU", $thisPageSrc, $matches, PREG_SET_ORDER)){
		// For each results page URL
		foreach($matches as $match) {
			$iUrl=$match[1];
			// check if a product page
			if (strpos($iUrl,'/pd/') !== false){
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
	$productArray["site"] = $site;
	$id = $productPageXpath->query('//span[@class="exp-style-color"]');
	if ($id->length > 0) {
		$productArray["id"] = substr(strrchr($id->item(0)->nodeValue, "Style: "), 7);
	}	
	$productArray["brand"] = "nike";
	$name = $productPageXpath->query('//h1[@itemprop="name"]');
	if ($name->length > 0) {		
		$productArray["name"] = $name->item(0)->nodeValue;
	}	
	$colour = $productPageXpath->query('//span[@class="colorText"]');
	if ($colour->length > 0) {
		$productArray["colour"] = explode('/',$colour->item(0)->nodeValue);
	}	
	$productArray["url"] = $thisPageUrl;
	$desc = $productPageXpath->query('//div[@class="pi-pdpmainbody"]/p');
	if ($desc->length > 0) {
		if(strlen($desc->item(1)->nodeValue)>strlen($desc->item(0)->nodeValue))$productArray["desc"] = $desc->item(1)->nodeValue;
		if(strlen($desc->item(0)->nodeValue)>strlen($desc->item(1)->nodeValue))$productArray["desc"] = $desc->item(0)->nodeValue;
	}	
	$price = $productPageXpath->query('//span[@itemprop="price"]');
	if ($price->length > 0) {
		$p1=preg_replace("/[^0-9,.]/", "", $price->item(0)->nodeValue);
		$p2=preg_replace("/[^0-9,.]/", "", $price->item(1)->nodeValue);
		$productArray["price"] = min($p1,$p2);
	}	
	$productArray["sport"] = "";
	$gender = $productPageXpath->query('//div[@class="exp-product-header"]/h2');
	if ($gender->length > 0) {
		$genderStr = $gender->item(0)->nodeValue;
		$genderStr = substr($genderStr, 0, strpos($genderStr, "'"));
		$productArray["gender"] = $genderStr;
	}	
	$productImage = $productPageXpath->query('//div[@class="hero-image-container"]/img/@src');
	if ($productImage->length > 0) {
		$imageUrl = $productImage->item(0)->nodeValue;
		$imageUrl .= "?wid=1860&hei=1860&bgc=FFFFFF&fmt=jpeg&qlt=85";
		$productArray["image"] = $imageUrl;
	}
	$productPageXpath = NULL;	// Nulling $booksPageXPath object
	$processedPagesUrls[] = $thisPageUrl;	// Build results page URL and add to $resultsPages array
	saveProduct($productArray);
	showProduct($productArray);
	unset($productArray);
}

?>