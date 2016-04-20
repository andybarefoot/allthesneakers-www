<?php

// site: www.reebok.com/us
$site=6;

// Declaring arrays
// the listing pages that might contain links to products
$spiderPagesUrls = array(
	"http://www.reebok.com/us/classic-shoes?sz=120",
	"http://www.reebok.com/us/classic-shoes?sz=120&start=120"
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
	$thisPageUrls = $thisPageXPath->query('//div[@class="hockeycard classics "]/div/div[@class="image"]/a/@href');	// Querying for href attributes of pagination
	// If results exist
	if ($thisPageUrls->length > 0) {
		// For each results page URL
		for ($i = 0; $i < $thisPageUrls->length; $i++) {
			$iUrl=$thisPageUrls->item($i)->nodeValue;
			if (strpos($iUrl,'http://') !== 0)$iUrl='http://www.reebok.com'.$iUrl;
			// check if a product page
			if (strpos($iUrl,'http://www.reebok.com/us/') !== false){
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
	$id = $productPageXpath->query('//segment[@class="product-segment MainProductSection"]/@id');
	if ($id->length > 0) {
		$productArray["id"] = $id->item(0)->nodeValue;
		$productArray["brand"] = "reebok";
		$name = $productPageXpath->query('//h1[@itemprop="name"]');
		if ($name->length > 0) {		
			$productArray["name"] = $name->item(0)->nodeValue;
		}	
		$productArray["url"] = $thisPageUrl;
		$colour = $productPageXpath->query('//div[@class="product-color para-small"]');
		if ($colour->length > 0) {
			$colourStr = $colour->item(0)->nodeValue;
			$productArray["colour"] = explode("/",$colourStr);
		}	
		$desc = $productPageXpath->query('//div[@itemprop="description"]');
		if ($desc->length > 0) {		
			$productArray["desc"] = $desc->item(0)->nodeValue;
		}	
		$price = $productPageXpath->query('//span[@itemprop="price"]');
		if ($price->length > 0) {
			$productArray["price"] = $price->item(0)->nodeValue;
		}
		$productArray["sport"] = "";
		$gender = $productPageXpath->query('//div[@class="title-16 adi-medium-grey vmargin4"]');
		if ($gender->length > 0) {		
			$productArray["gender"] = $gender->item(0)->nodeValue;
		}	
		$productImage = $productPageXpath->query('//img[@data-track="alternate image 1.0"]/@data-zoom');
		if ($productImage->length > 0) {
			$imageUrl = $productImage->item(0)->nodeValue;	// Add URL to variable
			if (strpos($imageUrl,'http://') !== 0)$imageUrl='http:'.$imageUrl;
			$productArray["image"] = $imageUrl;
		}
		$productPageXpath = NULL;	// Nulling $booksPageXPath object
		$processedPagesUrls[] = $thisPageUrl;	// Build results page URL and add to $resultsPages array
		showProduct($productArray);
		saveProduct($productArray);
		sleep(rand(1, 3)/100);	// Being polite and sleeping
	}
}

?>