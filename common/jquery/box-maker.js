/*
 *  Creates new boxes from posts
 */

			var boxMaker = {};

			boxMaker.makeBoxes = function() {

				page++;
				var postsURL="http://andybarefoot.com/api/posts.php?p="+page+"&n="+pageSize;
				var request = new XMLHttpRequest();
				request.open("GET", postsURL, false);
				request.send();
				var xml = request.responseXML;
				var posts = xml.getElementsByTagName("post");
				id="posts";
				var el = document.getElementById? document.getElementById(id): document.all[id];
				var boxes = [],count = pageSize;
				if(posts.length<pageSize){
					document.getElementById("append").innerHTML = "No more posts";
					document.getElementById("append").style.cursor="auto";
				}
				for (var i=0; i < posts.length; i++) {
					// get values
					var post = posts[i];
	    			var apostID = post.getElementsByTagName("postID");
	    			var apostNetwork = post.getElementsByTagName("postNetwork");
					postID=apostID[0].childNodes[0].nodeValue;
					postNetwork=apostNetwork[0].childNodes[0].nodeValue;
			    	var apostDate = post.getElementsByTagName("postDate");
				    var apostString = post.getElementsByTagName("postString");
					postDate=apostDate[0].childNodes[0].nodeValue;
					postString=apostString[0].childNodes[0].nodeValue;
				    if(postNetwork==1){
						bigImg="";
						smallImg="";
					}else if(postNetwork==2){
					    var abigImg = post.getElementsByTagName("bigImg");
					    var asmallImg = post.getElementsByTagName("smallImg");
						bigImg=abigImg[0].childNodes[0].nodeValue;
						smallImg=asmallImg[0].childNodes[0].nodeValue;
					}
					// caption paragraph
					var capPara = document.createElement("p");
//					var capStr = document.createTextNode(postString);
//					capPara.appendChild(capStr);
					capPara.innerHTML=postString;
					// large image anchor
					var largeAnchor = document.createElement("a");
					var largeImageHref = document.createAttribute("href");
					largeImageHref.nodeValue = bigImg;
					largeAnchor.setAttributeNode(largeImageHref);
					var lightboxRel = document.createAttribute("rel");
					lightboxRel.nodeValue = "lightbox";
					largeAnchor.setAttributeNode(lightboxRel);
					var largeImageTitle = document.createAttribute("title");
					largeImageTitle.nodeValue = postString;
					largeAnchor.setAttributeNode(largeImageTitle);
					// photo
					var photo = document.createElement("img");
					var photoSrc = document.createAttribute("src");
					photoSrc.nodeValue = smallImg;
					photo.setAttributeNode(photoSrc);
					// date div
					var dateDiv = document.createElement("div");
					var dateClass = document.createAttribute("class");
					dateClass.nodeValue = "dateText";
					dateDiv.setAttributeNode(dateClass);
					// instagram link
					var insAnchor = document.createElement("a");
					var insHref = document.createAttribute("href");
					insHref.nodeValue = "http://instagram.com/andybarefoot";
					insAnchor.setAttributeNode(insHref);
					var insTarget = document.createAttribute("target");
					insTarget.nodeValue = "_blank";
					insAnchor.setAttributeNode(insTarget);
					// twitter link
					var twiAnchor = document.createElement("a");
					var twiHref = document.createAttribute("href");
					twiHref.nodeValue = "http://twitter.com/andybarefoot";
					var twiTarget = document.createAttribute("target");
					twiTarget.nodeValue = "_blank";
					twiAnchor.setAttributeNode(twiTarget);
					// instagram icon
					var insIcon = document.createElement("img");
					var insIconSrc = document.createAttribute("src");
					insIconSrc.nodeValue = "images/icons/instagram.png";
					insIcon.setAttributeNode(insIconSrc);
					// twitter icon
					var twiIcon = document.createElement("img");
					var twiIconSrc = document.createAttribute("src");
					twiIconSrc.nodeValue = "images/icons/twitter.png";
					twiIcon.setAttributeNode(twiIconSrc);
					// date text
					var dateStr = document.createTextNode(postDate);
					// share text
					var shareStr = document.createTextNode("SHARE: ");
					// share div 
					var shareDiv = document.createElement("div");
					var shareClass = document.createAttribute("class");
					shareClass.nodeValue = "share";
					shareDiv.setAttributeNode(shareClass);
					// share anchor
					var shareAnchor = document.createElement("a");
					var shareHref = document.createAttribute("href");
					shareHref.nodeValue = "https://twitter.com/intent/tweet?url=http%3A%2F%2Fwww.andybarefoot.com/photo.php?postid="+apostID;
					shareAnchor.setAttributeNode(shareHref);
					var shareTarget = document.createAttribute("target");
					shareTarget.nodeValue = "_blank";
					shareAnchor.setAttributeNode(shareTarget);
					var box = document.createElement('div');
				    if(postNetwork==1){
						box.className = 'box twitterBlock';

						twiAnchor.appendChild(twiIcon);
	  					dateDiv.appendChild(twiAnchor);
						dateDiv.appendChild(dateStr);
	
						box.appendChild(capPara);
						box.appendChild(dateDiv);

					}else if(postNetwork==2){
						box.className = 'box instagramBlock';
						
	 					largeAnchor.appendChild(photo);
	  					insAnchor.appendChild(insIcon);
	  					dateDiv.appendChild(insAnchor);
						dateDiv.appendChild(dateStr);
	
						shareAnchor.appendChild(twiIcon);
	  					shareDiv.appendChild(shareStr);
						shareDiv.appendChild(shareAnchor);
						dateDiv.appendChild(shareDiv);
	  					
	  					box.appendChild(largeAnchor);
						box.appendChild(capPara);
						box.appendChild(dateDiv);
					}


					// add box DOM node to array of new elements
				    boxes.push( box );
				}
				return boxes;
			};
