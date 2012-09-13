
		</div>
<!--
The powered by notice MUST be left intact (and visible) in accordance with the license this software is released under.
-->
		<div id="footer">
			<p>
				Copyright &copy; <?php echo HOST; ?> | Powered by: <a href="http://domain-portfolio.secondversion.com/" title="Domain Portfolio Manager">Domain Portfolio Manager v<?php echo $version; ?></a>
				<?php if ($db->is_admin): ?>&nbsp; &mdash; &nbsp;<a href="./admin/admin.php" title="Admin Area">Admin</a><?php endif; ?><br /><br />
				Expiration dates shown using the UTC timezone. Other dates use the admin timezone of <?php echo date('T'); ?>.
			</p>
		</div>
	</div>
</div>

</body>
</html>