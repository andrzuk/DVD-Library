
var FolderId = 0;
var ScanResult = "";

function setFolderId(value)
{
	FolderId = value;
}
	
function getDrives()
{
	var i = 0;
	var myDriveLetter = "";
	var driveLetters = new Array("C", "D", "E", "F", "G", "H");

    var myFileSysObj = new ActiveXObject("Scripting.FileSystemObject");
	
	for (i = 0; i < driveLetters.length; i++)
	{
		if (myFileSysObj.GetDrive(driveLetters[i]).DriveType == 4)
		{
			myDriveLetter = driveLetters[i];
			break;
		}
	}

	return myDriveLetter;
}

function runDriveScan()
{
	var myNaped = getDrives();
	
	if (myNaped == "") return;
	
	myNaped += ":\\";
	
	document.getElementById("scan_result").value = "";

	getSubFolders(myNaped, 0);

	document.getElementById("scan_result").value = ScanResult;	
}

function getSubFolders(currFolder, currId)
{
	var myFileSysObj = new ActiveXObject("Scripting.FileSystemObject");
	var myFolder = myFileSysObj.GetFolder(currFolder);
	var myFolderEnum = new Enumerator(myFolder.SubFolders);

	while (!myFolderEnum.atEnd())
	{
		FolderId++;

		ScanResult += "{D}";
		ScanResult += "{P}" + currId + "{/P}";
		ScanResult += "{N}" + myFolderEnum.item().Name + "{/N}";
		ScanResult += "{C}" + dateFormat(myFolderEnum.item().DateCreated) + "{/C}";
		ScanResult += "{A}" + getFileAttrib(myFolderEnum.item().Attributes) + "{/A}";
		ScanResult += "{/D}";

		getSubFolders(myFolderEnum.item(), FolderId);

		myFolderEnum.moveNext();
	}

	var myFileEnum = new Enumerator(myFolder.Files);  

	while (!myFileEnum.atEnd())
	{					
		ScanResult += "{F}";
		ScanResult += "{P}" + currId + "{/P}";
		ScanResult += "{N}" + myFileEnum.item().Name + "{/N}";
		ScanResult += "{S}" + myFileEnum.item().Size + "{/S}";
		ScanResult += "{T}" + myFileEnum.item().Type + "{/T}";
		ScanResult += "{M}" + dateFormat(myFileEnum.item().DateLastModified) + "{/M}";
		ScanResult += "{A}" + getFileAttrib(myFileEnum.item().Attributes) + "{/A}";
		ScanResult += "{/F}";

		myFileEnum.moveNext();
	}
}

function dateFormat(itemDate) 
{
	currDate = new Date(itemDate);

	year = "" + currDate.getFullYear();
	month = "" + (currDate.getMonth() + 1); if (month.length == 1) { month = "0" + month; }
	day = "" + currDate.getDate(); if (day.length == 1) { day = "0" + day; }
	hour = "" + currDate.getHours(); if (hour.length == 1) { hour = "0" + hour; }
	minute = "" + currDate.getMinutes(); if (minute.length == 1) { minute = "0" + minute; }
	second = "" + currDate.getSeconds(); if (second.length == 1) { second = "0" + second; }

	return year + "." + month + "." + day + " " + hour + ":" + minute + ":" + second;
}

function getFileAttrib(attribute)
{
	var result = "";
	
	if (attribute & 1) result += "R";
	if (attribute & 2) result += "H";
	if (attribute & 4) result += "S";
	if (attribute & 8) result += "V";
	if (attribute & 16) result += "F";
	if (attribute & 32) result += "A";
	if (attribute & 64) result += "L";
	if (attribute & 128) result += "C";
	
	return result;
}

