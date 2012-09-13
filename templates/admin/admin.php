<?php include('header.php'); ?>
			<div id="content">
				<div id="intro">
					<p>Welcome <?php echo sanitize($_SESSION['dpm_admin_name']); ?>. Domains currently in your database are below.<br /><br /></p>
					<div id="table">
						<h1>Domains</h1>
						<form method="post" name="bulk">
						<table style="text-align: left; width: 650px;" border="0" cellpadding="2" cellspacing="1">
						<thead>
						<tr class="header">
							<th>&nbsp;</th>
							<th><a href="./admin.php?sort=domain">Domain</a></th>
							<th><a href="./admin.php?sort=category">Category</a></th>
							<th><a href="./admin.php?sort=registrar">Registrar</a></th>
							<th><a href="./admin.php?sort=expiry">Expiry</a></th>
							<th><a href="./admin.php?sort=price">Price</a></th>
							<th><a href="./admin.php?sort=status">Status</a></th>
							<th><a href="./admin.php?sort=hidden">Hidden</a></th>
							<th>&nbsp;</th>
						</tr>
						</thead>
						<tbody>
<?php

if (count($domains) == 0):
?>
						<tr>
							<td colspan="9" class="center">No domain names are in the database. Click <a href="./add_domains.php">here</a> to add domains.</td>
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
					<div id="pages"><?php echo $pagination['link']; ?></div>
				</div>
			</div>
<?php include('sidebar.php'); ?>
<?php include('footer.php'); ?>