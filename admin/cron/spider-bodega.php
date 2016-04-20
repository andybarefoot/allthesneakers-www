<?php

// getHour
$thisHour = date('G');
echo "Hour: ".$thisHour."<br/><br/>";

// site: http://shop.bdgastore.com//
$site = "4";

// include functions
// - function getColours($colourStrs,$localID,$shoeBrand)
// - curlGet($url)
// - returnXPathObject($item)
// - scrapeBetween($item, $start, $end)
// - saveProduct($productArray)
include_once '/nfs/c05/h04/mnt/73815/domains/allthesneakers.com/includes/commonScrapeFunctions.php';

// Declaring arrays
// the listing pages that might contain links to products
$spiderPagesUrls = array(
	"http://shop.bdgastore.com/collections/athletic"/*,
	"http://shop.bdgastore.com/collections/athletic?page=2",
	"http://shop.bdgastore.com/collections/athletic?page=3",
	"http://shop.bdgastore.com/collections/athletic?page=4",
	"http://shop.bdgastore.com/collections/athletic?page=5",
	"http://shop.bdgastore.com/collections/athletic?page=6",
	"http://shop.bdgastore.com/collections/athletic?page=7",
	"http://shop.bdgastore.com/collections/athletic?page=8",
	"http://shop.bdgastore.com/collections/athletic?page=9",
	"http://shop.bdgastore.com/collections/athletic?page=10",
	"http://shop.bdgastore.com/collections/casual",
	"http://shop.bdgastore.com/collections/casual?page=2",
	"http://shop.bdgastore.com/collections/casual?page=3",
	"http://shop.bdgastore.com/collections/casual?page=4",
	"http://shop.bdgastore.com/collections/casual?page=5",
	"http://shop.bdgastore.com/collections/casual?page=6",
	"http://shop.bdgastore.com/collections/casual?page=7",
	"http://shop.bdgastore.com/collections/casual?page=8",
	"http://shop.bdgastore.com/collections/casual?page=9",
	"http://shop.bdgastore.com/collections/casual?page=10",
	"http://shop.bdgastore.com/collections/casual?page=11",
	"http://shop.bdgastore.com/collections/casual?page=12",
	"http://shop.bdgastore.com/collections/casual?page=13",
	"http://shop.bdgastore.com/collections/casual?page=14",
	"http://shop.bdgastore.com/collections/casual?page=15"*/
	);

// the product pages found
$productPagesUrls = array();

// the product pages processed
$processedPagesUrls = array();

// Declaring variables
// the number of products processed
$productCount=0;

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
	$thisPageUrls = $thisPageXPath->query('//a[@class="product-link"]/@href');	// Querying for href attributes of pagination
	// If results exist
	if ($thisPageUrls->length > 0) {
		// For each results page URL
		for ($i = 0; $i < $thisPageUrls->length; $i++) {
			$iUrl=$thisPageUrls->item($i)->nodeValue;
			if (strpos($iUrl,'http://') !== 0)$iUrl='http://shop.bdgastore.com'.$iUrl;
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
	
	$fullDescription = $productPageXpath->query('//div[@itemprop="description"]/p');
	if ($fullDescription->length > 0) {
		echo $fullDescription->item($fullDescription->length-1)->nodeValue;
		echo "<br>";
	}	
	
	$id = $productPageXpath->query('(//div[@id="product-description"]/ul/li)[last()]');
	if ($id->length > 0) {
		$productArray["id"] = $id->item(0)->nodeValue;
	}	
	$brand = $productPageXpath->query('//h3[@itemprop="brand"]/a');
	if ($brand->length > 0) {
		$productArray["brand"] = $brand->item(0)->nodeValue;
	}	
	$name = $productPageXpath->query('//h2[@itemprop="name"]');
	if ($name->length > 0) {
		$productArray["name"] = $name->item(0)->nodeValue;
	}	
	$productArray["url"] = $thisPageUrl;
	$desc = $productPageXpath->query('//div[@id="product-description"]/p');
	if ($desc->length > 0) {
		$productArray["desc"] = $desc->item(0)->nodeValue;
	}
	$price = $productPageXpath->query('//span[@itemprop="price"]/span');
	if ($price->length > 0) {
		$p1=preg_replace("/[^0-9,.]/", "", $price->item(0)->nodeValue);
		$p2=preg_replace("/[^0-9,.]/", "", $price->item(1)->nodeValue);
		$productArray["price"] = min($p1,$p2);
	}	
	$colour = $productPageXpath->query('//title');
	if ($colour->length > 0) {
		$productArray["colour"] = explode("/",$colour->item(0)->nodeValue);
	}	
	$productArray["sport"] = "";
	$productArray["gender"] = "unisex";
	$productImage = $productPageXpath->query('//meta[@property="og:image"]/@content');
	if ($productImage->length > 0) {
		$imageUrl = $productImage->item(0)->nodeValue;	// Add URL to variable
		$productArray["image"] = $imageUrl;
	}
	$productPageXpath = NULL;	// Nulling $booksPageXPath object
	$processedPagesUrls[] = $thisPageUrl;	// Build results page URL and add to $resultsPages array
	showProduct($productArray);
//	saveProduct($productArray);
	unset($productArray);
	sleep(rand(1, 3)/100);	// Being polite and sleeping
}

// Spidering the specified URLs
while(count($spiderPagesUrls)>0){
	findProducts();
}

// Removing duplicates from Product URL array and reindex
$productPagesUrls = array_values(array_unique($productPagesUrls));
$totalUrls = count($productPagesUrls);

// Define subset to process this time
$offset = $totalUrls/24;
$sliceStart = floor($offset*$thisHour);
$sliceEnd = ceil($offset);
//$productPagesUrls = array_slice($productPagesUrls, $sliceStart, $sliceEnd);

// Process pages
while(count($productPagesUrls)>0){
	processPages();
}

?>