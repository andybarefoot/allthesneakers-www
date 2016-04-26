<?
$actual_link1 = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
$actual_link2 = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
$actual_link3 =  "//$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

$sneakerID=intval($_GET["id"]);

require_once '../includes/db-shoes.php';
require_once '../includes/dbactions.php';

$sqlQuery="
SELECT *
FROM `sneakers`,`products`,`brands`,`sites`
WHERE  `shoeBrand`=`brandID` AND `productSneaker`=`shoeID` AND `productSite`=`siteID` AND `shoeLive`=1  AND `shoeID`=".$sneakerID." 
ORDER BY `products`.`productPrice` ASC
LIMIT 0,100";
$shoes=getData($sqlQuery);
$sneakerLocalID=$shoes[0]['shoeLocalID'];
$sneakerName=$shoes[0]['shoeName'];
$sneakerImage=$shoes[0]['shoeImage'];
$sneakerDesc=$shoes[0]['shoeDesc'];
$sneakerBrand=$shoes[0]['brandName'];
$sneakerModel=$shoes[0]['shoeModel'];

if($sneakerModel!=0){
	$sqlQueryRelated="
	SELECT count(*) AS `productsNum`, `shoeID`, `productURL`, `shoeThumb`, `shoeName`, `brandName`, MIN(`productPrice`) AS 'productPrice'
	FROM `sneakers`,`products`,`brands` 
	WHERE  `shoeModel`=".$sneakerModel." AND `shoeBrand`=`brandID` AND `productSneaker`=`shoeID` AND `shoeIsSneaker`=1 AND `shoeLive`=1 AND `productLive`=1  AND `shoeID`!=".$sneakerID." 
	GROUP BY `shoeLocalID` 
	ORDER BY RAND() 
	LIMIT 10";
}else{
	$sqlQueryRelated="
	SELECT count(*) AS `productsNum`, `shoeID`, `productURL`, `shoeThumb`, `shoeName`, `brandName`, MIN(`productPrice`) AS 'productPrice'
	FROM `sneakers`,`products`,`brands` 
	WHERE  `shoeBrand`=`brandID` AND `productSneaker`=`shoeID` AND `shoeLive`=1 AND `productLive`=1  AND `shoeID`!=".$sneakerID." 
	GROUP BY `shoeLocalID` 
	ORDER BY RAND() 
	LIMIT 10";
}

$related=getData($sqlQueryRelated);

?>
<!doctype html>
<html lang="en"> 
<head>
	<meta charset="UTF-8">
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<meta name="viewport" content="initial-scale=1.0, width=device-width, minimal-ui" />		
	<meta name="apple-mobile-web-app-capable" content="yes" />
	<meta name="apple-mobile-web-app-status-bar-style" content="black" />
	<title>All The Sneakers:  <? echo $sneakerName; ?> (<? echo $sneakerBrand; ?>: <? echo $sneakerLocalID; ?>)</title>
	<script type="text/javascript" src="/common/jquery/jquery-1.8.2.min.js"></script>
	<script type="text/javascript" src="/common/jquery/hammer.min.js"></script>
	<link rel="stylesheet" type="text/css" href="/styles/reset.css">
	<link rel="stylesheet" type="text/css" href="/styles/sneaker.css" />
