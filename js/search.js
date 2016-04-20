var countryArray = new Array();

var ethnicArray = new Array();

var bandArray = new Array();
var filmArray = new Array();
var bookArray = new Array();
var tvArray = new Array();

var dateArray=new Array();
var themeArray=new Array();

var colours = new Array('ffff00','ff00ff','00ffff');

var currentPage = 1;
var totalPages = 1;


function generatePageNav(){
	var pageOffset = 0;
	var pageTotal = totalPages;
	if(totalPages>11){
		pageOffset=currentPage-6;
		if(pageOffset<0)pageOffset=0;
		if((pageOffset+11)>totalPages)pageOffset=totalPages-11;
		pageTotal=11;
	}
	if (document.getElementById || document.all) {
		var el = document.getElementById? document.getElementById("pageNav"): document.all["pageNav"];
		if (el && typeof el.innerHTML != "undefined") el.innerHTML = "";
		shtml='';
		shtml+='<a href="#" onClick="gotoPage(1);">&lt;&lt;</a> ';
		for(i=1;i<=pageTotal;i++){
			j=i+pageOffset;
			if(j==currentPage){
				shtml+=j+' ';
			}else{
				shtml+='<a href="#" onClick="gotoPage('+j+');">'+j+'</a> ';
			}
		}
		shtml+='<a href="#" onClick="gotoPage('+totalPages+');">&gt;&gt;</a>';
		if (el && typeof el.innerHTML != "undefined") el.innerHTML = shtml;
	}
}

function gotoPage(pageNo){
	currentPage=pageNo;
	fillShoes();
}

function removeOverlay(){
	if (document.getElementById || document.all) {
		for(i=0;i<dateArray.length;i++){
			var el = document.getElementById? document.getElementById('o'+i): document.all['o'+i];
			el.style.visibility='hidden';
		}
	}
}

function showDisplay(){
	if (document.getElementById || document.all) {
		var el = document.getElementById? document.getElementById('displayForm'): document.all['displayForm'];
		el.style.visibility='visible';
		var el = document.getElementById? document.getElementById('filtersForm'): document.all['filtersForm'];
		el.style.visibility='hidden';
	}
}

function showFilters(){
	if (document.getElementById || document.all) {
		var el = document.getElementById? document.getElementById('filtersForm'): document.all['filtersForm'];
		el.style.visibility='visible';
		var el = document.getElementById? document.getElementById('displayForm'): document.all['displayForm'];
		el.style.visibility='hidden';
	}
}

function filter(num){
	if(num==1){
		dateArray = dateArray1;
	}
	if(num==2){
		dateArray = dateArray2;
	}
	if(num==3){
		dateArray = dateArray3;
	}
	fillShoes();
}

function showInfo(infoType){
	if (document.getElementById || document.all) {
		for(i=0;i<dateArray.length;i++){
			var el = document.getElementById? document.getElementById("o"+i): document.all["o"+i];
			if (el && typeof el.innerHTML != "undefined") el.innerHTML = "";
			shtml='<ul><li>Radiohead</li><li>We are scientists</li><li>Suede</li><li>Arctic Monkeys</li><li>Anthrax</li></ul>';
			if (el && typeof el.innerHTML != "undefined") el.innerHTML += shtml;
			el.style.visibility='visible';
		}
	}
//	for(i=0;i<dateArray.length;i++){
//		getInfo(i,dateArray[i][0],infoType);
//	}
}

function getInfo(date,user,infoType){
	var infoURL="/flirt/ajax/infotext.php?u="+user+"&t="+infoType;
	var httpreq = getHTTPObject();
	httpreq.open("GET",infoURL, true);
	httpreq.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");                  
	httpreq.onreadystatechange=function(){
		if(httpreq.readyState==4){
			var infoXML = httpreq.responseXML;
			displayInfo(date,infoXML);
		}
	}
	httpreq.send(null);
}

