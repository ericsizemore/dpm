<?php include('header.php'); ?>
			<div id="content">
				<div id="intro">
					<p>Below you'll find a listing of your current categories and the amount of domains they contain.<br /><br /></p>
					<div id="table">
						<h1>Categories</h1>
						<form method="post" name="bulk">
						<table style="text-align: left; width: 650px;" border="0" cellpadding="2" cellspacing="1">
						<thead>
						<tr class="header">
							<th>&nbsp;</th>
							<th>Category ID</th>
							<th>Category</th>
							<th>Domains</th>
							<th>Options</th>
						</tr>
						</thead>
						<tbody>
<?php

if ($numcats == 0):
?>
						<tr>
							<td colspan="5" class="center">No categories are in the database. Click <a href="./add_category.php">here</a> to add categories.</td>
						</tr>
<?php
else:
?>
						<tr>
							<td colspan="5" class="center" style="font-weight: bold;">
								With selected, <a href="#" onclick="if (confirm('Are you sure you want to delete these domains?')) { document.bulk.action = 'delete.php?mode=domain&bulk=true'; document.bulk.submit(); } return false;">Delete</a> | <a href="#" onclick="alert('Not available yet.'); return false;">Edit</a></td>
						</tr>
<?php
	foreach ($cats AS $cat):
?>
						<tr class="r<?php echo $cat['class']; ?>">
							<td><input type="checkbox" name="cats[]" value="<?php echo $cat['catid']; ?>" /></td>
							<td><?php echo $cat['catid']; ?></td>
							<td><?php echo $cat['title']; ?></td>
							<td><?php echo $cat['numdomains']; ?></td>
							<td><a href="./edit.php?mode=category&amp;cat=<?php echo $cat['catid']; ?>">Edit</a> / <a href="./delete.php?mode=category&amp;cat=<?php echo $cat['catid']; ?>" onclick="return confirm('Are you sure you want to delete this category?');">Delete</a></td>
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