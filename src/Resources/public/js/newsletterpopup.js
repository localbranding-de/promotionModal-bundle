	
var idle = true;
var idletime = 119;
var callTime =30;


var modalTrigger;
function triggerModal(id) {
	if(modalTrigger[id])
	{
		MicroModal.close('modal-'+id);
		modalTrigger[id]=false;
	}
	else{
		MicroModal.show('modal-'+id);
		modalTrigger[id]=true;
		
	}
	
	
}
jQuery('document').ready(function(){
	
	

	TimeMe.initialize({
		idleTimeoutInSeconds: idletime
		
	});
	jQuery('.micromodal-slide').each(function(){
		var id = jQuery(this).attr('id').split("-").pop();
		var pages =jQuery(this).attr('pages');
		var start =jQuery(this).attr('start');
		  var stop =jQuery(this).attr('stop');
		  var bestBefore =jQuery(this).attr('bestBefore');
		  var duration =jQuery(this).attr('duration');
		if(pages=="true")
			{
			popNewsletter(id,bestBefore);
			}
		if(idle)
		{

		TimeMe.callAfterTimeElapsedInSeconds(duration, function(){
			popNewsletter(id,bestBefore);
		});
		}


		});
	
});

var scrollprogress = false;
var scrollpercentage = 70;


var setCookie=true;







function myFunction(e)
{
	var text = jQuery(e).find('input[name="email"]').val();
	var elsetext = jQuery(e).find('input[name="else"]').val();
	
	
	alert("You did it!"+text+" "+elsetext);
	jQuery('#newsletterpopup').hide('slow');
	setTimeout(function() {
jQuery('#newsletterpopup').remove();
}, 1000);
	
}




function getCookie(cname) {
  var name = cname + "=";
  var decodedCookie = decodeURIComponent(document.cookie);
  var ca = decodedCookie.split(';');
  for(var i = 0; i <ca.length; i++) {
    var c = ca[i];
    while (c.charAt(0) == ' ') {
      c = c.substring(1);
    }
    if (c.indexOf(name) == 0) {
      return c.substring(name.length, c.length);
    }
  }
  return "";
}

function deleteCookie(id)
{
	document.cookie = "newsletter"+id+"=true; expires=Thu, 01 Jan 1970 00:00:00 GMT;domain=."+location.hostname+";path=/";
	document.cookie = "newsletter"+id+"=true; expires=Thu, 01 Jan 1970 00:00:00 GMT";
}

function popNewsletter(id,bestBefore) 
{
	/*
	var popup="<div class='popup' id='newsletterpopup' style='background-color: #ee7100; width: 300px; height:auto; min-height: 8%; display: block; position: fixed; top:46%; right:0; float: right;display: none;'><form id='newsletter' action='javascript:;' onsubmit='myFunction(this)'><p><input name='email' type='text' placeholder='E-Mail'></input></p><p><input name='else' type='text' placeholder='Noch irgendwas'></input></p><p><button type='submit' >Button</button></p></form></div>";
	if(!getCookie("newsletter")){
		if(setCookie&&!getCookie("newsletter")){document.cookie = "newsletter=true;domain=."+location.hostname+";path=/";}
		if(jQuery("#newsletterpopup").length == 0) {
		
			
			jQuery(popup).insertBefore(".layout_full .container").show("slow");
		
		}
		
		
		//do sth....
	}
	*/
	if(bestBefore==0)
	{
		if(!getCookie("newsletter"+id)){
		if(setCookie&&!getCookie("newsletter"+id)){document.cookie = "newsletter"+id+"=true;domain=."+location.hostname+";path=/";}

		
			
			triggerModal(id);
		
		}
		
		//do sth....
	}
	else
		{
		if(!getCookie("newsletter"+id)){
			if(setCookie&&!getCookie("newsletter"+id)){
				var today= new Date();
				document.cookie = "newsletter"+id+"=true;domain=."+location.hostname+";path=/;expires="+today.setSeconds(today.getSeconds() + bestBefore);
				}

			
				
				triggerModal(id);
			
			}
		}
	
	
}

if(idle)
{
	TimeMe.initialize({
		idleTimeoutInSeconds: idletime
		
	});
TimeMe.callAfterTimeElapsedInSeconds(callTime, function(){
	popNewsletter();
});
}

if(scrollprogress)
{		
		var blogHeight = jQuery(".layout_full .container").outerHeight();
		var docHight =  jQuery(document).outerHeight();
		var blogOffset = jQuery(".layout_full .container").offset().top;
		var navBarHeight = jQuery(".navbar-fixed-top").outerHeight();
		var blogStart = blogOffset - navBarHeight;
		jQuery(document).scroll(function()
		{
			console.log((jQuery(window).scrollTop()-blogOffset+window.outerHeight/2)/blogHeight>=(scrollpercentage/100));
				if(jQuery(window).scrollTop()>=blogStart){
					
				}
				if((jQuery(window).scrollTop()-blogOffset+window.outerHeight/2)/blogHeight>=(scrollpercentage/100)){
					popNewsletter();
				}
		});
}
