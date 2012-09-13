<?php include('header.php'); ?>
			<div id="right">
				<h2>Details</h2>
				<p>Details for domain name: <strong><?php echo $domain['domain']; ?></strong></p>
<?php
if (!in_array($domain['status'], array('Pending Sale', 'Not For Sale'))):
?>
				<p>
					<a href="./contact.php?mode=domain&amp;d=<?php echo $domain['domainid']; ?>" title="Inquire about <?php echo $domain['domain']; ?>" rel="nofollow"><img src="./templates/legacy/images/makeoffer.png" alt="Make an offer" border="0" /></a> &nbsp; 
<?php
	if (!empty($domain['price']) AND $domain['price'] != '0.00' AND $config->get('paypal_email') != ''):
?>
					<a href="./paypal.php?action=process&amp;d=<?php echo $domain['domainid']; ?>" title="Buy <?php echo $domain['domain']; ?>" rel="nofollow"><img src="./templates/legacy/images/buynow.png" alt="Buy now" border="0" /></a>
<?php
	endif;
?>
				</p>
<?php
endif;
?>
				<br />
				<table cellspacing="0">
				<tbody>
				<tr class="odd">
					<td class="one"><strong>Domain ID:</strong></td>
					<td class="two">#<?php echo $domain['domainid']; ?></td>
				</tr>
				<tr class="even">
					<td class="one"><strong>Date Added:</strong></td>
					<td class="two"><?php echo $domain['added']; ?></td>
				</tr>
				<tr class="odd">
					<td class="one"><strong>Domain Lowercase:</strong></td>
					<td class="two"><?php echo $domain['lower']; ?></td>
				</tr>
				<tr class="even">
					<td class="one"><strong>Domain Uppercase:</strong></td>
					<td class="two"><?php echo $domain['upper']; ?></td>
				</tr>
				<tr class="odd">
					<td class="one"><strong>Domain Extension:</strong></td>
					<td class="two"><?php echo $domain['ext']; ?></td>
				</tr>
				<tr class="even">
					<td class="one"><strong>Domain Without Extension:</strong></td>
					<td class="two"><?php echo $domain['noext']; ?></td>
				</tr>
				<tr class="odd">
					<td class="one"><strong>Domain Length (no ext.):</strong></td>
					<td class="two"><?php echo $domain['chars']; ?></td>
				</tr>
				<tr class="even">
					<td class="one"><strong>Website:</strong></td>
					<td class="two"><?php if ($domain['issite']): ?><a href="http://www.<?php echo $domain['lower']; ?>" target="_blank"><?php echo $domain['lower']; ?></a><?php else: ?>N/A<?php endif; ?></td>
				</tr>
				<tr class="odd">
					<td valign="top" class="one"><strong>Description:</strong></td>
					<td class="two"><?php echo $domain['description']; ?></td>
				</tr>
				<tr class="even">
					<td valign="top" class="one"><strong>Keywords:</strong></td>
					<td class="two"><?php echo $domain['keywords']; ?></td>
				</tr>
				<tr class="odd">
					<td class="one"><strong>Registrar:</strong></td>
					<td class="two"><?php echo $domain['registrar']; ?></td>
				</tr>
				<tr class="even">
					<td class="one"><strong>Expiration:</strong></td>
					<td class="two"><?php echo $domain['expiry']; ?></td>
				</tr>
				<tr class="odd">
					<td class="one"><strong>Status:</strong></td>
					<td class="two"><?php echo $domain['status']; ?></td>
				</tr>
				<tr class="even">
					<td class="one"><strong>Price:</strong></td>
					<td class="two"><?php echo $domain['price']; ?></td>
				</tr>
				<tr class="odd">
					<td class="one"><strong>Category:</strong></td>
					<td class="two"><?php echo $domain['category']; ?></td>
				</tr>
				</tbody>
				</table>
			</div>
<?php include('sidebar.php'); ?>
<?php include('footer.php'); ?>