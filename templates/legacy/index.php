<?php include('header.php'); ?>
			<div id="content">
				<div id="intro">
					<p>
						Thanks for visiting our domain portfolio. To contact us about a specific domain you would like to buy, or would like more information on, please click the envelope icon next to the domain details. This will allow you to send an inquiry about the domain.<br /><br />
						Domains with a <img src="./templates/legacy/images/site.jpg" alt="site" /> beside them are websites, you can click the image to go to the site. Each domain is linked to it's own detail page if you'd like to view more information.<br /><br />
					</p>
					<div id="table">
						<h1>Domains<?php if ($currentcat): echo $currentcat; ?> &nbsp; &nbsp; <a href="rss.php?feed=category&amp;catid=<?php echo $catid; ?>" title="RSS Feed for <?php echo $currentcat; ?> domains."><img src="./templates/default/images/feed.png" alt="RSS Feed for <?php echo $currentcat; ?> domains." border="0" style="vertical-align: middle;" /></a><?php endif; ?></h1>
						<table style="text-align: left; width: 650px;" border="0" cellpadding="2" cellspacing="1">
						<thead>
						<tr class="header">
							<th><a href="./index.php?sort=domain<?php echo $catidsort; ?>" title="Sort by Domain">Domain</a></th>
							<th><a href="./index.php?sort=category<?php echo $catidsort; ?>" title="Sort by Category">Category</a></th>
							<th><a href="./index.php?sort=registrar<?php echo $catidsort; ?>" title="Sort by Registrar">Registrar</a></th>
							<th><a href="./index.php?sort=expiry<?php echo $catidsort; ?>" title="Sort by Expiry">Expiry</a></th>
							<th><a href="./index.php?sort=price<?php echo $catidsort; ?>" title="Sort by Price">Price</a></th>
							<th><a href="./index.php?sort=status<?php echo $catidsort; ?>" title="Sort by Status">Status</a></th>
							<th class="center">Contact</th>
						</tr>
						</thead>
						<tbody>
<?php

if (count($domains) == 0):
?>
						<tr>
							<td colspan="7" class="center">No domains <?php if (!is_null($catid)): ?>in this category<?php else: ?>in the database<?php endif; ?>.</td>
						</tr>
<?php
else:
	foreach ($domains AS $domain):
?>
						<tr class="r<?php echo $domain['class']; ?>">
							<td title="<?php echo $domain['description']; ?>"><?php if ($domain['issite']): ?><a href="http://www.<?php echo $domain['domain']; ?>" title="<?php echo $domain['description']; ?>" target="_blank"><img src="./templates/legacy/images/site.jpg" alt="<?php echo $domain['description']; ?>" /></a> <?php endif; ?> <a href="./details.php?d=<?php echo $domain['domainid']; ?>" title="Details for <?php echo $domain['domain']; ?>"><?php echo $domain['domain']; ?></a></td>
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
					<div id="pages"><?php echo $pagination['link']; ?></div>
				</div>
			</div>
<?php include('sidebar.php'); ?>
<?php include('footer.php'); ?>