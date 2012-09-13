<?php include('header.php'); ?>
			<div id="right">
				<h2>Domains<?php echo $currentcat; ?></h2>
				<p>Thanks for visiting our domain portfolio. To contact us about a specific domain you would like to buy, or would like more information on, please click the envelope icon next to the domain details. This will allow you to send an inquiry email about the domain.</p>
				<p>Domains with a <img src="./templates/legacy/images/site.jpg" alt="site" /> beside them are websites, you can click the image to go to the site. Each domain is linked to it's own detail page if you'd like to view more information.</p>
				<br />
				<table cellspacing="0">
				<thead>
				<tr>
					<th class="one"><a href="./index.php?sort=domain<?php echo $catidsort; ?>" title="Sort by Domain">Domain</a></th>
					<th class="two"><a href="./index.php?sort=category<?php echo $catidsort; ?>" title="Sort by Category">Category</a></th>
					<th class="three"><a href="./index.php?sort=registrar<?php echo $catidsort; ?>" title="Sort by Registrar">Registrar</a></th>
					<th class="four"><a href="./index.php?sort=expiry<?php echo $catidsort; ?>" title="Sort by Expiry">Expiry</a></th>
					<th class="five"><a href="./index.php?sort=price<?php echo $catidsort; ?>" title="Sort by Price">Price</a></th>
					<th class="six"><a href="./index.php?sort=status<?php echo $catidsort; ?>" title="Sort by Status">Status</a></th>
					<th class="seven">Contact</th>
				</tr>
				</thead>
				<tbody>
<?php

if (count($domains) == 0):
?>
				<tr class="even">
					<td colspan="7" style="text-align: center;">No domains <?php if (!is_null($catid)): ?>in this category<?php else: ?>in the database<?php endif; ?>.</td>
				</tr>
<?php
else:
	foreach ($domains AS $domain):
?>
				<tr class="<?php echo ($domain['class']) ? 'even' : 'odd'; ?>">
					<td class="one" title="<?php echo $domain['description']; ?>"><a href="./details.php?d=<?php echo $domain['domainid']; ?>" title="Details for <?php echo $domain['domain']; ?>"><?php echo $domain['domain']; ?></a> <?php if ($domain['issite']): ?><a href="http://www.<?php echo $domain['domain']; ?>" title="<?php echo $domain['description']; ?>" target="_blank"><img src="./templates/default/images/site.jpg" alt="<?php echo $domain['description']; ?>" /></a> <?php endif; ?></td>
					<td class="two"><?php echo $domain['category']; ?></td>
					<td class="three"><?php echo $domain['registrar']; ?></td>
					<td class="four"><?php echo $domain['expiry']; ?></td>
					<td class="five"><?php echo $domain['price']; ?></td>
					<td class="six"><?php echo $domain['status']; ?></td>
					<td class="seven"><?php if (!in_array($domain['status'], array('Pending Sale', 'Not For Sale'))): ?><a href="./contact.php?mode=domain&amp;d=<?php echo $domain['domainid']; ?>" rel="nofollow"><img src="./templates/default/images/contact.gif" border="0" width="18" height="11" alt="Enquire about <?php echo $domain['domain']; ?>" /></a><?php endif; ?></td>
				</tr>
<?php
	endforeach;
endif;
?>
				</tbody>
				</table>
			</div>
<?php include('sidebar.php'); ?>
<?php include('footer.php'); ?>