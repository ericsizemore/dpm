<?php include('header.php'); ?>
			<div id="content">
				<div id="intro">
<?php

if ($mode == 'category'):
?>
					<p>Use the following form to edit the details about this category (<?php echo $catinfo['title']; ?>}.<br /><br /></p>
<?php

	if ($result != ''):
?>
					<div id="result"><?php echo $result; ?></div>
					<br />
<?php
	endif;
?>
					<div id="table">
						<h1>Edit Category</h1>
						<form method="post" action="edit.php?mode=category&amp;cat=<?php echo $catid; ?>">
						<table style="text-align: left; width: 650px;" border="0" cellpadding="2" cellspacing="1">
						<tbody>
						<tr>
							<td><label for="category">Title:</label></td>
							<td><input type="text" name="category" id="category" value="<?php echo $catinfo['title']; ?>" /></td>
						</tr>
						<tr>
							<td valign="top"><label for="description">Description:</label></td>
							<td><textarea name="description" id="description" rows="5" cols="40"><?php echo $catinfo['description']; ?></textarea></td>
						</tr>
						<tr>
							<td><label for="keywords">Keywords:</label> <small>(seperate by commas)</small></td>
							<td><input type="text" name="keywords" id="keywords" value="<?php echo $catinfo['keywords']; ?>" /></td>
						</tr>
						<tr>
							<td colspan="2" class="center"><input type="submit" name="submit" value="Update" class="submit" /></td>
						</tr>
						</tbody>
						</table>
						</form>
						<br />
					</div>
					<div id="pages">&nbsp;</div>
<?php
else:
?>
					<p>Use the following form to edit any details about this domain (<?php echo $domaininfo['domain']; ?>).<br /><br /></p>
<?php

	if ($result != ''):
?>
					<div id="result"><?php echo $result; ?></div>
					<br />
<?php
	endif;
?>
					<div id="table">
						<h1>Edit Domain</h1>
						<form method="post" action="edit.php?mode=domain&amp;d=<?php echo $domainid; ?>">
						<table style="text-align: left; width: 650px;" border="0" cellpadding="2" cellspacing="1">
						<tbody>
						<tr>
							<td><label for="domain">Domain:</label></td>
							<td><input type="text" name="domain" id="domain" value="<?php echo $domaininfo['domain']; ?>" /></td>
						</tr>
						<tr>
							<td valign="top"><label for="description">Description:</label></td>
							<td><textarea name="description" id="description" rows="5" cols="40"><?php echo $domaininfo['description']; ?></textarea></td>
						</tr>
						<tr>
							<td><label for="keywords">Keywords:</label> <small>(seperate by commas)</small></td>
							<td><input type="text" name="keywords" id="keywords" value="<?php echo $domaininfo['keywords']; ?>" /></td>
						</tr>
						<tr>
							<td><label for="category">Category:</label></td>
							<td>
							<select name="category[]" id="category" multiple="multiple">
								<?php echo $cat_select; ?>
							</select>
							</td>
						</tr>
						<tr>
							<td colspan="2" align="center">
								 <button onclick="update('whois', get('domain').value); return false;">Fetch Expiry &amp; Registrar</button> &nbsp; 
								 <div id="ajaxresult"></div>
							</td>
						</tr>
						<tr>
							<td><label for="registrar">Registrar:</label></td>
							<td><input type="text" name="registrar" id="registrar" value="<?php echo $domaininfo['registrar']; ?>" /></td>
						</tr>
						<tr>
							<td><label for="datepicker">Expiry:</label></td>
							<td>
								<input type="text" name="expiry" id="datepicker" value="<?php echo date('m/d/Y', $domaininfo['expiry']); ?>" /> &nbsp;
							</td>
						</tr>
						<tr>
							<td><label for="price">Price:</label></td>
							<td><input type="text" name="price" id="price" value="<?php echo $domaininfo['price']; ?>" /></td>
						</tr>
						<tr>
							<td><label for="status">Status:</label></td>
							<td>
							<select name="status" id="status">
								<?php echo $status_select; ?>
							</select>
							</td>
						</tr>
						<tr>
							<td><label for="hidden">Hidden:</label></td>
							<td>
							<select name="hidden" id="hidden">
								<?php echo $hide_select; ?>
							</select>
							</td>
						</tr>
						<tr>
							<td><label for="issite">Developed:</label></td>
							<td>
							<select name="issite" id="issite">
								<?php echo $issite_select; ?>
							</select>
							</td>
						</tr>
						<tr>
							<td colspan="2" class="center"><input type="submit" name="submit" value="Update" class="submit" /></td>
						</tr>
						</tbody>
						</table>
						</form>
						<br />
					</div>
					<div id="pages">
						<p>* Hide the domain name from the index? Yes/No.</p>
						<p>* Use the following format for expiry: mm/dd/yyyy</p>
						<p>* Price must not contain '.00', MySQL adds it by default.</p>
					</div>
<?php
endif;
?>
				</div>
			</div>
<?php include('sidebar.php'); ?>
<?php include('footer.php'); ?>