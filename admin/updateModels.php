<?

require_once '../../includes/db-shoes.php';
require_once '../../includes/dbactions.php';
require_once '../../includes/commonScrapeFunctions.php';

$sqlQuery="SELECT * FROM `sneakers` ORDER BY shoeID DESC LIMIT 0,20000";
$allSneakers=getData($sqlQuery);
foreach ($allSneakers as $allSneaker) {
	$sneakerID=$allSneaker['shoeID'];
	$sneakerBrand=$allSneaker['shoeBrand'];
	$sneakerIsSneaker=$allSneaker['shoeIsSneaker'];
	$sneakerName=$allSneaker['shoeName'];
	$model=getModel($sneakerBrand,$sneakerName);
	print $model['ID'];
	print ':';
	print $model['sneaker'];
	print ':';
	print $sneakerName;
	print '<br>';
	if($model['ID']!=0){
		$sqlStmt1="UPDATE `db73815_shoes`.`sneakers` SET `shoeModel` = ".$model['ID']." WHERE `sneakers`.`shoeID` = $sneakerID;";
		$change1=updateData($sqlStmt1);
		if($sneakerIsSneaker==1){
			$sqlStmt2="UPDATE `db73815_shoes`.`sneakers` SET `shoeIsSneaker` = ".$model['sneaker']."  WHERE `sneakers`.`shoeID` = $sneakerID;";
			$change2=updateData($sqlStmt2);
		}
	}
}
?>