
			<div id="right">
<?php

if (!$db->is_admin):
?>
				<h1>Information</h1>
				<div class="links">
					<p>After logging in, you'll be able to control every aspect of Domain Portfolio Manager.</p>
				</div>
<?php
else:
?>
				<h1>Search</h1>
				<form method="post" action="search.php" style="display: inline;">
				<p>
					<input type="text" name="query" /> 
					<input class="submit" type="submit" name="submit" value="Go!" />
				</p>
				</form>
				<h1>Navigation</h1>
				<div class="links">
					<a href="../index.php" title="Main Home">Main Home</a>
					<a href="./admin.php" title="Admin Home">Admin Home</a>
					<a href="./add_domains.php" title="Add Domains">Add Domains</a>
					<a href="./add_domains_bulk.php" title="Add Domains Bulk">Add Domains (bulk)</a>
					<a href="./add_category.php" title="Add Categories">Add Categories</a>
					<a href="./categories.php" title="Current Categories">Current Categories</a>
					<a href="./database.php" title="Database Info">Database Info</a>
					<a href="./paypal.php" title="Paypal Transactions Log">Paypal Log</a>
					<a href="./site_config.php" title="Site Configuration">Site Config</a>
					<a href="./logout.php" onclick="return confirm('Are you sure you want to logout?');" title="Logout">Logout</a>
				</div>
<?php
endif;
?>
			</div>