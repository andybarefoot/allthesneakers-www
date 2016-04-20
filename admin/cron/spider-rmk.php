<?php

// site: http://www.rmkstore.com/
$site = "23";


// Declaring arrays
// the listing pages that might contain links to products
$spiderPagesUrls = array(
	"http://www.rmkstore.com/rmk.showProducts.do?orderBy=pd.priority%20desc,%20pd.createdTime%20desc&sortDirection=-1&field=null&key=2&special=0&searchType=3&status=null&name=&startingPos=0&pageSize=60",
	"http://www.rmkstore.com/rmk.showProducts.do?orderBy=pd.priority%20desc,%20pd.createdTime%20desc&sortDirection=-1&field=null&key=2&special=0&searchType=3&status=null&name=&startingPos=60&pageSize=60",
	"http://www.rmkstore.com/rmk.showProducts.do?orderBy=pd.priority%20desc,%20pd.createdTime%20desc&sortDirection=-1&field=null&key=2&special=0&searchType=3&status=null&name=&startingPos=120&pageSize=60",
	"http://www.rmkstore.com/rmk.showProducts.do?orderBy=pd.priority%20desc,%20pd.createdTime%20desc&sortDirection=-1&field=null&key=2&special=0&searchType=3&status=null&name=&startingPos=180&pageSize=60",
	"http://www.rmkstore.com/rmk.showProducts.do?orderBy=pd.priority%20desc,%20pd.createdTime%20desc&sortDirection=-1&field=null&key=2&special=0&searchType=3&status=null&name=&startingPos=240&pageSize=60",
	"http://www.rmkstore.com/rmk.showProducts.do?orderBy=pd.priority%20desc,%20pd.createdTime%20desc&sortDirection=-1&field=null&key=2&special=0&searchType=3&status=null&name=&startingPos=300&pageSize=60",
	"http://www.rmkstore.com/rmk.showProducts.do?orderBy=pd.priority%20desc,%20pd.createdTime%20desc&sortDirection=-1&field=null&key=2&special=0&searchType=3&status=null&name=&startingPos=360&pageSize=60",
	"http://www.rmkstore.com/rmk.showProducts.do?orderBy=pd.priority%20desc,%20pd.createdTime%20desc&sortDirection=-1&field=null&key=2&special=0&searchType=3&status=null&name=&startingPos=420&pageSize=60",
	"http://www.rmkstore.com/rmk.showProducts.do?orderBy=pd.priority%20desc,%20pd.createdTime%20desc&sortDirection=-1&field=null&key=2&special=0&searchType=3&status=null&name=&startingPos=480&pageSize=60",
	"http://www.rmkstore.com/rmk.showProducts.do?orderBy=pd.priority%20desc,%20pd.createdTime%20desc&sortDirection=-1&field=null&key=2&special=0&searchType=3&status=null&name=&startingPos=540&pageSize=60",
	"http://www.rmkstore.com/rmk.showProducts.do?orderBy=pd.priority%20desc,%20pd.createdTime%20desc&sortDirection=-1&field=null&key=2&special=0&searchType=3&status=null&name=&startingPos=600&pageSize=60",
	"http://www.rmkstore.com/rmk.showProducts.do?orderBy=pd.priority%20desc,%20pd.createdTime%20desc&sortDirection=-1&field=null&key=2&special=0&searchType=3&status=null&name=&startingPos=660&pageSize=60",
	"http://www.rmkstore.com/rmk.showProducts.do?orderBy=pd.priority%20desc,%20pd.createdTime%20desc&sortDirection=-1&field=null&key=2&special=0&searchType=3&status=null&name=&startingPos=720&pageSize=60",
	"http://www.rmkstore.com/rmk.showProducts.do?orderBy=pd.priority%20desc,%20pd.createdTime%20desc&sortDirection=-1&field=null&key=2&special=0&searchType=3&status=null&name=&startingPos=780&pageSize=60",
	"http://www.rmkstore.com/rmk.showProducts.do?orderBy=pd.priority%20desc,%20pd.createdTime%20desc&sortDirection=-1&field=null&key=2&special=0&searchType=3&status=null&name=&startingPos=840&pageSize=60",
	"http://www.rmkstore.com/rmk.showProducts.do?orderBy=pd.priority%20desc,%20pd.createdTime%20desc&sortDirection=-1&field=null&key=2&special=0&searchType=3&status=null&name=&startingPos=900&pageSize=60",
	"http://www.rmkstore.com/rmk.showProducts.do?orderBy=pd.priority%20desc,%20pd.createdTime%20desc&sortDirection=-1&field=null&key=2&special=0&searchType=3&status=null&name=&startingPos=960&pageSize=60",
	"http://www.rmkstore.com/rmk.showProducts.do?orderBy=pd.priority%20desc,%20pd.createdTime%20desc&sortDirection=-1&field=null&key=2&special=0&searchType=3&status=null&name=&startingPos=1020&pageSize=60"
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
	$thisPageUrls = $thisPageXPath->query('//figure[@class="product"]/div/a/@href');	// Querying for href attributes of pagination
	// If results exist
	if ($thisPageUrls->length > 0) {
		// For each results page URL
		for ($i = 0; $i < $thisPageUrls->length; $i++) {
			$iUrl=$thisPageUrls->item($i)->nodeValue;
			if (strpos($iUrl,'http://') !== 0)$iUrl='http://www.rmkstore.com'.$iUrl;
			// check if a product page
			if (strpos($iUrl,'/product/') !== false){
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
	$id = $productPageXpath->query('//th[contains(text(),"Model Code")]/following::td');
	if ($id->length > 0) {
		$productArray["id"] = $id->item(0)->nodeValue;
	}	
// BRAND
	$brand = $productPageXpath->query('//th[contains(text(),"Brand")]/following::td');
	if ($brand->length > 0) {
		$productArray["brand"] = $brand->item(0)->nodeValue;
	}	
// NAME	
	$name = $productPageXpath->query('//div[@class="product-page"]/div/h2');
	if ($name->length > 0) {
		$productArray["name"] = $name->item(0)->nodeValue;
	}	
// COLOUR
	$colour = $productPageXpath->query('//th[contains(text(),"Colour")]/following::td');
	if ($colour->length > 0) {
		$productArray["colour"] = explode("/",$colour->item(0)->nodeValue);
	}	
// DESC
	$productArray["desc"] = "";
// PRICE
	$price = $productPageXpath->query('//input[@name="amount"]/@value');
	if ($price->length > 0) {
		$productArray["price"] = $price->item(0)->nodeValue;
	}	
// SPORT
	$productArray["sport"] = "";
// GENDER
	$gender = $productPageXpath->query('(//select[@id="size"]/option)[2]');
	if ($gender->length > 0) {
		$productArray["gender"] = $gender->item(0)->nodeValue;
	}	
// IMAGE
	$productImage = $productPageXpath->query('//img[@class="rsImg rsMainSlideImage"]/@src');
	if ($productImage->length > 0) {
		$imageUrl = $productImage->item(0)->nodeValue;	// Add URL to variable
		if (strpos($imageUrl,'http://www.onitsukatiger.com') !== 0)$imageUrl='http://www.onitsukatiger.com:'.$imageUrl;
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