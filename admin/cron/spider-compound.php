<?php

// site: http://compoundgallery.com/
$site = "24";


// Declaring arrays
// the listing pages that might contain links to products
$spiderPagesUrls = array(
	"http://compoundgallery.com/collections/footwear",
	"http://compoundgallery.com/collections/footwear?page=2",
	"http://compoundgallery.com/collections/footwear?page=3",
	"http://compoundgallery.com/collections/footwear?page=4",
	"http://compoundgallery.com/collections/footwear?page=5"
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
	$thisPageUrls = $thisPageXPath->query('//figure/a/@href');	// Querying for href attributes of pagination
	// If results exist
	if ($thisPageUrls->length > 0) {
		// For each results page URL
		for ($i = 0; $i < $thisPageUrls->length; $i++) {
			$iUrl=$thisPageUrls->item($i)->nodeValue;
			if (strpos($iUrl,'http://') !== 0)$iUrl='http://compoundgallery.com'.$iUrl;
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
// ID
	$regexp = '<p>Style:(.*)<br>';
	if(preg_match_all("/$regexp/siU", $productPage, $matches, PREG_SET_ORDER)){
		$localID=$matches[0][1];
		$productArray["id"]=$localID;
	}
// BRAND
	$brand = $productPageXpath->query('//h2[@itemprop="brand"]/a');
	if ($brand->length > 0) {
		$productArray["brand"] = $brand->item(0)->nodeValue;
	}	
// NAME	
	$regexp = '"title":"(.*)"';
	if(preg_match_all("/$regexp/siU", $productPage, $matches, PREG_SET_ORDER)){
		$name=$matches[0][1];
		$productArray["name"]=$name;
	}
// COLOUR
	$regexp = '<br>Color:(.*)<br>';
	if(preg_match_all("/$regexp/siU", $productPage, $matches, PREG_SET_ORDER)){
		$colour=$matches[0][1];
		$productArray["colour"] = explode("/",$colour);
	}
// DESC
	$productArray["desc"] = "";
// PRICE
	$price = $productPageXpath->query('//meta[@itemprop="price"]/@content');
	if ($price->length > 0) {
		$productArray["price"] = $price->item(0)->nodeValue;
	}	
// SPORT
	$productArray["sport"] = "";
// GENDER
	$productArray["gender"] = "unisex";
// IMAGE
	$productImage = $productPageXpath->query('//div[@class="featured-image"]/a/@href');
	if ($productImage->length > 0) {
		$imageUrl = $productImage->item(0)->nodeValue;	// Add URL to variable
		if (strpos($imageUrl,'http:') !== 0)$imageUrl='http:'.$imageUrl;
		$productArray["image"] = $imageUrl;
	}
	$productPageXpath = NULL;	// Nulling $booksPageXPath object
	$processedPagesUrls[] = $thisPageUrl;	// Build results page URL and add to $resultsPages array
	saveProduct($productArray);
	showProduct($productArray);
	unset($productArray);
	sleep(rand(1, 3)/100);	// Being polite and sleeping
}

?>