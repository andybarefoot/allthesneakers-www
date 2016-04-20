<?php

// site: http://us.puma.com/
$site=9;

// Declaring arrays
// the listing pages that might contain links to products
$spiderPagesUrls = array(
	"http://us.puma.com/en_US/men/shoes/sneakers?start=0&format=page-element&sz=50",
	"http://us.puma.com/en_US/men/shoes/sneakers?start=50&format=page-element&sz=50",
	"http://us.puma.com/en_US/women/shoes/sneakers?start=0&format=page-element&sz=50",
	"http://us.puma.com/en_US/women/shoes/sneakers?start=50&format=page-element&sz=50"
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
			if (strpos($iUrl,'http://') !== 0)$iUrl='http://us.puma.com'.$iUrl;
			// check if a product page
			if (strpos($iUrl,'/pd/') !== false){
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
	$productArray["brand"] = "puma";
	$name = $productPageXpath->query('//h1[@itemprop="name"]');
	if ($name->length > 0) {		
		$productArray["name"] = $name->item(0)->nodeValue;
	}	
	$colour = $productPageXpath->query('//label[@itemprop="color"]/span[@itemprop="name"]');
	if ($colour->length > 0) {		
		$productArray["colour"] = explode("-",$colour->item(0)->nodeValue);
	}	
	$productArray["url"] = $thisPageUrl;
	$desc = $productPageXpath->query('//div[@itemprop="description"]/p');
	if ($desc->length > 0) {		
		$productArray["desc"] = $desc->item(0)->nodeValue;
	}	
	$price = $productPageXpath->query('//span[@itemprop="price"]');
	if ($price->length > 0) {
		$productArray["price"] = $price->item(0)->nodeValue;
	}	
	$productArray["sport"] = "";
	$gender = $productPageXpath->query('//h1[@itemprop="name"]');
	if ($gender->length > 0) {		
		$productArray["gender"] = $gender->item(0)->nodeValue;
	}	
	$imageUrl="http://pumaecom.scene7.com/is/image/PUMAECOM/".$imageUrl = str_replace("-", "_", $productArray["id"])."_01_PNA?&wid=1000&hei=1000&fmt=jpg&op_sharpen=1&resMode=sharp2";
	$productArray["image"] = $imageUrl;
	$productPageXpath = NULL;	// Nulling $booksPageXPath object
	$processedPagesUrls[] = $thisPageUrl;	// Build results page URL and add to $resultsPages array
	saveProduct($productArray);
	showProduct($productArray);
	unset($productArray);
	sleep(rand(1, 3)/100);	// Being polite and sleeping
}

?>