function GetSegmentDiscs(segment)
{
	var node_id = "Segment_" + segment;
	var myNode = document.getElementById(node_id);
	var myChilds = myNode.childNodes;

	if (myChilds.length) 
	{
		while (myNode.firstChild) myNode.removeChild(myNode.firstChild);
		return;
	}

	var newli  = document.createElement("li");
	newli.setAttribute("class", "Lib_Load");
	newli.innerHTML = "Discs list loading...";
	myNode.appendChild(newli);

	if (window.XMLHttpRequest)
	{
		xmlhttp = new XMLHttpRequest();
	}
	else
	{
		xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	}
	xmlhttp.onreadystatechange = function()
	{
		if (xmlhttp.readyState == 4 && xmlhttp.status == 200)
		{
			while (myNode.firstChild) myNode.removeChild(myNode.firstChild);
			
			var results = new Array();
			results = JSON.parse(xmlhttp.responseText);
			
			for (i = 0; i < results.length; i++)
			{
				var newli = document.createElement("li");
				newli.setAttribute("class", "Lib_Disc");
				newli.innerHTML = '<a onclick="GetDiscFolders(' + results[i]['id'] + ');"><b>' + results[i]['disc_name'] + '</b> (folderów: <b>' + results[i]['folders_count'] + '</b>, plików: <b>' + results[i]['files_count'] + '</b>, rozmiar: <b>' + results[i]['total_size'] + '</b>)</a>' + '<ul id="disc_' + results[i]['id'] + '"></ul>';
				myNode.appendChild(newli);
			}
		}
	}
	xmlhttp.open("GET", "lib/ajax/get_discs.php?segment=" + segment, true);
	xmlhttp.send();
}

function GetDiscFolders(id)
{
	var node_id = "disc_" + id;
	var myNode = document.getElementById(node_id);
	var myChilds = myNode.childNodes;

	if (myChilds.length) 
	{
		while (myNode.firstChild) myNode.removeChild(myNode.firstChild);
		return;
	}

	var newli  = document.createElement("li");
	newli.setAttribute("class", "Lib_Load");
	newli.innerHTML = "Folders list loading...";
	myNode.appendChild(newli);

	if (window.XMLHttpRequest)
	{
		xmlhttp = new XMLHttpRequest();
	}
	else
	{
		xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	}
	xmlhttp.onreadystatechange = function()
	{
		if (xmlhttp.readyState == 4 && xmlhttp.status == 200)
		{
			while (myNode.firstChild) myNode.removeChild(myNode.firstChild);
			
			var results = new Array();
			results = JSON.parse(xmlhttp.responseText);
			
			for (i = 0; i < results.length; i++)
			{
				var newli = document.createElement("li");
				newli.setAttribute("class", "Lib_Folder");
				newli.innerHTML = '<a onclick="GetSubFolders(' + results[i]['id'] + ');">' + results[i]['folder_name'] + '</a>' + '<ul id="folder_' + results[i]['id'] + '"></ul>';
				myNode.appendChild(newli);
			}
		}
	}
	xmlhttp.open("GET", "lib/ajax/get_folders.php?id=" + id, true);
	xmlhttp.send();
}

function GetSubFolders(id)
{
	var node_id = "folder_" + id;
	var myNode = document.getElementById(node_id);
	var myChilds = myNode.childNodes;
	
	if (myChilds.length) 
	{
		while (myNode.firstChild) myNode.removeChild(myNode.firstChild);
		return;
	}

	var newli  = document.createElement("li");
	newli.setAttribute("class", "Lib_Load");
	newli.innerHTML = "Folders and files list loading...";
	myNode.appendChild(newli);

	if (window.XMLHttpRequest)
	{
		xmlhttp = new XMLHttpRequest();
	}
	else
	{
		xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	}
	xmlhttp.onreadystatechange = function()
	{
		if (xmlhttp.readyState == 4 && xmlhttp.status == 200)
		{
			while (myNode.firstChild) myNode.removeChild(myNode.firstChild);
			
			var results = new Array();
			results = JSON.parse(xmlhttp.responseText);
			
			for (i = 0; i < results.length; i++)
			{
				var newli = document.createElement("li");
				if (results[i]['type'] == 'folder')
				{
					newli.setAttribute("class", "Lib_Folder");
					newli.innerHTML = '<a onclick="GetSubFolders(' + results[i]['id'] + ');">' + results[i]['folder_name'] + '</a>' + '<ul id="folder_' + results[i]['id'] + '"></ul>';
				}
				if (results[i]['type'] == 'file')
				{
					newli.setAttribute("class", "Lib_File");
					newli.innerHTML = results[i]['folder_name'];
				}
				myNode.appendChild(newli);
			}
		}
	}
	xmlhttp.open("GET", "lib/ajax/get_subfolders.php?id=" + id, true);
	xmlhttp.send();
}

