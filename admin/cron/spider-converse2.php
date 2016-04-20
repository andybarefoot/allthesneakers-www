<?php

// site: http://www.converse.com
$site=8;

// Declaring arrays
// the listing pages that might contain links to products
$spiderPagesUrls = array(
	"http://www.converse.com/products/men/sneakers?start=0&sz=300",
	"http://www.converse.com/products/women/sneakers?start=0&sz=300"
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
	$thisPageUrls = $thisPageXPath->query('//a[@class="thumb-link"]/@href');	// Querying for href attributes of pagination
	// If results exist
	if ($thisPageUrls->length > 0) {
		// For each results page URL
		for ($i = 0; $i < $thisPageUrls->length; $i++) {
			$iUrl=$thisPageUrls->item($i)->nodeValue;
			if (strpos($iUrl,'http://') !== 0)$iUrl='http://www.converse.com'.$iUrl;
			// check if a product page
			if (strpos($iUrl,'/regular/') !== false){
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

	$id = $productPageXpath->query('//span[@itemprop="productID"]');
	if ($id->length > 0) {
		$productArray["id"] = $id->item(0)->nodeValue;
	}	
	$productArray["brand"] = "converse";
	$name = $productPageXpath->query('//h1[@itemprop="name"]');
	if ($name->length > 0) {		
		$productArray["name"] = $name->item(0)->nodeValue;
	}	
	$colour = $productPageXpath->query('//span[@itemprop="color"]');
	if ($colour->length > 0) {		
		$productArray["colour"] = explode("/",$colour->item(0)->nodeValue);
	}	
	$productArray["url"] = $thisPageUrl;
	$desc = $productPageXpath->query('//p[@itemprop="description"]');
	if ($desc->length > 0) {		
		$productArray["desc"] = $desc->item(0)->nodeValue;
	}	
	$price = $productPageXpath->query('//div[@class="product-price"]/span');
	if ($price->length > 0) {
		$p1=preg_replace("/[^0-9,.]/", "", $price->item(0)->nodeValue);
		$p2=preg_replace("/[^0-9,.]/", "", $price->item(1)->nodeValue);
		if($p2!=""){
			$productArray["price"] = min($p1,$p2);
		}else{
			$productArray["price"] = $p1;
		}
	}	
	$productArray["sport"] = "";
	$productArray["gender"] = "unisex";
	$productImage = $productPageXpath->query('//a[@class="product-image main-image"]/@href');
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