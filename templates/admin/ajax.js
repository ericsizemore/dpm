/**
* This file was not part of the original DNP, this is exclusive to DPM.
*/
function init()
{
	if (typeof XMLHttpRequest == 'undefined')
	{
		objects = Array(
			'Microsoft.XMLHTTP',
			'MSXML2.XMLHTTP',
			'MSXML2.XMLHTTP.3.0 ',
			'MSXML2.XMLHTTP.4.0',
			'MSXML2.XMLHTTP.5.0'
		);

		for (i in objects)
		{
			try
			{
				return new ActiveXObject(objects[i]);
			}
			catch (e) {}
		}
	}
	else
	{
		return new XMLHttpRequest();
	}
}

function get(id)
{
	return document.getElementById(id);
}

function update(action, which)
{
	var http = init();

	http.open('GET', 'ajax.php?action=' + action + '&which=' + which, true);

	get('ajaxresult').innerHTML = 'Please wait...';

	http.onreadystatechange = function()
	{
		if (http.readyState == 4)
		{
			switch (action)
			{
				case 'whois':
					if (http.responseText != '')
					{
						get('ajaxresult').innerHTML = '';

						var tmpvalue = http.responseText;
						tmpvalue = tmpvalue.split('|');

						if (tmpvalue[0] != null)
						{
							get('datepicker').value = tmpvalue[0];
						}

						if (tmpvalue[1] != null)
						{
							get('registrar').value = tmpvalue[1];
						}
					}
					else
					{
						get('ajaxresult').innerHTML = 'Could not auto-fetch';
					}
					break;
			}
		}
	}
	http.send(null);
}