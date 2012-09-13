<?php include('header.php'); ?>
			<div id="content">
				<div id="intro">
					<p>
						Details for <strong><?php echo $domain['domain']; ?></strong><br /><br />
<?php
if (!in_array($domain['status'], array('Pending Sale', 'Not For Sale'))):
?>
						<a href="./contact.php?mode=domain&amp;d=<?php echo $domain['domainid']; ?>" title="Inquire about <?php echo $domain['domain']; ?>" rel="nofollow"><img src="./templates/legacy/images/makeoffer.png" alt="Make an offer" border="0" /></a> &nbsp; 
<?php
	if (!empty($domain['price']) AND $domain['price'] != '0.00' AND $config->get('paypal_email') != ''):
?>
						<a href="./paypal.php?action=process&amp;d=<?php echo $domain['domainid']; ?>" title="Buy <?php echo $domain['domain']; ?>" rel="nofollow"><img src="./templates/legacy/images/buynow.png" alt="Buy now" border="0" /></a>
<?php
	endif;
endif;
?>
					</p>
					<br />
					<div id="table">
						<h1>Details</h1>
						<table style="text-align: left; width: 650px;" border="0" cellpadding="2" cellspacing="1">
						<tbody>
						<tr>
							<td><strong>Domain ID:</strong></td>
							<td>#<?php echo $domain['domainid']; ?></td>
						</tr>
						<tr>
							<td><strong>Date Added:</strong></td>
							<td><?php echo $domain['added']; ?></td>
						</tr>
						<tr>
							<td><strong>Domain Lowercase:</strong></td>
							<td><?php echo $domain['lower']; ?></td>
						</tr>
						<tr>
							<td><strong>Domain Uppercase:</strong></td>
							<td><?php echo $domain['upper']; ?></td>
						</tr>
						<tr>
							<td><strong>Domain Extension:</strong></td>
							<td><?php echo $domain['ext']; ?></td>
						</tr>
						<tr>
							<td><strong>Domain Without Extension:</strong></td>
							<td><?php echo $domain['noext']; ?></td>
						</tr>
						<tr>
							<td><strong>Domain Length (no ext.):</strong></td>
							<td><?php echo $domain['chars']; ?></td>
						</tr>
						<tr>
							<td><strong>Website:</strong></td>
							<td><?php if ($domain['issite']): ?><a href="http://www.<?php echo $domain['lower']; ?>" target="_blank"><?php echo $domain['lower']; ?></a><?php else: ?>N/A<?php endif; ?></td>
						</tr>
						<tr>
							<td valign="top"><strong>Description:</strong></td>
							<td><?php echo $domain['description']; ?></td>
						</tr>
						<tr>
							<td valign="top"><strong>Keywords:</strong></td>
							<td><?php echo $domain['keywords']; ?></td>
						</tr>
						<tr>
							<td><strong>Registrar:</strong></td>
							<td><?php echo $domain['registrar']; ?></td>
						</tr>
						<tr>
							<td><strong>Expiration:</strong></td>
							<td><?php echo $domain['expiry']; ?></td>
						</tr>
						<tr>
							<td><strong>Status:</strong></td>
							<td><?php echo $domain['status']; ?></td>
						</tr>
						<tr>
							<td><strong>Price:</strong></td>
							<td><?php echo $domain['price']; ?></td>
						</tr>
						<tr>
							<td><strong>Category:</strong></td>
							<td><?php echo $domain['category']; ?></td>
						</tr>
						</tbody>
						</table>
						<br />
					</div>
					<div id="pages">&nbsp;</div>
				</div>
			</div>
<?php include('sidebar.php'); ?>
<?php include('footer.php'); ?>