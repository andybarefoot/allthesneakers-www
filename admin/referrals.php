<?

require_once '../../includes/db-shoes.php';
require_once '../../includes/dbactions.php';

$sqlQuery="
SELECT count(*) AS total, YEAR(`referralDate`) AS year, MONTH(`referralDate`) AS month, DAY(`referralDate`) AS day 
FROM `referrals` 
GROUP BY year, month, day 
ORDER BY year ASC, month ASC, day ASC";
$referrals=getData($sqlQuery);

$sqlQuery="
SELECT count(*) AS total, `siteName`,`productSite`,`productURL`, YEAR(`referralDate`) AS year, MONTH(`referralDate`) AS month 
FROM `referrals`,`products`,`sites`
WHERE `productID`=`referralProduct` AND `productSite`=`siteID`
GROUP BY year, month,  `productSite`
ORDER BY year ASC, month ASC, productSite ASC";
$siteReferrals=getData($sqlQuery);

$sqlQuery="
SELECT * 
FROM  `sites`
ORDER BY siteID ASC";
$sites=getData($sqlQuery);


?>
<!doctype html>
<html lang="en"> 
<head>
	<meta charset="UTF-8">
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<meta name="viewport" content="initial-scale=1.0, width=device-width" />		
	<title>All The Sneakers: Referrals</title>
	<link rel="stylesheet" type="text/css" href="/styles/reset.css">
	<link rel="stylesheet" type="text/css" href="/styles/general.css" />
	<link rel="stylesheet" type="text/css" href="/styles/admin.css" />
	<link href='http://fonts.googleapis.com/css?family=Roboto+Slab:300' rel='stylesheet' type='text/css'>
	<script type="text/javascript" src="https://www.google.com/jsapi"></script>
   <script type="text/javascript">
      google.load("visualization", "1.1", {packages:["bar"]});
      google.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = google.visualization.arrayToDataTable([
          ['Date', 'Referrals']
<?
foreach ($referrals as $referral) {
	$total=$referral['total'];
	$year=$referral['year'];
	$month=$referral['month'];
	$day=$referral['day'];
	echo ",['".$day."/".$month."/".$year."', ".$total."]";
}
?>
        ]);
        var options = {
          chart: {
            title: 'Number of referrals',
            subtitle: '(Clicks on links to external stores from allthesneakers.com)',
          }
        };
        var chart = new google.charts.Bar(document.getElementById('columnchart_material'));
        chart.draw(data, options);
      }
    </script>
<script type="text/javascript">
    google.load('visualization', '1.1', {packages: ['line']});
    google.setOnLoadCallback(drawChart);

    function drawChart() {

      var data = new google.visualization.DataTable();
      data.addColumn('string', 'Date');
<?
foreach ($sites as $site) {
	$siteName=$site['siteName'];
	echo "data.addColumn('number', '".$siteName."');";
}

$siteRefArray=array();

$i=0;
$thisMonthYear="";
foreach ($siteReferrals as $siteReferral) {
	$total=$siteReferral['total'];
	$site=$siteReferral['productSite'];
	$month=$siteReferral['month'];
	$year=$siteReferral['year'];
	$monthYear=$month."/".$year;
	if($thisMonthYear!=$monthYear)$i++;
	$thisMonthYear=$monthYear;
	$siteRefArray[$i][0]=$monthYear;
	$siteRefArray[$i][$site]=$total;
}
?>

      data.addRows([
<?
$i=0;
foreach ($siteRefArray as $siteRef) {
	echo "['".$siteRef[0]."'";
		foreach ($sites as $site) {
			$siteID=$site['siteID'];
			if($siteRef[$siteID]){
				echo ",".$siteRef[$siteID];
			}else{
				echo ",0";
			}
		}
	echo"]";	
	if($i<count($siteRefArray))echo",";	
	$i++;
}
?>
]);

      var options = {
        chart: {
          title: 'Referrals per site',
          subtitle: '(Clicks on links to external stores from allthesneakers.com)'
        },
        width: 900,
        height: 700
      };

      var chart = new google.charts.Line(document.getElementById('linechart_material'));

      chart.draw(data, options);
    }
  </script>
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
	<div class="chart" id="columnchart_material"></div>
	<div class="chart" id="linechart_material"></div>
</div>
</body>
</html>