<!--	<link href='http://fonts.googleapis.com/css?family=Roboto+Slab:300' rel='stylesheet' type='text/css'> -->
	<link href='https://fonts.googleapis.com/css?family=Lato:400,100,300' rel='stylesheet' type='text/css'>

	<link rel="apple-touch-icon" sizes="57x57" href="/images/ios-icons/apple-icon-57x57.png">
	<link rel="apple-touch-icon" sizes="60x60" href="/images/ios-icons/apple-icon-60x60.png">
	<link rel="apple-touch-icon" sizes="72x72" href="/images/ios-icons/apple-icon-72x72.png">
	<link rel="apple-touch-icon" sizes="76x76" href="/images/ios-icons/apple-icon-76x76.png">
	<link rel="apple-touch-icon" sizes="114x114" href="/images/ios-icons/apple-icon-114x114.png">
	<link rel="apple-touch-icon" sizes="120x120" href="/images/ios-icons/apple-icon-120x120.png">
	<link rel="apple-touch-icon" sizes="144x144" href="/images/ios-icons/apple-icon-144x144.png">
	<link rel="apple-touch-icon" sizes="152x152" href="/images/ios-icons/apple-icon-152x152.png">
	<link rel="apple-touch-icon" sizes="180x180" href="/images/ios-icons/apple-icon-180x180.png">
	<link rel="icon" type="image/png" sizes="192x192"  href="/images/ios-icons/android-icon-192x192.png">
	<link rel="icon" type="image/png" sizes="32x32" href="/images/ios-icons/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="96x96" href="/images/ios-icons/favicon-96x96.png">
	<link rel="icon" type="image/png" sizes="16x16" href="/images/ios-icons/favicon-16x16.png">
	<link rel="manifest" href="/manifest.json">
	<meta name="msapplication-TileColor" content="#ffffff">
	<meta name="msapplication-TileImage" content="/ms-icon-144x144.png">
	<meta name="theme-color" content="#ffffff">	

	<script>

		// Google Analytics
		(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
		(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
		m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
		})(window,document,'script','//www.google-analytics.com/analytics.js','ga');
		ga('create', 'UA-56778069-1', 'auto');
		ga('send', 'pageview');
		// end GA
		
		// register custom elements
		var cardShoe = document.registerElement('card-shoe');

		var prevID=false;
		var nextID=false;

		// ensure document is loaded before attempting any jQuery
		$(function(){
			thisID = "<? echo $sneakerID; ?>";
//			console.log(localStorage.productsList);
			idArray = localStorage.productsList.split("/");
			idArray.pop();
			thisIndex = idArray.indexOf(thisID);
//			console.log("This index: "+thisIndex);
			prevIndex = thisIndex-1;
			nextIndex = thisIndex+1;
//			console.log("Prev index: "+prevIndex);
//			console.log("Nexy index: "+nextIndex);
			if(thisIndex!=-1){
				if(idArray.length>1){
					if(nextIndex==idArray.length-1)nextIndex=0;
					if(prevIndex<0)prevIndex=idArray.length-1;
				}else{
				    onlyResult();
					prevIndex = thisIndex;
					nextIndex = thisIndex;
				}
//				console.log("Prev index: "+prevIndex);
//				console.log("Nexy index: "+nextIndex);
				prevID = idArray[prevIndex];
				nextID = idArray[nextIndex];
				updateNextPrevNav(prevID,nextID);
			}else{
			    removeNextPrevNav();
			}
//			console.log(nextID);
//			console.log(prevID);
			var a = document.getElementById("goNext");
			var b = document.getElementById("goPrev");
			a.onclick = function() {
				nextSneaker();
				return false;
			}
			b.onclick = function() {
				prevSneaker();
				return false;
			}
			delete Hammer.defaults.cssProps.userSelect;
			var myElement = document.getElementById('sneaker');
			var swipeControl = new Hammer(myElement);
			swipeControl.get('tap').set({ enable: false });
			swipeControl.get('doubletap').set({ enable: false });
			swipeControl.get('press').set({ enable: false });
			swipeControl.get('pan').set({ enable: false });
			swipeControl.get('swipe').set({ enable: true });
			swipeControl.get('pinch').set({ enable: false });
			swipeControl.get('rotate').set({ enable: false });
			swipeControl.on("swipeleft", function() {
				nextSneaker();
			});
			swipeControl.on("swiperight", function() {
				prevSneaker();
			});

		});
		function updateNextPrevNav(prevID,nextID){
			$('body').keydown(function(e) {
//				console.log(e.keyCode);
				var tag = e.target.tagName.toLowerCase();
				if ( e.keyCode == 37 && tag != 'input'){ //LEFT
//					console.log(nextID);
//					console.log('/sneaker/'+nextID+'/');
					prevSneaker();
				}
				if ( e.keyCode == 39 && tag != 'input'){ //RIGHT
//					console.log(prevID);
//					console.log('/sneaker/'+prevID+'/');
					nextSneaker();
				}
			});
		}
		function removeNextPrevNav(){
		    document.getElementById('headerBarStandalone').style.display = 'block';
		    document.getElementById('headerBar').style.display = 'none';
		}
		function onlyResult(){
		}
		function nextSneaker(){
			window.location.href = '/sneaker/'+nextID+'/';
		}
		function prevSneaker(){
			window.location.href = '/sneaker/'+prevID+'/';
		}
	</script>
</head>
<body>
<header>
	<div id="headerBar">
		<div id="nextShoe"><a id="goNext" href="#">Next &gt;&gt;</a></div>
		<div id="prevShoe"><a id="goPrev" href="#">&lt;&lt; Previous</a><span id="backText2">&nbsp;&nbsp;&nbsp;&nbsp;<a href="/">Back</a></span></div>
		<a href="/"><img class="logo" src="/images/all-the-sneakers.png" /></a><h1 class="sneaker"><span id="backText1"><a href="/">Back</a></span><span id="nameText"><? echo $sneakerName; ?> </span><span id="idText">(<? echo $sneakerLocalID; ?>)</span></h1>
	</div>
	<div id="headerBarStandalone">
		<h1>
			<div id="nextShoe"><a href="/">All The Sneakers ></a></div>
			<a href="/"><img class="logo" src="/images/all-the-sneakers.png" /></a><span id="nameText"><? echo $sneakerName; ?> </span><span id="idText">(<? echo $sneakerLocalID; ?>)</span>&nbsp;
		</h1>
	</div>
