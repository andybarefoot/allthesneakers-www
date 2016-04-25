<?php

// site: http://www.newbalance.com/
$site=26;

// Declaring arrays
// the listing pages that might contain links to products
$spiderPagesUrls = array(
	"http://www.newbalance.com/men/shoes/lifestyle-1/?srule=By%20Price%20%28High-Low%29&sz=1000",
	"http://www.newbalance.com/women/shoes/lifestyle-1/?srule=By%20Price%20%28High-Low%29&sz=1000"
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
	$thisPageUrls = $thisPageXPath->query('//a[@class="product-image"]/@href');	// Querying for href attributes of pagination
	// If results exist
	if ($thisPageUrls->length > 0) {
		// For each results page URL
		for ($i = 0; $i < $thisPageUrls->length; $i++) {
			$iUrl=$thisPageUrls->item($i)->nodeValue;
			if (strpos($iUrl,'http://') !== 0)$iUrl='http://www.newbalance.com'.$iUrl;
			// check if a product page
			if (strpos($iUrl,'/pd/') !== false){
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
	$id = $productPageXpath->query('//div[@class="product-meta"]/@data-style');
	if ($id->length > 0) {
		$productArray["id"] = $id->item(0)->nodeValue;
	}	
// BRAND
	$productArray["brand"] = "New Balance";
// NAME	
	$name = $productPageXpath->query('//div[@class="product-meta"]/@data-productname');
	if ($name->length > 0) {
		$productArray["name"] = $name->item(0)->nodeValue;
	}	
// COLOUR
	$regexp = "\"product_sku\":(.*)]";
	if(preg_match_all("/$regexp/siU", $productPage, $matches, PREG_SET_ORDER)){
		foreach($matches as $match) {
			$colour=$match[1];
			$productArray["colour"] = explode("_",$colour);
		}
	}
// DESC
	$desc = $productPageXpath->query('//meta[@name="description"]/@content');
	if ($desc->length > 0) {
		$productArray["desc"] = $desc->item(0)->nodeValue;
	}	
// PRICE
	$regexp = "\"product_unit_price\":(.*)]";
	if(preg_match_all("/$regexp/siU", $productPage, $matches, PREG_SET_ORDER)){
		foreach($matches as $match) {
			$price=$match[1];
			$productArray["price"] = $price;
		}
	}
// SPORT
	$productArray["sport"] = "";
// GENDER
	$productArray["gender"] = "";
// IMAGE
	$productImage = $productPageXpath->query('//meta[@property="og:image"]/@content');
	if ($productImage->length > 0) {
		$imageUrl = $productImage->item(0)->nodeValue;	// Add URL to variable
		$productArray["image"] = substr($imageUrl, 0, strpos($imageUrl, '?'));
	}
	$productPageXpath = NULL;	// Nulling $booksPageXPath object
	$processedPagesUrls[] = $thisPageUrl;	// Build results page URL and add to $resultsPages array
	saveProduct($productArray);
	showProduct($productArray);
	sleep(rand(1, 3)/100);	// Being polite and sleeping
}

?>