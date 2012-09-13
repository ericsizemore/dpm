<?php include('header.php'); ?>
			<div id="content">
				<div id="intro">
<?php

if ($searchquery == ''):
?>
					<p>You can use the following form to search the domains in our database. Please enter atleast 2 characters for your search term.<br /><br /></p>
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
					<p>
						Results of your search are below.<br /><br />
						Domains with a <img src="./templates/legacy/images/site.jpg" alt="site" /> beside them are websites. 
						Click the image to go to the site. You can click on the domain to view more details about the domain.<br /><br />
					</p>
					<div id="table">
						<h1>Domains</h1>
						<table style="text-align: left; width: 650px;" border="0" cellpadding="2" cellspacing="1">
						<thead>
						<tr class="header">
							<th><a href="./search.php?sort=domain<?php echo $searchquery; ?>" title="Sort by Domain">Domain</a></th>
							<th><a href="./search.php?sort=category<?php echo $searchquery; ?>" title="Sort by Category">Category</a></th>
							<th><a href="./search.php?sort=registrar<?php echo $searchquery; ?>" title="Sort by Registrar">Registrar</a></th>
							<th><a href="./search.php?sort=expiry<?php echo $searchquery; ?>" title="Sort by Expiry">Expiry</a></th>
							<th><a href="./search.php?sort=price<?php echo $searchquery; ?>" title="Sort by Price">Price</a></th>
							<th><a href="./search.php?sort=status<?php echo $searchquery; ?>" title="Sort by Status">Status</a></th>
							<th class="center">Contact</th>
						</tr>
						</thead>
						<tbody>
<?php

	if ($error != ''):
?>
						<tr>
							<td colspan="7" class="center"><?php echo $error; ?></td>
						</tr>
<?php
	elseif (count($domains) == 0):
?>
						<tr>
							<td colspan="7" class="center">Nothing matched your query, try different search terms.</td>
						</tr>
<?php
	else:
		foreach ($domains AS $domain):
?>
						<tr class="r<?php echo $domain['class']; ?>">
							<td title="<?php echo $domain['description']; ?>"><?php if ($domain['issite']): ?><a href="http://www.<?php echo $domain['domain']; ?>" title="<?php echo $domain['description']; ?>" target="_blank"><img src="./templates/legacy/images/site.jpg" alt="<?php echo $domain['description']; ?>" /></a> <?php endif; ?> <a href="./details.php?d=<?php echo $domain['domainid']; ?>" title="Details for <?php echo $domain['domain']; ?>"><?php echo $domain['domain']; ?></a>
							</td>
							<td><?php echo $domain['category']; ?></td>
							<td><?php echo $domain['registrar']; ?></td>
							<td><?php echo $domain['expiry']; ?></td>
							<td><?php echo $domain['price']; ?></td>
							<td><?php echo $domain['status']; ?></td>
							<td class="center"><?php if (!in_array($domain['status'], array('Pending Sale', 'Not For Sale'))): ?><a href="./contact.php?mode=domain&amp;d=<?php echo $domain['domainid']; ?>" rel="nofollow"><img src="./templates/legacy/images/contact.gif" border="0" width="18" height="11" alt="Enquire about <?php echo $domain['domain']; ?>" /></a><?php endif; ?></td>
						</tr>
<?php
		endforeach;
	endif;
?>
						</tbody>
						</table>
						<br />
					</div>
<?php
endif;
?>
					<div id="pages"><?php echo ($pagination['link'] ? $pagination['link'] : '&nbsp;'); ?></div>
				</div>
			</div>
<?php include('sidebar.php'); ?>
<?php include('footer.php'); ?>