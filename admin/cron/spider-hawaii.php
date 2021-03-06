<?php

// site: store.kickshawaii.com/
$site=14;

// Declaring arrays
// the listing pages that might contain links to products
$spiderPagesUrls = array(
	"http://store.kickshawaii.com/collections/footwear?page=1",
	"http://store.kickshawaii.com/collections/footwear?page=2",
	"http://store.kickshawaii.com/collections/footwear?page=3",
	"http://store.kickshawaii.com/collections/footwear?page=4",
	"http://store.kickshawaii.com/collections/footwear?page=5",
	"http://store.kickshawaii.com/collections/footwear?page=6",
	"http://store.kickshawaii.com/collections/footwear?page=7",
	"http://store.kickshawaii.com/collections/footwear?page=8",
	"http://store.kickshawaii.com/collections/footwear?page=9",
	"http://store.kickshawaii.com/collections/footwear?page=10"
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
	$thisPageUrls = $thisPageXPath->query('//div[@class="product-grid-item "]/a/@href');	// Querying for href attributes of pagination
	
	// If results exist
	if ($thisPageUrls->length > 0) {
		// For each results page URL
		for ($i = 0; $i < $thisPageUrls->length; $i++) {
			$iUrl=$thisPageUrls->item($i)->nodeValue;
			if (strpos($iUrl,'http://') !== 0)$iUrl='http://store.kickshawaii.com/'.$iUrl;
			// check if a product page
			if (strpos($iUrl,'/collections/footwear/products/') !== false){
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

	$productArray["site"] = $site;
	$regexp = "\"sku\":\"(.*)\"";
	if(preg_match_all("/$regexp/siU", $productPage, $matches, PREG_SET_ORDER)){
		foreach($matches as $match) {
			$productArray["id"] = $match[1];
		}
	}
	$brand = $productPageXpath->query('//h1[@id="product-title"]');
	if ($brand->length > 0) {		
		$brandArray = explode("<BR>", $brand->item(0)->nodeValue);
		$productArray["brand"] = $brandArray[0];
	}	
	$name = $productPageXpath->query('//div[@class="description"]/p[1]');
	if ($name->length > 0) {		
		$productArray["name"] = $name->item(0)->nodeValue;
	}	
	$productArray["url"] = $thisPageUrl;
	$colour = $productPageXpath->query('//div[@class="description"]/p[2]');
	if ($colour->length > 0) {		
		$productArray["colour"] = explode("/",substr(($colour->item(0)->nodeValue), strpos(($colour->item(0)->nodeValue), ' ')));
	}	
	$desc = "";
	$price = $productPageXpath->query('//p[@class="price"]/strong');
	if ($price->length > 0) {		
		$productArray["price"] = $price->item(0)->nodeValue;
	}
	$regexp = "\"price\":(.*),\"price_min\"";
	if(preg_match_all("/$regexp/siU", $productPage, $matches, PREG_SET_ORDER)){
		foreach($matches as $match) {
			$priceCents=$match[1];
			$productArray["price"] = $priceCents/100;
		}
	}
	$productArray["sport"] = "";
	$productArray["gender"] = "unisex";
	$productImage = $productPageXpath->query('//div[@id="product-gallery"]/div[@id="active-wrapper"]/a/@href');
	if ($productImage->length > 0) {
		$imageUrl = $productImage->item(0)->nodeValue;	// Add URL to variable
		if (strpos($imageUrl,'http://') !== 0)$imageUrl='http:'.$imageUrl;
		$productArray["image"] = $imageUrl;
	}
	$productPageXpath = NULL;	// Nulling $booksPageXPath object
	$processedPagesUrls[] = $thisPageUrl;	// Build results page URL and add to $resultsPages array
	saveProduct($productArray);
	showProduct($productArray);
	sleep(rand(1, 3)/100);	// Being polite and sleeping
}

?>