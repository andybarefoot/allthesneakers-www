<?php

// site: http://www.sneakersnstuff.com/
$site = "20";

// Declaring arrays
// the listing pages that might contain links to products
$spiderPagesUrls = array(
/*	"http://www.sneakersnstuff.com/en/2/sneakers/1?orderBy=Published"*/
	"http://www.sneakersnstuff.com/en/2/sneakers/1?orderBy=Published",
	"http://www.sneakersnstuff.com/en/2/sneakers/2?orderBy=Published",
	"http://www.sneakersnstuff.com/en/2/sneakers/3?orderBy=Published",
	"http://www.sneakersnstuff.com/en/2/sneakers/4?orderBy=Published",
	"http://www.sneakersnstuff.com/en/2/sneakers/5?orderBy=Published",
	"http://www.sneakersnstuff.com/en/2/sneakers/6?orderBy=Published",
	"http://www.sneakersnstuff.com/en/2/sneakers/7?orderBy=Published",
	"http://www.sneakersnstuff.com/en/2/sneakers/8?orderBy=Published",
	"http://www.sneakersnstuff.com/en/2/sneakers/9?orderBy=Published",
	"http://www.sneakersnstuff.com/en/2/sneakers/10?orderBy=Published",
	"http://www.sneakersnstuff.com/en/2/sneakers/11?orderBy=Published",
	"http://www.sneakersnstuff.com/en/2/sneakers/12?orderBy=Published",
	"http://www.sneakersnstuff.com/en/2/sneakers/13?orderBy=Published",
	"http://www.sneakersnstuff.com/en/2/sneakers/14?orderBy=Published",
	"http://www.sneakersnstuff.com/en/2/sneakers/15?orderBy=Published",
	"http://www.sneakersnstuff.com/en/2/sneakers/16?orderBy=Published",
	"http://www.sneakersnstuff.com/en/2/sneakers/17?orderBy=Published",
	"http://www.sneakersnstuff.com/en/2/sneakers/18?orderBy=Published",
	"http://www.sneakersnstuff.com/en/2/sneakers/19?orderBy=Published",
	"http://www.sneakersnstuff.com/en/2/sneakers/20?orderBy=Published",
	"http://www.sneakersnstuff.com/en/2/sneakers/21?orderBy=Published",
	"http://www.sneakersnstuff.com/en/2/sneakers/22?orderBy=Published",
	"http://www.sneakersnstuff.com/en/2/sneakers/23?orderBy=Published",
	"http://www.sneakersnstuff.com/en/2/sneakers/24?orderBy=Published",
	"http://www.sneakersnstuff.com/en/2/sneakers/25?orderBy=Published",
	"http://www.sneakersnstuff.com/en/2/sneakers/26?orderBy=Published",
	"http://www.sneakersnstuff.com/en/2/sneakers/27?orderBy=Published",
	"http://www.sneakersnstuff.com/en/2/sneakers/28?orderBy=Published",
	"http://www.sneakersnstuff.com/en/2/sneakers/29?orderBy=Published",
	"http://www.sneakersnstuff.com/en/2/sneakers/30?orderBy=Published",
	"http://www.sneakersnstuff.com/en/2/sneakers/31?orderBy=Published",
	"http://www.sneakersnstuff.com/en/2/sneakers/32?orderBy=Published",
	"http://www.sneakersnstuff.com/en/2/sneakers/33?orderBy=Published",
	"http://www.sneakersnstuff.com/en/2/sneakers/34?orderBy=Published",
	"http://www.sneakersnstuff.com/en/2/sneakers/35?orderBy=Published",
	"http://www.sneakersnstuff.com/en/2/sneakers/36?orderBy=Published",
	"http://www.sneakersnstuff.com/en/2/sneakers/37?orderBy=Published",
	"http://www.sneakersnstuff.com/en/2/sneakers/38?orderBy=Published",
	"http://www.sneakersnstuff.com/en/2/sneakers/39?orderBy=Published",
	"http://www.sneakersnstuff.com/en/2/sneakers/40?orderBy=Published"
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
	$thisPageUrls = $thisPageXPath->query('//a[@class="plink image"]/@href');	// Querying for href attributes of pagination
	// If results exist
print "debug 0";
		if ($thisPageUrls->length > 0) {
print "debug 1";
		// For each results page URL
		for ($i = 0; $i < $thisPageUrls->length; $i++) {
print "debug 2";
			$iUrl=$thisPageUrls->item($i)->nodeValue;
			if (strpos($iUrl,'http://') !== 0)$iUrl='http://www.sneakersnstuff.com'.$iUrl;
			// check if a product page
			if (strpos($iUrl,'/en/product/') !== false){
print "debug 3";
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
	$id = $productPageXpath->query('//span[@id="product-artno"]');
	if ($id->length > 0) {
		$productArray["id"] = $id->item(0)->nodeValue;
		if(strpos($productArray["id"],'Article number:') === 0)$productArray["id"]=substr($productArray["id"],17);
	}	
// BRAND
	$brand = $productPageXpath->query('//h1[@id="product-name"]/text()[following-sibling::br]');
	if ($brand->length > 0) {
		$productArray["brand"] = $brand->item(0)->nodeValue;
	}	
// NAME	
	$name = $productPageXpath->query('//h1[@id="product-name"]/text()[preceding-sibling::br]');
	if ($name->length > 0) {
		$productArray["name"] = $name->item(0)->nodeValue;
	}	
// COLOUR
	$colour = $productPageXpath->query('//span[@id="product-color"]');
	if ($colour->length > 0) {
		$productArray["colour"] = explode("/",$colour->item(0)->nodeValue);
	}	
// DESC
	$productArray["desc"] = "";
// PRICE
	$price = $productPageXpath->query('//div[@class="product-price"]/span');
	if ($price->length > 0) {
		$productArray["price"] = $price->item(0)->nodeValue;
	}	
// SPORT
	$productArray["sport"] = "";
// GENDER
	$gender = $productPageXpath->query('//div[@id="product-info-gender-image"]/img/@src');
	if ($gender->length > 0) {
		$genderURL = $gender->item(0)->nodeValue;
		$genderImg = substr($genderURL, strrpos($genderURL, '/') + 1);
		if($genderImg=="men.png"){
			$productArray["gender"] = "men";
		}else if($genderImg=="kids.png"){
			$productArray["gender"] = "kids";
		}else if($genderImg=="women.png"){
			$productArray["gender"] = "women";
		}else if($genderImg=="unisex.png"){
			$productArray["gender"] = "unisex";
		}else{
			$productArray["gender"] = "";
		}
	}	
// IMAGE
	$productImage = $productPageXpath->query('//a[@id="primary-image-zoom"]/@href');
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