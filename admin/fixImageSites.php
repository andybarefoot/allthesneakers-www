<?

require_once '../../includes/db-shoes.php';
require_once '../../includes/dbactions.php';

$sqlQuery="
SELECT count(*) AS 'number',`shoeID`,`shoeLocalID`,`shoeImageSite`,`shoeDescSite`,`productSite`
FROM `sneakers`,`products`
WHERE  `productSneaker`=`shoeID` AND `shoeImageSite`=0
GROUP BY `shoeID`
ORDER BY `shoeID` ASC
LIMIT 0,20000";
$emptySites=getData($sqlQuery);
$count=0;
$changed=0;
foreach ($emptySites as $emptySite) {
	$count++;
	$productSite=$emptySite['productSite'];
	$number=$emptySite['number'];
	$shoeID=$emptySite['shoeID'];
	if($number==1){
		$sqlStmt="UPDATE  `db73815_shoes`.`sneakers` SET  `shoeImageSite` =  '$productSite', `shoeDescSite` =  '$productSite' WHERE  `sneakers`.`shoeID` =$shoeID;";
		$change=updateData($sqlStmt);
		$changed=$changed+$change;
	}
}
echo $changed."/".$count."<br/>";
?>