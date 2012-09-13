<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title><?php echo $pagetitle; ?> - <?php echo $title; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="Content-Language" content="en" />
<meta name="description" content="<?php echo $description; ?>" />
<meta name="keywords" content="<?php echo $keywords . ($catkeywords ? ", $catkeywords" : ''); ?>" />
<meta name="generator" content="Domain Portfolio Manager v<?php echo $version; ?>" />
<link rel="stylesheet" href="./templates/default/style.css" type="text/css" />
<!--[if IE]><link rel="stylesheet" href="./templates/default/ie_style.css?v=<?php echo $version; ?>" type="text/css" /><![endif]-->
<!--[if IE 6]><link rel="stylesheet" href="./templates/default/ie6_style.css?v=<?php echo $version; ?>" type="text/css" /><![endif]-->
<?php

$path = str_replace(dpm_getenv('DOCUMENT_ROOT'), '', realpath('.')) . '/';
$path = str_replace('//', '/', $path);
$host = HOST;

?>
<link rel="alternate" type="application/rss+xml" title="Latest domains" href="http://<?php echo "{$host}/{$path}"; ?>rss.php?feed=latest" />
<?php

if (defined('IN_CONTACT')):
?>
<script type="text/javascript" language="JavaScript">
<!--
function validate_form(mode)
{
	if (mode == '')
	{
		mode = 'domain';
	}

	var flag = true;
	var error_msg = 'The following errors occurred:\n';
	var sname = document.getElementById('sender_name');
	var semail = document.getElementById('sender_email');
	var sphone = document.getElementById('sender_phone');

	if (mode == 'domain')
	{
		var soffer = document.getElementById('sender_offer');
		var offerregex = new RegExp('^[0-9,]+$', '');
	}
	else
	{
		var subject = document.getElementById('sender_subject');
	}

	var smessage = document.getElementById('sender_message');

	// Check Name
	if (sname.value == '' || sname.value == null || sname.length < 2)
	{
		flag = false;
		error_msg += '\n Please enter your name';
	}

	// Check email
	if (semail.value == '' || semail.value == null)
	{
		flag = false;
		error_msg += '\n Please enter a valid email address';
	}

	// Check phone number
	if (sphone.value == '' || sphone.value == null)
	{
		flag = false;
		error_msg += '\n Please enter a valid phone number';
	}

	// Check offer
	if (mode == 'domain' && soffer.value != '' && soffer.value != null)
	{
		if (offerregex.test(soffer.value) == false)
		{
			flag = false;
			error_msg += '\n Please enter a valid offer price';
		}
	}

	// Check subject if in general mode
	if ((mode == 'general') && (subject.value == '' || subject.value == null))
	{
		flag = false;
		error_msg += '\n Please choose a subject';
	}

	// Check Message
	if (smessage.value == '' || smessage.value == null)
	{
		flag = false;
		error_msg += '\n Please enter a message';
	}

	if (!flag)
	{
		window.alert(error_msg + '\n\n');
	}
	return flag;
}
// -->
</script>
<?php
endif;

?>
</head>

<body>

<div id="centered">
	<div id="header">
		<div class="left">
			<h1><?php echo $title; ?></h1>
			<ul>
				<li<?php if (dpm_page() == 'home'): ?> class="active"<?php endif; ?>><a href="./" title="Home"><span>Home</span></a></li>
				<li<?php if (dpm_page() == 'about'): ?> class="active"<?php endif; ?>><a href="./about.php" title="About"><span>About</span></a></li>
				<li<?php if (dpm_page() == 'contact'): ?> class="active"<?php endif; ?>><a href="./contact.php" title="Contact"><span>Contact</span></a></li>
				<li<?php if (dpm_page() == 'privacy'): ?> class="active"<?php endif; ?>><a href="./privacy.php" title="Privacy Policy"><span>Privacy Policy</span></a></li>
			</ul>
		</div>
	</div>
<?php

$adsense = $config->get('adsense');

if ($adsense['pubid'] AND $adsense['header']['show'] == true AND dpm_page() != 'contact')
{
	echo build_adsense('header');
}

unset($adsense);

?>
	<div id="bar">&nbsp;</div>
	<div id="bg">
		<div id="container">