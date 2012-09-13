<?php include('header.php'); ?>
			<div id="right">
<?php

if ($searchquery == ''):
?>
				<h2>Domains<?php echo $currentcat; ?></h2>
				<p>You can use the following form to search the domains in our database. Please enter atleast 2 characters for your search term.</p>
				<br />
				<form name="search" action="./search.php" method="post" style="display: inline;">
				<table cellspacing="0">
				<tbody>
				<tr class="odd">
					<td class="one"><label for="query">Query:</label></td>
					<td class="two"><input type="text" name="query" id="query" size="30" maxlength="100" /> <input type="submit" name="submit" value="Submit" /></td>
				</tr>
				</tbody>
				</table>
				</form>
<?php
else:
?>
				<h2>Domains</h2>
				<p>Results of your search are below.</p>
				<p>
					Domains with a <img src="./templates/default/images/site.jpg" alt="site" /> beside them are websites. 
					Click the image to go to the site. You can click on the domain to view more details about the domain.
				</p>
				<br />
				<table cellspacing="0">
				<thead>
				<tr>
					<th class="one"><a href="./search.php?sort=domain<?php echo $searchquery; ?>" title="Sort by Domain">Domain</a></th>
					<th class="two"><a href="./search.php?sort=category<?php echo $searchquery; ?>" title="Sort by Category">Category</a></th>
					<th class="three"><a href="./search.php?sort=registrar<?php echo $searchquery; ?>" title="Sort by Registrar">Registrar</a></th>
					<th class="four"><a href="./search.php?sort=expiry<?php echo $searchquery; ?>" title="Sort by Expiry">Expiry</a></th>
					<th class="five"><a href="./search.php?sort=price<?php echo $searchquery; ?>" title="Sort by Price">Price</a></th>
					<th class="six"><a href="./search.php?sort=status<?php echo $searchquery; ?>" title="Sort by Status">Status</a></th>
					<th class="seven">Contact</th>
				</tr>
				</thead>
				<tbody>
<?php

	if ($error != ''):
?>
				<tr class="even">
					<td colspan="7" style="text-align: center;"><?php echo $error; ?>.</td>
				</tr>
<?php
	elseif (count($domains) == 0):
?>
				<tr class="even">
					<td colspan="7" style="text-align: center;">Nothing matched your query, try different search terms.</td>
				</tr>
<?php
	else:
		foreach ($domains AS $domain):
?>
				<tr class="<?php echo ($domain['class']) ? 'even' : 'odd'; ?>">
					<td class="one" title="<?php echo $domain['description']; ?>"><?php if ($domain['issite']): ?><a href="http://www.<?php echo $domain['domain']; ?>" title="<?php echo $domain['description']; ?>" target="_blank"><img src="./templates/default/images/site.jpg" alt="<?php echo $domain['description']; ?>" /></a> <?php endif; ?> <a href="./details.php?d=<?php echo $domain['domainid']; ?>" title="Details for <?php echo $domain['domain']; ?>"><?php echo $domain['domain']; ?></a></td>
					<td class="two"><?php echo $domain['category']; ?></td>
					<td class="three"><?php echo $domain['registrar']; ?></td>
					<td class="four"><?php echo $domain['expiry']; ?></td>
					<td class="five"><?php echo $domain['price']; ?></td>
					<td class="six"><?php echo $domain['status']; ?></td>
					<td class="seven"><?php if (!in_array($domain['status'], array('Pending Sale', 'Not For Sale'))): ?><a href="./contact.php?mode=domain&amp;d=<?php echo $domain['domainid']; ?>" rel="nofollow"><img src="./templates/default/images/contact.gif" border="0" width="18" height="11" alt="Inquire about <?php echo $domain['domain']; ?>" /></a><?php endif; ?></td>
				</tr>
<?php
		endforeach;
	endif;
?>
				</tbody>
				</table>
<?php
endif;

?>
			</div>
<?php include('sidebar.php'); ?>
<?php include('footer.php'); ?>