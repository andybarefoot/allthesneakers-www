<?php

// site: http://store.kix-files.com
$site = "19";

// Declaring arrays
// the listing pages that might contain links to products
$spiderPagesUrls = array(
/*	"http://www.ubiqlife.com/footwear.html",
	"http://www.ubiqlife.com/footwear/page/2/show/45.html",
	"http://www.ubiqlife.com/footwear/page/3/show/45.html",
	"http://www.ubiqlife.com/footwear/page/4/show/45.html",
	"http://www.ubiqlife.com/footwear/page/5/show/45.html",
	"http://www.ubiqlife.com/footwear/page/6/show/45.html",
	"http://www.ubiqlife.com/footwear/page/7/show/45.html",
	"http://www.ubiqlife.com/footwear/page/8/show/45.html",
	"http://www.ubiqlife.com/footwear/page/9/show/45.html",
	"http://www.ubiqlife.com/footwear/page/10/show/45.html",*/
	"http://www.ubiqlife.com/footwear/page/11/show/45.html",
	"http://www.ubiqlife.com/footwear/page/12/show/45.html",
	"http://www.ubiqlife.com/footwear/page/13/show/45.html",
	"http://www.ubiqlife.com/footwear/page/14/show/45.html",
	"http://www.ubiqlife.com/footwear/page/15/show/45.html",
	"http://www.ubiqlife.com/footwear/page/16/show/45.html",
	"http://www.ubiqlife.com/footwear/page/17/show/45.html",
	"http://www.ubiqlife.com/footwear/page/18/show/45.html",
	"http://www.ubiqlife.com/footwear/page/19/show/45.html",
	"http://www.ubiqlife.com/footwear/page/20/show/45.html"
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
	$thisPageUrls = $thisPageXPath->query('//li[@class="item last"]/a/@href');	// Querying for href attributes of pagination
	// If results exist
	if ($thisPageUrls->length > 0) {
		// For each results page URL
		for ($i = 0; $i < $thisPageUrls->length; $i++) {
			$iUrl=$thisPageUrls->item($i)->nodeValue;
			if (strpos($iUrl,'http://') !== 0)$iUrl='http://www.ubiqlife.com/'.$iUrl;
			// check not already found
			if(!in_array($iUrl, $productPagesUrls)) {
				$productPagesUrls[] = $iUrl;	// Build results page URL and add to $resultsPages array
				print $productCount.": ".$iUrl."<br>";
				$productCount++;
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
	$id = $productPageXpath->query('//div[@class="sku"]');
	if ($id->length > 0) {
		$productArray["id"] = substr($id->item(0)->nodeValue, 4);
	}	
// NAME	
// COLOUR
	$name = $productPageXpath->query('//div[@class="product-name"]/span');
	if ($name->length > 0) {
		$productArray["name"] = strstr($name->item(0)->nodeValue, '(', true);
		$productArray["colour"] = explode("|",strstr($name->item(0)->nodeValue, '('));
	}	
// BRAND
	$productArray["brand"] = $productArray["name"];

// DESC
	$desc = $productPageXpath->query('//div[@class="short-description"]/div[@class="std"]');
	if ($desc->length > 0) {
		$productArray["desc"] = $desc->item(0)->nodeValue;
	}	
// PRICE
//	$price = $productPageXpath->query('//span[@class="price"]');
//	if ($price->length > 0) {
//		$productArray["price"] = $price->item(0)->nodeValue;
//	}
	$price = $productPageXpath->query('//button[@class="button btn-cart"]/@data-price');
	if ($price->length > 0) {
		$productArray["price"] = $price->item(0)->nodeValue;
	}
	// SPORT
	$productArray["sport"] = "";
// GENDER
	$genderStr = "";
	$productArray["gender"] = $genderStr;
// IMAGE
	$productImage = $productPageXpath->query('//img[@class="owl-lazy"]/@data-src');
	if ($productImage->length > 0) {
		$imageUrl = $productImage->item(0)->nodeValue;	// Add URL to variable
		if (strpos($imageUrl,'http://') !== 0)$imageUrl='http:'.$imageUrl;
		$productArray["image"] = $imageUrl;
	}
	$productPageXpath = NULL;	// Nulling $booksPageXPath object
	$processedPagesUrls[] = $thisPageUrl;	// Build results page URL and add to $resultsPages array
	if($productArray["price"]!=""){
		saveProduct($productArray);
	}
//	showProduct($productArray);
	unset($productArray);
	sleep(rand(1, 3)/100);	// Being polite and sleeping
}

?>