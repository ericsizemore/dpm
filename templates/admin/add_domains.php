<?php include('header.php'); ?>
			<div id="content">
				<div id="intro">
					<p>Use the following form to add your domain name. If you would like to add domains in bulk, click <a href="add_domains_bulk.php">here</a>.</p><br />
					<p>Support for IDN domains is currently in BETA.</p><br />
<?php

if ($result != ''):
?>
					<div id="result"><?php echo $result; ?></div>
					<br />
<?php
endif;
?>
					<div id="table">
						<h1>Add Domains</h1>
						<form method="post" action="add_domains.php">
						<table style="text-align: left; width: 650px;" border="0" cellpadding="2" cellspacing="1">
						<tbody>
						<tr>
							<td><label for="domain">Domain:</label></td>
							<td><input type="text" name="domain" id="domain" value="<?php echo $_SESSION['form']['domain']; ?>" /></td>
						</tr>
						<tr>
							<td valign="top"><label for="description">Description:</label></td>
							<td><textarea name="description" id="description" rows="5" cols="40"><?php echo $_SESSION['form']['description']; ?></textarea></td>
						</tr>
						<tr>
							<td><label for="keywords">Keywords:</label> <small>(seperate by commas)</small></td>
							<td><input type="text" name="keywords" id="keywords" value="<?php echo $_SESSION['form']['domain_keywords']; ?>" /></td>
						</tr>
						<tr>
							<td><label for="category">Category:</label></td>
							<td>
							<select name="category[]" id="category" multiple="multiple">
							<?php echo build_select('category'); ?>
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
							<td><input type="text" name="registrar" id="registrar" value="<?php echo $_SESSION['form']['registrar']; ?>" /></td>
						</tr>
						<tr>
							<td><label for="datepicker">Expiry*:</label></td>
							<td><input type="text" name="expiry" id="datepicker" value="<?php echo $_SESSION['form']['expiry']; ?>" /></td>
						</tr>
						<tr>
							<td><label for="price">Price:</label></td>
							<td><input type="text" name="price" id="price" value="<?php echo $_SESSION['form']['price']; ?>" /></td>
						</tr>
						<tr>
							<td><label for="status">Status:</label></td>
							<td>
							<select name="status" id="status">
								<option label="For Sale" value="For Sale" selected="selected">For Sale</option>
								<option label="Not For Sale" value="Not For Sale">Not For Sale</option>
								<option label="Make Offer" value="Make Offer">Make Offer</option>
								<option label="Sold" value="Sold">Sold</option>
							</select>
							</td>
						</tr>
						<tr>
							<td><label for="hidden">Hidden:*</label></td>
							<td>
							<select name="hidden" id="hidden">
								<option label="Yes" value="1">Yes</option>
								<option label="No" value="0" selected="selected">No</option>
							</select>
							</td>
						</tr>
						<tr>
							<td><label for="issite">Developed?</label></td>
							<td>
							<select name="issite" id="issite">
								<option label="Yes" value="1">Yes</option>
								<option label="No" value="0" selected="selected">No</option>
							</select>
							</td>
						</tr>
						<tr>
							<td colspan="2" class="center"><input type="submit" name="submit" value="Add" class="submit" /></td>
						</tr>
						</tbody>
						</table>
						</form>
						<br />
					</div>
					<div id="pages">
						<p>* Hide the domain name from the index? Yes/No</p>
						<p>* Use the following format for expiry: mm/dd/yyyy</p>
						<p>* Price must not contain '.00', MySQL adds it by default.</p>
					</div>
				</div>
			</div>
<?php include('sidebar.php'); ?>
<?php include('footer.php'); ?>