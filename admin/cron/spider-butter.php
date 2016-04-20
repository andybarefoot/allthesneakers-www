<?php

// site: http://shop.extrabutterny.com/
$site = "3";

// Declaring arrays
// the listing pages that might contain links to products
$spiderPagesUrls = array(
	"http://shop.extrabutterny.com/collections/footwear",
	"http://shop.extrabutterny.com/collections/footwear?page=2",
	"http://shop.extrabutterny.com/collections/footwear?page=3",
	"http://shop.extrabutterny.com/collections/footwear?page=4",
	"http://shop.extrabutterny.com/collections/footwear?page=5",
	"http://shop.extrabutterny.com/collections/footwear?page=6",
	"http://shop.extrabutterny.com/collections/footwear?page=7",
	"http://shop.extrabutterny.com/collections/footwear?page=8",
	"http://shop.extrabutterny.com/collections/footwear?page=9",
	"http://shop.extrabutterny.com/collections/footwear?page=10",
	"http://shop.extrabutterny.com/collections/footwear?page=11",
	"http://shop.extrabutterny.com/collections/footwear?page=12",
	"http://shop.extrabutterny.com/collections/footwear?page=13",
	"http://shop.extrabutterny.com/collections/footwear?page=14",
	"http://shop.extrabutterny.com/collections/footwear?page=15",
	"http://shop.extrabutterny.com/collections/footwear?page=16",
	"http://shop.extrabutterny.com/collections/footwear?page=17",
	"http://shop.extrabutterny.com/collections/footwear?page=18",
	"http://shop.extrabutterny.com/collections/footwear?page=19",
	"http://shop.extrabutterny.com/collections/footwear?page=20",
	"http://shop.extrabutterny.com/collections/footwear?page=21",
	"http://shop.extrabutterny.com/collections/footwear?page=22",
	"http://shop.extrabutterny.com/collections/footwear?page=23",
	"http://shop.extrabutterny.com/collections/footwear?page=24",
	"http://shop.extrabutterny.com/collections/footwear?page=25"
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
	$thisPageUrls = $thisPageXPath->query('//div[contains(concat(" ", @class, " "), " thumbnail ")]/a/@href');	// Querying for href attributes of pagination
	// If results exist
	if ($thisPageUrls->length > 0) {
		// For each results page URL
		for ($i = 0; $i < $thisPageUrls->length; $i++) {
			$iUrl=$thisPageUrls->item($i)->nodeValue;
			if (strpos($iUrl,'http://') !== 0)$iUrl='http://shop.extrabutterny.com'.$iUrl;
			// check if a product page
			if (strpos($iUrl,'/collections/footwear/products/') !== false){
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
	$id = $productPageXpath->query('//meta[@name="twitter:description"]/@content');
	if ($id->length > 0) {
		$idStr = $id->item(0)->nodeValue;
		$idStr = trim(preg_replace('/\s\s+/', ' ', $idStr));
		$productArray["id"] = strtok($idStr, " ");;
	}
	$brand = $productPageXpath->query('//meta[@name="twitter:data2"]/@content');
	if ($brand->length > 0) {
		$productArray["brand"] = $brand->item(0)->nodeValue;
	}	
	$name = $productPageXpath->query('//h1[@class="product_name"]');
	if ($name->length > 0) {		
		$nameStr = $name->item(0)->nodeValue;
		preg_match('~:(.*?)\(~', $nameStr, $nameStripped);
		$productArray["name"] = $nameStripped[1];
		preg_match('~\((.*?)\)~', $nameStr, $colourStripped);
		$productArray["colour"] = explode("/",$colourStripped[1]);
	}	
	$productArray["url"] = $thisPageUrl;
	$desc = "";
	$price = $productPageXpath->query('//span[@itemprop="price"]/@content');
	if ($price->length > 0) {
		$productArray["price"] = $price->item(0)->nodeValue;
	}	
	$productArray["sport"] = "";
	$productArray["gender"] = "unisex";
	$productImage = $productPageXpath->query('//a[@class="fancybox"]/@href');
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