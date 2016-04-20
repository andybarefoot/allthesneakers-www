<?php

// site: http://http://www.addictmiami.com/
$site = "15";

// Declaring arrays
// the listing pages that might contain links to products
$spiderPagesUrls = array(
	"http://www.addictmiami.com/collections/men?page=1",
	"http://www.addictmiami.com/collections/men?page=2",
	"http://www.addictmiami.com/collections/men?page=3",
	"http://www.addictmiami.com/collections/men?page=4",
	"http://www.addictmiami.com/collections/men?page=5",
	"http://www.addictmiami.com/collections/men?page=6",
	"http://www.addictmiami.com/collections/men?page=7",
	"http://www.addictmiami.com/collections/men?page=8",
	"http://www.addictmiami.com/collections/men?page=9",
	"http://www.addictmiami.com/collections/men?page=10",
	"http://www.addictmiami.com/collections/men?page=11",
	"http://www.addictmiami.com/collections/men?page=12",
	"http://www.addictmiami.com/collections/men?page=13",
	"http://www.addictmiami.com/collections/men?page=14",
	"http://www.addictmiami.com/collections/men?page=15",
	"http://www.addictmiami.com/collections/men?page=16",
	"http://www.addictmiami.com/collections/men?page=17",
	"http://www.addictmiami.com/collections/men?page=18",
	"http://www.addictmiami.com/collections/men?page=19",
	"http://www.addictmiami.com/collections/women?page=1",
	"http://www.addictmiami.com/collections/women?page=2",
	"http://www.addictmiami.com/collections/women?page=3",
	"http://www.addictmiami.com/collections/women?page=4",
	"http://www.addictmiami.com/collections/women?page=5",
	"http://www.addictmiami.com/collections/women?page=6",
	"http://www.addictmiami.com/collections/women?page=7",
	"http://www.addictmiami.com/collections/women?page=8",
	"http://www.addictmiami.com/collections/women?page=9",
	"http://www.addictmiami.com/collections/women?page=10",
	"http://www.addictmiami.com/collections/women?page=11",
	"http://www.addictmiami.com/collections/women?page=12"
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
	$thisPageUrls = $thisPageXPath->query('//div[@class="coll-image-wrap"]/a/@href');	// Querying for href attributes of pagination
	// If results exist
	if ($thisPageUrls->length > 0) {
		// For each results page URL
		for ($i = 0; $i < $thisPageUrls->length; $i++) {
			$iUrl=$thisPageUrls->item($i)->nodeValue;
			if (strpos($iUrl,'http://') !== 0)$iUrl='http://www.addictmiami.com'.$iUrl;
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
	$productArray["site"] = $site;
	$productArray["url"] = $thisPageUrl;
	$brand = $productPageXpath->query('//h2[@itemprop="brand"]/a/text()');
	if ($brand->length > 0) {
		$productArray["brand"] = $brand->item(0)->nodeValue;
	}
	$name = $productPageXpath->query('//h1[@itemprop="name"]');
	if ($name->length > 0) {
		$n = strrpos($name->item(0)->nodeValue, ' ');		
		$productArray["name"] = substr($name->item(0)->nodeValue, 0, n);
		$productArray["id"] = substr($name->item(0)->nodeValue, n + 1);
	}	
	$idStrL = $productPageXpath->query('//meta[@name="description"]/@content');


	if ($idStrL->length > 0) {
		$idStrL = str_replace(" ", "-", $idStrL->item(0)->nodeValue);
		$idStr = getLocalID($idStrL,$productArray["brand"]);
		$productArray["id"] = $idStr;
	}
	$desc = "";
	$productArray["$desc"] = $desc;
	$price = $productPageXpath->query('//span[@itemprop="price"]');
	if ($price->length > 0) {
		$productArray["price"] = $price->item(0)->nodeValue;
	}	
	$colour = "";
	$productArray["colour"] = explode("/",$colour);
	$productArray["sport"] = "";
	$genderStr = $thisPageUrl;
	$genderStr = substr($genderStr, strrpos($genderStr, "/collections/")+13, (strpos($genderStr, "/products/")-strrpos($genderStr, "/collections/"))-13);
	echo $genderStr;
	$productArray["gender"] = $genderStr;
	$productImage = $productPageXpath->query('//a[@class="gallery"]/@href');
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