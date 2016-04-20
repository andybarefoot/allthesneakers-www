<?php

// site: http://thedarksideinitiative.com/
$site = "16";

// Declaring arrays
// the listing pages that might contain links to products
$spiderPagesUrls = array(
	"http://thedarksideinitiative.com/shop/category.php?id_category=6&p=1",
	"http://thedarksideinitiative.com/shop/category.php?id_category=6&p=2",
	"http://thedarksideinitiative.com/shop/category.php?id_category=6&p=3",
	"http://thedarksideinitiative.com/shop/category.php?id_category=6&p=4",
	"http://thedarksideinitiative.com/shop/category.php?id_category=6&p=5",
	"http://thedarksideinitiative.com/shop/category.php?id_category=6&p=6"
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
	$thisPageUrls = $thisPageXPath->query('//a[@title="More"]/@href');	// Querying for href attributes of pagination
	// If results exist
	if ($thisPageUrls->length > 0) {
		// For each results page URL
		for ($i = 0; $i < $thisPageUrls->length; $i++) {
			$iUrl=$thisPageUrls->item($i)->nodeValue;
			if (strpos($iUrl,'http://') !== 0)$iUrl='http://thedarksideinitiative.com'.$iUrl;
			// check if a product page
			if (strpos($iUrl,'/product.php?id_product') !== false){
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
// ID
// BRAND
// DESC
	$name = $productPageXpath->query('//div[@id="short_description_block"]/p');
	if ($name->length > 0) {
		$productArray["name"] = $name->item(1)->nodeValue;
		$productArray["colour"] = explode("/",$name->item(2)->nodeValue);
		$productArray["id"] = $name->item(3)->nodeValue;
		$productArray["desc"] = $name->item(5)->nodeValue;
		$nameSplit=explode(" ",trim($productArray["name"]));
		$productArray["brand"] = $nameSplit[0];
		
	}	
// PRICE
	$price = $productPageXpath->query('//span[@id="our_price_display"]');
	if ($price->length > 0) {
		$productArray["price"] = $price->item(0)->nodeValue;
	}	
// SPORT
	$productArray["sport"] = "";
// GENDER
	$genderStr = "";
	$productArray["gender"] = $genderStr;
// IMAGE
	$productImage = $productPageXpath->query('//div[@id="image-block"]/img/@src');
	if ($productImage->length > 0) {
		$imageUrl = $productImage->item(0)->nodeValue;	// Add URL to variable
		if (strpos($imageUrl,'http://') !== 0)$imageUrl='http:'.$imageUrl;
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