function displayInfo(date,infoXML){
	var info = infoXML.getElementsByTagName('info');
	shtml='<span class="infotxt"><b>';
	shtml+=info[0].getElementsByTagName('title')[0].firstChild.data;
	shtml+='</b></span>';
	if(info[0].childNodes.length<4){
		n=info[0].childNodes.length-1;
	}else{
		n=3;
	}
	for(var i=0;i<n;i++){
		var id= info[0].getElementsByTagName('text')[i].getAttribute('id');
		var name=info[0].getElementsByTagName('text')[i].firstChild.data;
		shtml+="<br/><span class=\"spacertxt\">&nbsp;</span><br/><span class=\"infotxt\">"+name+"</span>";
	}
	if(info[0].childNodes.length>3){
		shtml+="<br/><span class=\"spacertxt\">&nbsp;</span><br/><span class=\"infotxt\"><a href=\"showDetails();\">more...</a></span>";
	}
	id="i"+date;
	matchID="m"+date;
	textID="t"+date;
	if (document.getElementById || document.all) {
		var el = document.getElementById? document.getElementById(id): document.all[id];
		if (el && typeof el.innerHTML != "undefined") el.innerHTML = "";
		if (el && typeof el.innerHTML != "undefined") el.innerHTML += shtml;
		var matchEL = document.getElementById? document.getElementById(matchID): document.all[matchID];
		var textEL = document.getElementById? document.getElementById(textID): document.all[textID];
		for(j=0;j<el.childNodes.length;j++){
			el.childNodes[j].style.backgroundColor=colours[dateArray[date][1]-1];
		}
	}
}

function processArray(){
	id="matches";
	if (document.getElementById || document.all) {
		var el = document.getElementById? document.getElementById(id): document.all[id];
		if (el && typeof el.innerHTML != "undefined") el.innerHTML = "";
		for(i=0;i<dateArray.length;i++){
			shtml='<div id="m'+i+'" class="match"><div id="n'+i+'" class="matchName">'+dateArray[i][2]+'</div><div id="p'+i+'" class="matchPhoto"><a href="'+dateArray[i][4]+'" rel="lightbox" title="'+dateArray[i][2]+'" alt="'+dateArray[i][2]+'"><img src="'+dateArray[i][3]+'" /></a></div><div id="l'+i+'" class="matchLinks"><a href="'+dateArray[i][4]+'" rel="lightbox" title="'+dateArray[i][2]+'" alt="'+dateArray[i][2]+'">Details</a> | <a href="'+dateArray[i][5]+'"target="_blank">Buy</a></div></div>';
			if (el && typeof el.innerHTML != "undefined") el.innerHTML += shtml;
		}
	}
	initLightbox();
}
	
function processXML(xmlDoc){
	dateArray.length=0;
	var pages = xmlDoc.getElementsByTagName('pageData');
	currentPage=pages[0].getElementsByTagName('currentPage')[0].firstChild.data;
	totalPages=pages[0].getElementsByTagName('totalPages')[0].firstChild.data;
	var shoes = xmlDoc.getElementsByTagName('shoe');
	for(var i=0;i<shoes.length;i++){
		var id=shoes[i].getElementsByTagName('id')[0].firstChild.data;
		var brand=shoes[i].getElementsByTagName('brand')[0].firstChild.data;
		var name=shoes[i].getElementsByTagName('name')[0].firstChild.data;
		var thumb=shoes[i].getElementsByTagName('thumb')[0].firstChild.data;
		var image=shoes[i].getElementsByTagName('image')[0].firstChild.data;
		var url=shoes[i].getElementsByTagName('url')[0].firstChild.data;
		var desc=shoes[i].getElementsByTagName('desc')[0].firstChild.data;
		var price=shoes[i].getElementsByTagName('price')[0].firstChild.data;
		var sport=shoes[i].getElementsByTagName('sport')[0].firstChild.data;
		var colour1=shoes[i].getElementsByTagName('colour1')[0].firstChild.data;
		var colour2=shoes[i].getElementsByTagName('colour2')[0].firstChild.data;
		var colour3=shoes[i].getElementsByTagName('colour3')[0].firstChild.data;
		var profile=shoes[i].getElementsByTagName('profile')[0].firstChild.data;
		userArray=new Array(id,brand,name,thumb,image,url,desc,price,sport,colour1,colour2,colour3,profile);
		dateArray.push(userArray);
	}
	generatePageNav();
	processArray();	
}

function brandsAll(id){
//	alert('brandsAll:'+id);
	if(id==1){	
		if(document.filters.brandAll.checked){
			for(i=0;i<document.filters.brandNo.value;i++){
				document.filters.brand[i].checked=true;
			}
		}else if(!document.filters.brandAll.checked){
			for(i=0;i<document.filters.brandNo.value;i++){
				document.filters.brand[i].checked=false;
			}
		}
	}else{
		document.filters.brandAll.checked=false;
	}
}

