<?php

// site: http://www.baitme.com
$site = "13";

// Declaring arrays
// the listing pages that might contain links to products
$spiderPagesUrls = array(
	"http://www.baitme.com/footwear",
	"http://www.baitme.com/footwear?p=2",
	"http://www.baitme.com/footwear?p=3",
	"http://www.baitme.com/footwear?p=4",
	"http://www.baitme.com/footwear?p=5",
	"http://www.baitme.com/footwear?p=6",
	"http://www.baitme.com/footwear?p=7",
	"http://www.baitme.com/footwear?p=8",
	"http://www.baitme.com/footwear?p=9",
	"http://www.baitme.com/footwear?p=10",
	"http://www.baitme.com/footwear?p=11",
	"http://www.baitme.com/footwear?p=12",
	"http://www.baitme.com/footwear?p=13",
	"http://www.baitme.com/footwear?p=14",
	"http://www.baitme.com/footwear?p=15",
	"http://www.baitme.com/footwear?p=16",
	"http://www.baitme.com/footwear?p=17",
	"http://www.baitme.com/footwear?p=18",
	"http://www.baitme.com/footwear?p=19",
	"http://www.baitme.com/footwear?p=20",
	"http://www.baitme.com/footwear?p=21",
	"http://www.baitme.com/footwear?p=22",
	"http://www.baitme.com/footwear?p=23",
	"http://www.baitme.com/footwear?p=24",
	"http://www.baitme.com/footwear?p=25",
	"http://www.baitme.com/footwear?p=26",
	"http://www.baitme.com/footwear?p=27",
	"http://www.baitme.com/footwear?p=28",
	"http://www.baitme.com/footwear?p=29",
	"http://www.baitme.com/footwear?p=30",
	"http://www.baitme.com/footwear?p=31",
	"http://www.baitme.com/footwear?p=32",
	"http://www.baitme.com/footwear?p=33",
	"http://www.baitme.com/footwear?p=34",
	"http://www.baitme.com/footwear?p=35",
	"http://www.baitme.com/footwear?p=36",
	"http://www.baitme.com/footwear?p=37",
	"http://www.baitme.com/footwear?p=38",
	"http://www.baitme.com/footwear?p=39",
	"http://www.baitme.com/footwear?p=40",
	"http://www.baitme.com/footwear?p=41",
	"http://www.baitme.com/footwear?p=42",
	"http://www.baitme.com/footwear?p=43",
	"http://www.baitme.com/footwear?p=44",
	"http://www.baitme.com/footwear?p=45",
	"http://www.baitme.com/footwear?p=46",
	"http://www.baitme.com/footwear?p=47",
	"http://www.baitme.com/footwear?p=48",
	"http://www.baitme.com/footwear?p=49",
	"http://www.baitme.com/footwear?p=50",
	"http://www.baitme.com/footwear?p=51",
	"http://www.baitme.com/footwear?p=52",
	"http://www.baitme.com/footwear?p=53",
	"http://www.baitme.com/footwear?p=54",
	"http://www.baitme.com/footwear?p=55"
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
	$thisPageUrls = $thisPageXPath->query('//div[@class="category-products"]/ul/li/a/@href');	// Querying for href attributes of pagination
	// If results exist
	if ($thisPageUrls->length > 0) {
		// For each results page URL
		for ($i = 0; $i < $thisPageUrls->length; $i++) {
			$iUrl=$thisPageUrls->item($i)->nodeValue;
			if (strpos($iUrl,'http://') !== 0)$iUrl='http://www.baitme.com'.$iUrl;
			// check if a product page
			if (strpos($iUrl,'/') !== false){
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
	$id = $productPageXpath->query('//span[@class="product-ids"]');
	if ($id->length > 0) {
		$idStr = $id->item(0)->nodeValue;
		$idStr = substr($idStr, strpos($idStr, "SKU: ") + 5);
		$productArray["id"] = $idStr;
	}
	$brand = $productPageXpath->query('//h1');
	if ($brand->length > 0) {
		$productArray["brand"] = $brand->item(0)->nodeValue;
	}	
	$name = $productPageXpath->query('//h1');
	if ($name->length > 0) {		
		$nameStr = $name->item(0)->nodeValue;
		$cleanNameStr = strstr($nameStr, '(', true);
		$productArray["name"] = $cleanNameStr;
		preg_match('~\((.*?)\)~', $nameStr, $colourStripped);
		$productArray["colour"] = explode("/",$colourStripped[1]);
	}	
	$productArray["url"] = $thisPageUrl;
	$desc = "";
	$price = $productPageXpath->query('//span[@class="price"]');
	if ($price->length > 0) {
		$productArray["price"] = $price->item(0)->nodeValue;
	}	
	$productArray["sport"] = "";
	$productArray["gender"] = "unisex";
	$productImage = $productPageXpath->query('//img[@id="image-0"]/@data-zoom-image');
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