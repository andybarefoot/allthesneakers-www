<?

require_once '../../includes/db-shoes.php';
require_once '../../includes/dbactions.php';

$sqlQuery="SELECT * FROM `sneakers` ORDER BY shoeID";
$allSneakers=getData($sqlQuery);
foreach ($allSneakers as $allSneaker) {
	$sneakerID=$allSneaker['shoeID'];
	$sqlStmt="UPDATE `db73815_shoes`.`sneakers` SET `shoeCred` = (SELECT AVG( siteCred ) AS cred FROM `products` , `sites` WHERE `productSite` = `siteID` AND `productSneaker` =$sneakerID) WHERE `sneakers`.`shoeID` = $sneakerID;";
	$change=updateData($sqlStmt);
	$changed=$changed+$change;
}
?>