<?php include('header.php'); ?>
			<div id="content">
				<div id="intro">
<?php

if ($searchquery == ''):
?>
					<p>You can use the following form to search the domains in your database. Please enter atleast 2 characters for your search term.<br /><br /></p>
					<div id="table">
						<h1>Search</h1>
						<form name="search" action="./search.php" method="post" style="display: inline;">
						<table style="text-align: left; width: 650px;" border="0" cellpadding="2" cellspacing="1">
						<tbody>
						<tr>
							<td><label for="query">Query:</label></td>
							<td><input type="text" name="query" id="query" maxlength="100" /></td>
						</tr>
						<tr>
							<td colspan="2" class="center"><input type="submit" name="submit" value="Submit" class="submit" /></td>
						</tr>
						</tbody>
						</table>
						</form>
						<br />
					</div>
<?php
else:
?>
					<p>Results of your search are below.<br /><br /></p>
					<div id="table">
						<h1>Domains</h1>
						<form method="post" name="bulk">
						<table style="text-align: left; width: 650px;" border="0" cellpadding="2" cellspacing="1">
						<thead>
						<tr class="header">
							<th>&nbsp;</th>
							<th><a href="./search.php?sort=domain<?php echo $searchquery; ?>">Domain</a></th>
							<th><a href="./search.php?sort=category<?php echo $searchquery; ?>">Category</a></th>
							<th><a href="./search.php?sort=registrar<?php echo $searchquery; ?>">Registrar</a></th>
							<th><a href="./search.php?sort=expiry<?php echo $searchquery; ?>">Expiry</a></th>
							<th><a href="./search.php?sort=price<?php echo $searchquery; ?>">Price</a></th>
							<th><a href="./search.php?sort=status<?php echo $searchquery; ?>">Status</a></th>
							<th><a href="./search.php?sort=hidden<?php echo $searchquery; ?>">Hidden</a></th>
							<th>&nbsp;</th>
						</tr>
						</thead>
						<tbody>
<?php

	if ($error != ''):
?>
						<tr>
							<td colspan="9" class="center"><?php echo $error; ?></td>
						</tr>
<?php
	elseif (count($domains) == 0):
?>
						<tr>
							<td colspan="9" class="center">Nothing matched your query, try different search terms.</td>
						</tr>
<?php
	else:
?>
						<tr>
							<td colspan="9" class="center" style="font-weight: bold;">
								With selected, <a href="#" onclick="if (confirm('Are you sure you want to delete these domains?')) { document.bulk.action = 'delete.php?mode=domain&bulk=true'; document.bulk.submit(); } return false;">Delete</a> | <a href="#" onclick="alert('Not available yet.'); return false;">Edit</a></td>
						</tr>
<?php
		foreach ($domains AS $domain):
?>
						<tr class="r<?php echo $domain['class']; ?>">
							<td>
								<input type="checkbox" name="domains[]" value="<?php echo $domain['domainid']; ?>" />
							</td>
							<td title="<?php echo $domain['description']; ?>"><?php echo $domain['domain']; ?></a></td>
							<td><?php echo $domain['category']; ?></td>
							<td><?php echo $domain['registrar']; ?></td>
							<td><?php echo $domain['expiry']; ?></td>
							<td><?php echo $domain['price']; ?></td>
							<td><?php echo $domain['status']; ?></td>
							<td><?php echo ($domain['hidden'] == 1) ? 'Yes' : 'No'; ?></td>
							<td><a href="./edit.php?mode=domain&amp;d=<?php echo $domain['domainid']; ?>">Edit</a> / <a href="./delete.php?mode=domain&amp;d=<?php echo $domain['domainid']; ?>" onclick="return confirm('Are you sure you want to delete this domain?')">Delete</a></td>
						</tr>
<?php
		endforeach;
	endif;
?>
						</tbody>
						</table>
						</form>
						<br />
					</div>
<?php
endif;

?>
					<div id="pages"><?php echo ($pagination['link']) ? $pagination['link'] : '&nbsp;'; ?></div>
				</div>
			</div>
<?php include('sidebar.php'); ?>
<?php include('footer.php'); ?>