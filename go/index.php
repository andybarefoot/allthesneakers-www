<?
$productID=intval($_GET["id"]);

require_once '../../includes/db-shoes.php';
require_once '../../includes/dbactions.php';

$sqlQuery="
SELECT  `productURL` 
FROM  `products` 
WHERE  `productID` =".$productID;
$goResult=getData($sqlQuery);

if(count($goResult)>0){
	$goURL=$goResult[0]['productURL'];
	$now=getdate();
	$sqlStmt="INSERT INTO `db73815_shoes`.`referrals` (`referralID`, `referralProduct`, `referralDate`) VALUES (NULL, '$productID', now());";
	$thisReferral=insertDataReturnID($sqlStmt);
}else{
	$goURL="http://www.allthesneakers.com";	
}

header("HTTP/1.1 301 Moved Permanently"); 
header("Location: ".$goURL); 

?>
