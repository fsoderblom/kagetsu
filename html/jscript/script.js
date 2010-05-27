var xmlHttp;
var url;
var timeoutId=0;
var searchstring = "";

var IE = document.all?true:false
var tempX = 0
var tempY = 0

// If NS -- that is, !IE -- then set up for mouse capture
if (!IE) document.captureEvents(Event.MOUSEMOVE)

// Set-up to use getMouseXY function onMouseMove
document.onmousemove = getMouseXY;


function getMouseXY(e) 
{
  if (IE) { // grab the x-y pos.s if browser is IE
    tempX = event.clientX + document.body.scrollLeft;
    tempY = event.clientY + document.body.scrollTop;
  } else {  // grab the x-y pos.s if browser is NS
    tempX = e.pageX;
    tempY = e.pageY;
  }  
  // catch possible negative values in NS4
  if (tempX < 0){tempX = 0;}
  if (tempY < 0){tempY = 0;}  
  // show the position values in the form named Show
  // in the text fields named MouseX and MouseY

  return true;
}

/**
 * Only firefox and Internet Explorer supports HTTP calls. This function was 
 * ripped from w3schools.com and creates appropiate object depending on browser.
 */
function GetXmlHttpObject(handler)
{ 
	var objXMLHttp=null;

	if (window.XMLHttpRequest)
	{
		objXMLHttp=new XMLHttpRequest();
	}
	else if (window.ActiveXObject)
	{
		objXMLHttp=new ActiveXObject("Microsoft.XMLHTTP");
	}
	
	return objXMLHttp;
}


/**
 * 0 The request is not initialized
 * 1 The request has been set up
 * 2 The request has been sent
 * 3 The request is in process
 * 4 The request is completed
 */
function stateChanged() 
{ 
	if (xmlHttp.readyState==1)
	{
	}
	else if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
	{ 

		var obj = document.getElementById("progressimg");
		if(obj!=null) obj.src='images/icons/progressimg1.gif';

		if(xmlHttp.responseText.indexOf("||") > 0)
		{
//				alert(xmlHttp.responseText);
			var sep = xmlHttp.responseText.indexOf("||");
			var dest = xmlHttp.responseText.substring(0,sep);
			var str = TrimString(xmlHttp.responseText.substring(sep+2));

			if(dest=='getobjects')
			{
				var obj = document.getElementById('id');
				obj.options.length = 0;
				if(obj!=null)
				{
					var tmp = new Array();
					tmp = str.split(';');
					for(i=0; i<tmp.length; i++)
					{
						var dat = new Array();
						dat = tmp[i].split('#');
						id = dat[0];
						ip = dat[1];
						nam = dat[2];
						stat = dat[3];
						
						obj.options[obj.length]	= new Option(ip+" "+nam,id);
						if(stat=="999" || stat=="998") 
						{
							obj.options[i].style.color = "#cc0000";
						}
					}
				}
			}
			
			if(dest=='message')
			{
				var obj = document.getElementById(dest);

				if(obj!=null)
				{
					obj.value = str;
				}
			}

			if(dest=='name')
			{
				var obj = document.getElementById(dest);

				if(obj!=null)
				{
					obj.value = str;
					if(str!=null && str.length>=2)
					{
						if(str.substring(0,2)=="sw")
							autoselectopt('type', 'Switch');
						else if(str.substring(0,1)=="r")
								autoselectopt('type', 'Router');
					}
				}
			}
			
			if(dest=='objinfo')
			{
				var obj = document.getElementById(dest);

				if(obj!=null)
				{
					obj.innerHTML = str;
				}
			}
		
		}

	} 
} 

function testimg(imgobj, divobj)
{
	var objin = document.getElementById(imgobj)
	var objut = document.getElementById(divobj)
	if(objin!=null && objut!=null)
	{
		var legendx = (tempX-objin.offsetLeft);
		var legendy = (tempY-objin.offsetTop);
		objut.style.left = tempX+30+"px";
		objut.style.top = tempY+"px";
		searchstring = legendx+","+legendy;
		
		clearTimeout(timeoutId);
		timeoutId = setTimeout("buildAjaxString('getobjinfo', 'objinfo', searchstring)",200);
	}
}

