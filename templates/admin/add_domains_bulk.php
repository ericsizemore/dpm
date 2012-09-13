<?php include('header.php'); ?>
			<div id="content">
				<div id="intro">
					<p>Use the following form to add domains in bulk.</p><br />
					<p>Support for IDN domains is currently NOT supported for bulk additions, you must add them <a href="add_domains.php">one at a time</a>.</p><br />
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

						<div id="tabs">
							<ul>
								<li><a href="#tabs-1">New</a></li>
								<li><a href="#tabs-2">Legacy</a></li>
							</ul>
							<div id="tabs-1">
								<form method="post" action="add_domains_bulk.php">
								<table style="text-align: left; width: 600px;" border="0" cellpadding="2" cellspacing="1">
								<caption>The registrar, expiry, status, hidden, and developed values you choose will be used for ALL domains you enter.<br />You must enter ONLY the domain name in the below textbox. One per line.</caption>
								<tbody>
								<tr>
									<td colspan="2" class="center"><textarea name="domains" rows="5" cols="80"></textarea></td>
								</tr>
								<tr>
									<td><label for="registrar">Registrar:</label></td>
									<td><input type="text" name="registrar" id="registrar" /></td>
								</tr>
								<tr>
									<td><label for="datepicker">Expiry*:</label></td>
									<td>
										<input type="text" name="expiry" id="datepicker" /> 
									</td>
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
									<td colspan="2" class="center">
										<input type="hidden" name="legacy" value="0" />
										<input type="submit" name="submit" value="Add" class="submit" />
									</td>
								</tr>
								</tbody>
								</table>
								</form>
							</div>
							<div id="tabs-2">
								<p>Valid options for "Status" are: For Sale, Not For Sale, Make Offer or Sold</p><br />

								<form method="post" action="add_domains_bulk.php">
								<table style="text-align: left; width: 600px;" border="0" cellpadding="2" cellspacing="1">
								<caption>You must enter domains in the format of: <br /><code>Domain,Registrar,Expiry,Price,Status</code>.<br />Seperate each with a new line (by pressing enter).</caption>
								<tbody>
								<tr>
									<td colspan="2" class="center"><textarea name="domains" rows="5" cols="80"></textarea></td>
								</tr>
								<tr>
									<td colspan="2" class="center">
										<input type="hidden" name="legacy" value="1" />
										<input type="submit" name="submit" value="Add" class="submit" />
									</td>
								</tr>
								</tbody>
								</table>
								</form>
							</div>
						</div>
						<br />
					</div>
					<div id="pages">
						<p>* Price must not contain '.00', MySQL adds this by default.</p>
						<p>* Expiration date must be in the format of: mm/dd/yyyy</p>
					</div>
				</div>
			</div>
<?php include('sidebar.php'); ?>
<?php include('footer.php'); ?>