</header>

<div id="sneaker">
	<div id="sneakerImage">
		<img alt="<? echo $sneakerBrand; ?>: <? echo preg_replace("/[^A-Za-z0-9 ]/", '', $sneakerName); ?> (<? echo $sneakerLocalID; ?>)" src="<? echo $sneakerImage; ?>" />
	</div>
	<div id="sneakerDetails">
		<h2><? echo $sneakerName; ?></h2>
		<h3><? echo $sneakerBrand; ?>: <? echo $sneakerLocalID; ?></h3>
<?

$isAvailable=false;
$descPhotoStr="";
$linksStr="";
$descLink=false;
$photoLink=false;
for($i=0;$i<count($shoes);$i++){
	if($shoes[$i]['productLive']==1){
		$linksStr.= '<li><a class="buyLink" href="http://www.allthesneakers.com/go/?id='.$shoes[$i]['productID'].'" target="_blank">$'.$shoes[$i]['productPrice'].' at '.$shoes[$i]['siteName'].'</a></li>';
		$isAvailable=true;
	}
	if($shoes[$i]['siteID']==$shoes[$i]['shoeImageSite']){
		$photoName=$shoes[$i]['siteName'];
		$photoURL=$shoes[$i]['siteURL'];
		$photoLink=true;
	}
	if($shoes[$i]['siteID']==$shoes[$i]['shoeDescSite']){
		$descName=$shoes[$i]['siteName'];
		$descURL=$shoes[$i]['siteURL'];
		$descLink=true;
	}
}
if($photoLink){
	if(($descLink)&&(strlen($sneakerDesc)>50)){
		if($descURL==$photoURL){
			$descPhotoStr.= 'Photo & description from <a href="'.$descURL.'" target="_blank">'.$descName.'</a>:<br/><br/>';
		}else{
			$descPhotoStr.= 'Photo from <a href="'.$photoURL.'" target="_blank">'.$photoName.'</a> & description from <a href="'.$descURL.'" target="_blank">'.$descName.'</a>:<br/><br/>';
		}
	}else{
		$descPhotoStr.= 'Photo from <a href="'.$photoURL.'" target="_blank">'.$photoName.'</a><br/><br/>';
	}
}else if(($descLink)&&(strlen($sneakerDesc)>50)){
	$descPhotoStr.= 'Description from <a href="'.$descURL.'" target="_blank">'.$descName.'</a>:<br/><br/>';
}
if(!$isAvailable)$linksStr.='<li>No online stockists found</li>';

echo "<p>";
echo $descPhotoStr;
if(strlen($sneakerDesc)>50){
	echo $sneakerDesc;
}
echo "</p>";
?>
		<ul>
<?
	echo $linksStr;
?>
			
		</ul>
	</div>
	<div id="relatedSneakers">
		<h4>More sneakers</h4>
<?
// echo $sqlQuery; 
for($i=0;$i<count($related);$i++){
	$urlProductName = preg_replace('/^-+|-+$/', '', strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $related[$i]['shoeName'])));
	$urlBrandName = preg_replace('/^-+|-+$/', '', strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $related[$i]['brandName'])));
	$sneakerName=$related[$i]['shoeName'];
	while(strlen($sneakerName)>50){
		$sneakerName=substr($sneakerName,0,strrpos($sneakerName,' ')).'...';
	}
	echo '<card-shoe class="related'.$i.'" id="'.$related[$i]['shoeID'].'">';
	echo '<a class="fancybox fancybox.ajax" rel="group" href="/sneaker/'.$related[$i]['shoeID'].'/'.$urlBrandName.'/'.$urlProductName.'"><img alt="'.$related[$i]['brandName'].': '.preg_replace("/[^A-Za-z0-9 ]/", '', $sneakerName).'" src="http://www.allthesneakers.com'.$related[$i]['shoeThumb'].'"></a>';
	echo '<div class="shoeDetails"><span class="shoeName">'.$sneakerName.'</span><br/>';
	echo '<span class="shoeBrand">'.$related[$i]['brandName'].'</span>';
	echo '<span class="shoePrice">$'.$related[$i]['productPrice'].'</span>';
	echo '</div></card-shoe>';
}
?>		
	</div>
	<div id="hiddenLinks">
<?
	for($i=0;$i<count($shoes);$i++){
		if($shoes[$i]['productLive']==1){
			echo '<a href="'.$shoes[$i]['productURL'].'" target="_blank"><img src="images/trans.png" width="100" height="10"/></a>';
			$isAvailable=true;
		}
	}
?>
	</div>
</div>
<script>
</script>
</body>
</html>
