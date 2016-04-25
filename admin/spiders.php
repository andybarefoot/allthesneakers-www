<?

require_once '../../includes/db-shoes.php';
require_once '../../includes/dbactions.php';

if($_POST['spiderUpdate']=="yes"){
	foreach ($_POST as $key => $value) {
		if($key!='spiderUpdate'){
			$keyValues = explode("-", $key);
			$cv=$keyValues[0];
			$dv=$keyValues[1];
			$hv=$keyValues[2];
			$sqlQuery="UPDATE  `db73815_shoes`.`spiders` SET  `spiderScript` =  '$value' WHERE  `spiders`.`spiderCron` =$cv AND `spiders`.`spiderDay` =$dv AND `spiders`.`spiderHour` =$hv;";
			$spiders=updateData($sqlQuery);
		}
	}
	
}

$sqlQuery="SELECT * , SUBSTRING_INDEX(  `spiderScript` ,  '-', -1 ) AS  'action'
FROM  `spiders` ,  `sites` 
WHERE  `sites`.`siteID` = SUBSTRING_INDEX(  `spiderScript` ,  '-', 1 ) 
ORDER BY  `spiders`.`spiderHour` ASC ,  `spiders`.`spiderDay` ASC";
$spiders=getData($sqlQuery);
$cells = array();

foreach ($spiders as $spider) {
	$cron=$spider['spiderCron'];
	$day=$spider['spiderDay'];
	$hour=$spider['spiderHour'];
	$script=$spider['spiderScript'];
	$id=$spider['siteID'];
	$name=$spider['siteName'];
	$action=$spider['action'];
	$cells[$cron][$day][$hour][0]=$name;
	$cells[$cron][$day][$hour][1]=$action;
	$cells[$cron][$day][$hour][2]=$id;
}

$sqlQuery="SELECT * 
FROM  `sites` 
ORDER BY  `sites`.`siteName` ASC";
$sites=getData($sqlQuery);

$sqlQuery="SELECT * 
FROM  `scans` 
ORDER BY  `scans`.`scanTime` DESC
LIMIT 0,200";
$scans=getData($sqlQuery);

?>
<!doctype html>
<html lang="en"> 
<head>
	<meta charset="UTF-8">
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<meta name="viewport" content="initial-scale=1.0, width=device-width" />		
	<title>All The Sneakers: Spider Scheulde</title>
	<link rel="stylesheet" type="text/css" href="/styles/reset.css">
	<link rel="stylesheet" type="text/css" href="/styles/general.css" />
	<link href='http://fonts.googleapis.com/css?family=Roboto+Slab:300' rel='stylesheet' type='text/css'>
<style>
	table{
		background-color: white;
		margin: 20px;
	}
	td{
		text-align: center;
		padding: 2px;
	}
</style>
</head>
<body>
<div class="header">
	<div id="headerBar">
		<h1><a href="spiders.php">Spider Schedule</a> | <a href="referrals.php">Referrals</a></h1>
	</div>
</div>
<div id="content">
	<form id="spiderForm" action="spiders.php" method="POST">
	<table border="1">
		<tr>
			<td>Hour</td>
			<td>SUN</td>
			<td>MON</td>
			<td>TUE</td>
			<td>WED</td>
			<td>THU</td>
			<td>FRI</td>
			<td>SAT</td>
		</tr>
		<tr>
<?
/*
	for ($i = 0; $i < 7; $i++) {
		for ($j = 1; $j <= 5; $j++) {
			echo '<td>CRON '.$j.'</td>';
		}
	}
 */
?>
		</tr>
<?
for ($h = 0; $h < 24; $h++) {
	echo '<tr>';
	echo '<td>'.$h.'</td>';
	for ($d = 0; $d < 7; $d++) {
//		for ($c = 1; $c <= 5; $c++) {
			echo '<td><select name="1-'.$d.'-'.$h.'">';
			foreach ($sites as $site) {
				$id=$site['siteID'];
				$name=$site['siteName'];
				$s1="";
				$s2="";
				if($cells[1][$d][$h][2]==$id){
					if($cells[1][$d][$h][1]=="a"){
						$s1=" selected";
					}
					if($cells[1][$d][$h][1]=="b"){
						$s2=" selected";
					}
				}
				echo '<option'.$s1.' value="'.$id.'-a">+'.$name.'</option>';				
				echo '<option'.$s2.' value="'.$id.'-b">-'.$name.'</option>';				
			}
			echo '</select></td>';				
			//		}
	}
	echo '</tr>';
}
?>
	</table>
	<input type="hidden" name="spiderUpdate" value="yes"/>
	<button type="submit" form="spiderForm" value="Submit">Submit</button>
	</form>
	Last 200 scans:
	<table>
		<tr>
		<td>Time</td>
		<td>Script</td>
		<td>Found (Unique)</td>
		<td>Spidered</td>
		<td>New Shoes</td>
		<td>New on site</td>
		</tr>
<?
foreach ($scans as $scan) {
	$time=$scan['scanTime'];
	$script=$scan['scanScript'];
	$found=$scan['scanFound'];
	$unique=$scan['scanUnique'];
	$spidered=$scan['scanSpidered'];
	$start=$scan['scanStart'];
	$end=$scan['scanEnd'];
	$newShoe=$scan['scanNewShoe'];
	$newSite=$scan['scanNewStockist'];
	echo '<tr><td>'.$time.'</td><td>'.$script.'</td><td>'.$found.'('.$unique.')</td><td>'.$spidered.'('.$start.'-'.$end.')</td><td>'.$newShoe.'</td><td>'.$newSite.'</td></tr>';
}
?>		
	</table>


</div>
</body>
</html>