function coloursAll(id){
	if(id==1){	
		if(document.filters.colourAll.checked){
			for(i=0;i<document.filters.colourNo.value;i++){
				document.filters.colour[i].checked=true;
			}
		}else if(!document.filters.colourAll.checked){
			for(i=0;i<document.filters.colourNo.value;i++){
				document.filters.colour[i].checked=false;
			}
		}
	}else{
		document.filters.colourAll.checked=false;
	}
}

function profilesAll(id){
	if(id==1){	
		if(document.filters.profileAll.checked){
			for(i=0;i<document.filters.profileNo.value;i++){
				document.filters.profile[i].checked=true;
			}
		}else if(!document.filters.profileAll.checked){
			for(i=0;i<document.filters.profileNo.value;i++){
				document.filters.profile[i].checked=false;
			}
		}
	}else{
		document.filters.profileAll.checked=false;
	}
}

function fillShoes(){
//	alert('fillShoes');
	brandFilterVal="";
	colourFilterVal="";
	profileFilterVal="";
/*
	for(i=0;i<document.filters.brandNo.value;i++){
		document.filters.brand[0].name;
		if(document.filters.brand[i].checked){
			brandFilterVal+=document.filters.brand[i].value;
			if(i!=(document.filters.brandNo.value-1))brandFilterVal+=",";
		}
	}
	if(document.filters.colourAll.checked)colourFilterVal+="0,";
	for(i=0;i<document.filters.colourNo.value;i++){
		if(document.filters.colour[i].checked){
			colourFilterVal+=document.filters.colour[i].value;
			if(i!=(document.filters.colourNo.value-1))colourFilterVal+=",";
		}
	}
	if(document.filters.profileAll.checked)profileFilterVal+="0,";
	for(i=0;i<document.filters.profileNo.value;i++){
		if(document.filters.profile[i].checked){
			profileFilterVal+=document.filters.profile[i].value;
			if(i!=(document.filters.profileNo.value-1))profileFilterVal+=",";
		}
	}
	pageSize=document.filters.pageSize[document.filters.pageSize.selectedIndex].value;
*/	
	var usersURL="../ajax/searchResults.php?x=0&b=1,5&c=0,1,3,4,5,6,7,8,9,10,11,12,14&p=0,1,2,3,4&n=200&pn=1";
/*
	usersURL+="&b=";
	usersURL+=brandFilterVal
	usersURL+="&c=";
	usersURL+=colourFilterVal
	usersURL+="&p=";
	usersURL+=profileFilterVal
	usersURL+="&n=";
	usersURL+=pageSize;
	usersURL+="&pn=";
	usersURL+=currentPage;
//	alert(usersURL);
*/
	var httpreq = getHTTPObject();
	httpreq.open("GET",usersURL, true);
	httpreq.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");                  
	httpreq.onreadystatechange=function(){
		if(httpreq.readyState==4){
			var usersXML = httpreq.responseXML;
			processXML(usersXML);
		}
	}
	httpreq.send(null);
}

function displayNames(){
	id="matches";
	if (document.getElementById || document.all) {
		var el = document.getElementById? document.getElementById(id): document.all[id];
		if (el && typeof el.innerHTML != "undefined") el.innerHTML = "";
		for(i=0;i<dateArray.length;i++){
			shtml='<div id="m'+i+'" class="match"><div class="border"><img src="images/border'+dateArray[i][0][0]+'.png"></div><div class="name">'+dateArray[i][0][1]+'</div></div>';
			if (el && typeof el.innerHTML != "undefined") el.innerHTML += shtml;
			setBackground('m'+i,colours[dateArray[i][0][0]-1],'url(images/matchLoading.gif)');
		}
	}
	for(i=0;i<dateArray.length;i++){
		setBackground('m'+i,colours[dateArray[i][0][0]-1],'url(thumbs/'+dateArray[i][0][2]+')');
	}
}