function autoselectopt(selobj, val)
{
	var obj = document.getElementById(selobj)
	if(obj!=null)
	{
		for (var i=0; i<obj.options.length; i++) 
		{
			if(obj.options[i].text==TrimString(val))
			{
				obj.options[i].selected = true;
				break;
			}
		}
	}
}

/**
 *
 */
function getHost(src)
{
	if(src!=null)
	{
		searchstring = TrimString(src);
		clearTimeout(timeoutId);
		timeoutId = setTimeout("buildAjaxString('gethost', 'name', searchstring)",200);
	}
}

/**
 *
 */
function getobjects(src)
{
	var obj = document.getElementById('vrf')
	if(obj!=null) obj.selectedIndex=0;
	obj = document.getElementById('office')
	if(obj!=null) obj.selectedIndex=0;

	if(src!=null)
	{
		searchstring = TrimString(src);
		clearTimeout(timeoutId);
		timeoutId = setTimeout("buildAjaxString('getobjects', 'getobjects', searchstring)",200);
	}
}

/**
 *
 */
function getobjects_office(src)
{
	var obj = document.getElementById('vrf')
	if(obj!=null) obj.selectedIndex=0;
	obj = document.getElementById('search');
	if(obj!=null) obj.value='';

	if(src!=null)
	{
		searchstring = TrimString(src);
		clearTimeout(timeoutId);
		timeoutId = setTimeout("buildAjaxString('getobjects_office', 'getobjects', searchstring)",200);
	}
}

/**
 *
 */
function getobjects_vrf(src)
{
	var obj = document.getElementById('office')
	if(obj!=null) obj.selectedIndex=0;
	obj = document.getElementById('search');
	if(obj!=null) obj.value='';

	if(src!=null)
	{
		searchstring = TrimString(src);
		clearTimeout(timeoutId);
		timeoutId = setTimeout("buildAjaxString('getobjects_vrf', 'getobjects', searchstring)",200);
	}
}

/**
 * If no one is selected - select them all
 */
function selectAll(src)
{
	var obj = document.getElementById(src)
	if(obj!=null)
	{
		for (var i=0; i<obj.options.length; i++) 
		{
			if(obj.options[i].selected)
			{	
				return;
			}
		}
		for (var i=0; i<obj.options.length; i++) 
		{
			obj.options[i].selected = true;
		}
	}
}

function checkselected(src, src2)
{
	var obj = document.getElementById(src)
	var objbtn = document.getElementById(src2)

	objbtn.disabled = true;
	
	if(obj!=null)
	{
		for (var i=0; i<obj.options.length; i++) 
		{
			if(obj.options[i].selected)
			{	
				objbtn.disabled = false;
				return;
			}
		}
	}
}

/**
 * Creates URL and do the actual AJAX call
 */
function buildAjaxString(cmd, prm1, prm2)
{	
	var obj = document.getElementById("progressimg");
    if(obj!=null) obj.src='images/icons/progressimg2.gif';

	xmlHttp=GetXmlHttpObject();
	if (xmlHttp==null)
	{
		alert ("Browser does not support HTTP Request");
		return;
	}

	url="ajax.php";
	url=url+"?cmd="+cmd;
	url=url+"&prm1="+prm1;
	url=url+"&prm2="+prm2;
	url=url+"&sid="+Math.random();
//alert(url);
	xmlHttp.onreadystatechange=stateChanged;
	xmlHttp.open("GET",url,true);
	xmlHttp.send(null);
}

/**
 *
 */
function okDel(spec)
{
	if(spec!='')
	{
		return confirm('Are you sure that you wan\'t to delete '+spec+'?');
	}
	else
	{
		return confirm('Are you sure that you wan\'t to delete?');
	}
}

/**
 *
 */
function TrimString(sInString) {
  sInString = sInString.replace( /^\s+/g, "" );// strip leading
  return sInString.replace( /\s+$/g, "" );// strip trailing
}

/**
 *
 */
function checkenter()
{
	if(window.event && window.event.keyCode == 13)
	{
		alert("enter");
		return true;
	}
}

