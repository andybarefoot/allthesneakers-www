<?php
require_once '../../includes/db-shoes.php';
require_once '../../includes/dbactions.php';


$sqlQuerySelect="SELECT count(*) AS `productsNum`, `shoeID`, `productURL`, `shoeThumb`, `shoeName`, `brandName`, MIN(`productPrice`) AS 'productPrice'
FROM `sneakers`,`products`,`brands` 
WHERE  `shoeBrand`=`brandID` AND `productSneaker`=`shoeID` AND `shoeLive`=1 AND `productLive`=1 ";

$sqlQueryGroup="GROUP BY `shoeLocalID` ";
$sqlQueryOrder="ORDER BY `sneakers`.`shoeID` DESC, `products`.`productPrice` DESC ";

$sqlQuery=$sqlQuerySelect.$sqlQueryFilter.$sqlQueryGroup.$sqlQueryOrder.$sqlQueryLimit;

$shoes=getData($sqlQuery);

// echo $sqlQuery; 
for($i=0;$i<count($shoes);$i++){
	$urlProductName = preg_replace('/^-+|-+$/', '', strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $shoes[$i]['shoeName'])));
	$urlBrandName = preg_replace('/^-+|-+$/', '', strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $shoes[$i]['brandName'])));
	echo '&lt;url&gt;&lt;loc&gt;http://www.allthesneakers.com/sneaker/'.$shoes[$i]['shoeID'].'/'.$urlBrandName.'/'.$urlProductName.'&lt;/loc&gt;&lt;/url&gt;<br/>';
}
?>