function display(num){
	id="matches";
	if (document.getElementById || document.all) {
		var el = document.getElementById? document.getElementById(id): document.all[id];
		if (el && typeof el.innerHTML != "undefined") el.innerHTML = "";
		for(i=0;i<dateArray.length;i++){
			shtml='<div id="m'+i+'" class="match"><div class="border"><img src="images/border'+dateArray[i][0][0]+'.png"></div><div class="info">';
			for(j=0;j<dateArray[i][num].length;j++){
				shtml+='<hr />'+dateArray[i][num][j];
			}
			shtml+='</div></div>';
			if (el && typeof el.innerHTML != "undefined") el.innerHTML += shtml;
			setBackground('m'+i,colours[dateArray[i][0][0]-1],'url(images/matchLoading.gif)');
		}
	}
	for(i=0;i<dateArray.length;i++){
		setBackground('p'+i,colours[dateArray[i][0][0]-1],'url(thumbs/'+dateArray[i][0][2]+')');
	}
}

function setBackground(element,colour,image){
	if (document.getElementById || document.all) {
		var el = document.getElementById? document.getElementById(element): document.all[element];
		if(image)el.style.background=image;
		if(colour)el.style.backgroundColor=colour;
		el.style.backgroundRepeat='no-repeat';
	}
}

function getHTTPObject() {
  var xmlhttp;
  /*@cc_on
  @if (@_jscript_version >= 5)
    try {
      xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
    } catch (e) {
      try {
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
      } catch (E) {
        xmlhttp = false;
      }
    }
  @else
  xmlhttp = false;
  @end @*/
  if (!xmlhttp && typeof XMLHttpRequest != 'undefined') {
    try {
      xmlhttp = new XMLHttpRequest();
    } catch (e) {
      xmlhttp = false;
    }
  }
  return xmlhttp;
}

var ajaxFilmURL = "/flirt/ajax/films.php?";

function autoComplete(sender,ev,filters){
	var suggestionID=0;
	if ((ev.keyCode>=48 && ev.keyCode<=57)||(ev.keyCode>=65&&ev.keyCode<=90)){
		var httpreq = getHTTPObject();
		var parms="str="+sender.value;
		httpreq.open("GET",ajaxFilmURL+parms, true);
		var original_text = sender.value;
		httpreq.onreadystatechange=function(){
			if(httpreq.readyState==4){
				var response = httpreq.responseText;
				suggestionID = response.substring(0, response.indexOf(':'));
				var suggestion = response.substring(response.indexOf(':')+1);
				var txtAuto = document.getElementById(filters+"Auto");
				if((suggestion)&&(txtAuto.value==original_text)){
					if(document.getSelection){
						var initial_len=txtAuto.value.length;
						txtAuto.value += suggestion;
						txtAuto.selectionStart = initial_len;
						txtAuto.selectionEnd = txtAuto.value.length;
					}else if( document.selection ){
						var sel = document.selection.createRange();
						sel.text=suggestion;
						sel.move=("character",-suggestion.length);
						sel.findText(suggestion);
						sel.select();
					}
 				}
			}
		}
		httpreq.send(null);
	}
	if (ev.keyCode==13 && sender.value.length>0){
		addFilter(sender.value,filters,suggestionID);
		sender.value="";
	}
}

function addFilter(value,filters,suggestionID){
	id=filters+"Filters";
	eval('arr='+filters+'Array;');
	arr.push(value);
	if (document.getElementById || document.all) {
		var el = document.getElementById? document.getElementById(id): document.all[id];
		if (el && typeof el.innerHTML != "undefined") el.innerHTML = "";
		for(i=0;i<arr.length;i++){
			shtml=arr[i]+' [<a href="#" onClick="deleteFilter('+i+',\''+filters+'\')">x</a>]<br />';
			if (el && typeof el.innerHTML != "undefined") el.innerHTML += shtml;
		}
	}
}

function deleteFilter(value,filters){
	id=filters+"Filters";
	eval('arr='+filters+'Array;');
	arr.splice(value,1);
	if (document.getElementById || document.all) {
		var el = document.getElementById? document.getElementById(id): document.all[id];
		if (el && typeof el.innerHTML != "undefined") el.innerHTML = "";
		for(i=0;i<arr.length;i++){
			shtml=arr[i]+' [<a href="#" onClick="deleteFilter('+i+',\''+filters+'\')">x</a>]<br />';
			if (el && typeof el.innerHTML != "undefined") el.innerHTML += shtml;
		}
	}
}
