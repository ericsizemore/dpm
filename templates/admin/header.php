<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title><?php echo $pagetitle; ?> - Admin - <?php echo $title; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="Content-Language" content="en" />
<meta name="generator" content="Domain Portfolio Manager v<?php echo $version; ?>" />
<link rel="stylesheet" href="../templates/legacy/style.css?v=<?php echo $version; ?>" type="text/css" />
<?php

if (defined('CALENDAR') OR defined('BULKADD')):
?>
<link type="text/css" href="../templates/admin/jquery-ui/themes/cupertino/jquery.ui.all.css?v=<?php echo $version; ?>" rel="stylesheet" />
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
<script type="text/javascript" src="../templates/admin/jquery-ui/ui/jquery.ui.core.js?v=<?php echo $version; ?>"></script>
<script type="text/javascript" src="../templates/admin/jquery-ui/ui/jquery.ui.widget.js?v=<?php echo $version; ?>"></script>
<?php
endif;

if (defined('CALENDAR')):
?>
<script type="text/javascript" src="../templates/admin/jquery-ui/ui/jquery.ui.datepicker.js?v=<?php echo $version; ?>"></script>
<script type="text/javascript">
$(function()
{
	$("#datepicker").datepicker({
		showOn: 'button', 
		buttonImage: '../templates/admin/jquery-ui/themes/cupertino/images/calendar.gif', 
		buttonImageOnly: true,
		minDate: '0'
	});
});
</script>
<?php
endif;

if (defined('BULKADD')):
?>
<script type="text/javascript" src="../templates/admin/jquery-ui/ui/jquery.ui.tabs.js?v=<?php echo $version; ?>"></script>
<script type="text/javascript">
$(function()
{
	$("#tabs").tabs();
});
</script>
<?php
endif;
?>
<script type="text/javascript" src="../templates/admin/ajax.js?v=<?php echo $version; ?>"></script>
</head>

<body>

<div id="hold">
	<div id="border1">
		<div id="container">
			<div id="banner"><?php echo $title; ?></div>
			<div id="search">
				<?php if (dpm_page() == 'home' OR dpm_page() == 'index'): ?>&nbsp;<?php else: ?><strong>Your Domain Portfolio Manager Version:</strong> <?php echo $version; ?> | <strong>Latest version:</strong> <a href="#" onclick="window.open('./admin.php?version_check=1', null, 'height=100,width=100'); return false;">Check</a>&nbsp;<?php endif; ?>
			</div>