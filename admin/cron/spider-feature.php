<?php
// SHOPIFY SITE - using Shopify code
// site: http://www.featuresneakerboutique.com/
$site = "23";

// Declaring arrays
// the listing pages that might contain links to products
$spiderPagesUrls = array(
	"http://featuresneakerboutique.com/collections/sneakers"
/*	"http://featuresneakerboutique.com/collections/sneakers",
	"http://featuresneakerboutique.com/collections/sneakers?page=2",
	"http://featuresneakerboutique.com/collections/sneakers?page=3",
	"http://featuresneakerboutique.com/collections/sneakers?page=4",
	"http://featuresneakerboutique.com/collections/sneakers?page=5",
	"http://featuresneakerboutique.com/collections/sneakers?page=6",
	"http://featuresneakerboutique.com/collections/sneakers?page=7",
	"http://featuresneakerboutique.com/collections/sneakers?page=8",
	"http://featuresneakerboutique.com/collections/sneakers?page=9",
	"http://featuresneakerboutique.com/collections/sneakers?page=10",
	"http://featuresneakerboutique.com/collections/sneakers?page=11",
	"http://featuresneakerboutique.com/collections/sneakers?page=12",
	"http://featuresneakerboutique.com/collections/sneakers?page=13",
	"http://featuresneakerboutique.com/collections/sneakers?page=14",
	"http://featuresneakerboutique.com/collections/sneakers?page=15"*/
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
	$thisPageUrls = $thisPageXPath->query('//div[@class="product-index-inner"]/a/@href');	// Querying for href attributes of pagination
	// If results exist
	if ($thisPageUrls->length > 0) {
		// For each results page URL
		for ($i = 0; $i < $thisPageUrls->length; $i++) {
			$iUrl=$thisPageUrls->item($i)->nodeValue;
			if (strpos($iUrl,'http://') !== 0)$iUrl='http://www.featuresneakerboutique.com'.$iUrl;
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
	$regexp = '{"id":(.*),.*"sku":"(.*)".*}';
	if(preg_match_all("/$regexp/siU", $productPage, $matches, PREG_SET_ORDER)){
		$localID=$matches[0][2];
		$productArray["id"]=$localID;
	}
// BRAND
	$regexp = '"vendor":"(.*)"';
	if(preg_match_all("/$regexp/siU", $productPage, $matches, PREG_SET_ORDER)){
		$brand=$matches[0][1];
		$productArray["brand"]=$brand;
	}
// NAME	
// COLOUR
	$regexp = '"title":"(.*)"';
	if(preg_match_all("/$regexp/siU", $productPage, $matches, PREG_SET_ORDER)){
		$nameColArray = explode("(",$matches[0][1]);
		$productArray["name"]=$nameColArray[0];
		$productArray["colour"] = explode("/",$nameColArray[1]);
	}
// DESC
	$desc = "";
// PRICE
	$price = $productPageXpath->query('//span[@id="price-field"]');
	if ($price->length > 0) {
		$productArray["price"] = $price->item(0)->nodeValue;
	}	
// SPORT
	$productArray["sport"] = "";
// GENDER
	$productArray["gender"] = "unisex";
// IMAGE
	$productImage = $productPageXpath->query('//a[@rel="set_product_images"]/@href');
	if ($productImage->length > 0) {
		$imageUrl = $productImage->item(0)->nodeValue;	// Add URL to variable
		if (strpos($imageUrl,'http://') !== 0)$imageUrl='http:'.$imageUrl;
		$productArray["image"] = $imageUrl;
	}
	$productPageXpath = NULL;	// Nulling $booksPageXPath object
	$processedPagesUrls[] = $thisPageUrl;	// Build results page URL and add to $resultsPages array
//	saveProduct($productArray);
	showProduct($productArray);
	unset($productArray);
	sleep(rand(1, 3)/100);	// Being polite and sleeping
}

?>