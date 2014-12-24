function mark(obj, mode) {
	if (mode)
		obj.style.backgroundColor = '';
	else
		obj.style.backgroundColor = '#FFF1B8';
}

function checkform(obj) {
	var xc = 0;
	for (x = 0; x < obj.length; x++) {
		if (obj[x].value == '') {
			xc++;
			obj[x].style.backgroundColor = '#EEEEEE';
			obj[x].style.borderColor = '#FF0000';
			if (xc == 1)
				obj[x].focus();
		} else {
			obj[x].style.backgroundColor = '';
			obj[x].style.borderColor = '';
		}
	}
	if (xc > 0) {
		alert("Bitte fehlende Daten eingeben");
		return false;
	}
	return true;
}

function askDel(myurl) {
	if (confirm("Sind Sie sicher?")) {
		if (myurl != '')
			location.href = myurl;
		else
			return true;
	}
	return false;
}

function askSubmit(obj)
{
	if(confirm("Sind sie sicher?"))
	{
		obj.submit();
	}
}

function markbtn(obj, mode) {
	if (mode)
		obj.style.backgroundColor = '';
	else
		obj.style.backgroundColor = '#FFE47D';
}

function markfield(obj, mode) {
	if (mode)
		obj.style.backgroundColor = hover_field_old_color;
	else {
		hover_field_old_color = obj.style.backgroundColor;
		obj.style.backgroundColor = '#FFF1B8';
	}
}

function markSelBoxes(formobj, chkname, stat) {
	for ( var x = 0; x < formobj.elements.length; x++) {
		if (formobj.elements[x].type == 'checkbox'
				&& formobj.elements[x].name == chkname)
			formobj.elements[x].checked = stat.checked;
	}
}

function hilightRow(obj, val)
{
	if (val == 1)
		document.getElementById(obj).style.backgroundColor = '#dddddd';
	else
		document.getElementById(obj).style.backgroundColor = '#ffffff';
}

function hilight(obj, val)
{
	
	if(val == 1)
		obj.setAttribute('bgcolor', '#fff111');
	else
		obj.removeAttribute('bgcolor');
}

function checkDate(date)
{
    $.post("libs/modules/schedule/schedule.ajax.php", 
    	    {exec: "checkDate", deadline: date}, 
    	    function(data) {
    	    	if(data != "")
        	    	alert(data);
	});
}