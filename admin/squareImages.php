<?php

ini_set('memory_limit','8M');

require_once '../../includes/db-shoes.php';
require_once '../../includes/dbactions.php';
require_once '../../includes/commonFunctions.php';



$brand=intval($_GET["b"]);
if($brand<1)$brand=0;

//createCroppedImage('../images/products/2/downloaded/13527.jpg',1000,1000,'../images/products/0/1000sq/18092.jpg','middle', 'f5f5f5');
?>
<script>
setInterval(function() {
                  window.location.reload();
 //                 alert("reload");
}, 300000); 
</script>
<?php
function listFolderFiles($dir){
    $ffs = scandir($dir);
    echo '<ol>';
    $imageCount=0;
    foreach($ffs as $ff){
        if($ff != '.' && $ff != '..'){
            echo '<li>'.$dir.'/'.$ff;
            if(is_dir($dir.'/'.$ff)) listFolderFiles($dir.'/'.$ff);
            if(strpos($dir, 'downloaded') === false){
            	echo ' - not download';
            } else if(strpos($ff, '.jpg') === false){
            	echo ' - not jpg';
            } else {
            	echo ' - IMAGE';
            	$destination = str_replace("downloaded", "1000sq", $dir);
            	if(!file_exists($destination)){
            		mkdir($destination, 0755);
            	}
	          	if(!file_exists($destination.'/'.$ff)){
					createCroppedImage($dir.'/'.$ff,1000,1000,$destination.'/'.$ff,'middle', 'f5f5f5');
	            	echo ' - MAKE 1000 sq';
                    $imageCount++;
            	}
            	$destination = str_replace("downloaded", "250sq", $dir);
            	if(!file_exists($destination)){
            		mkdir($destination, 0755);
            	}
	          	if(!file_exists($destination.'/'.$ff)){
					createCroppedImage($dir.'/'.$ff,250,250,$destination.'/'.$ff,'middle', 'f5f5f5');
	            	echo ' - MAKE 250 sq';
                    $imageCount++;
            	}
            }
            echo '</li>';
        }
        if($imageCount>250){
            break;
        }
    }
    echo '</ol>';
}

if($brand==0){
	listFolderFiles('../images/products/');
} else {
	listFolderFiles('../images/products/'.$brand.'/downloaded/');
}

?>