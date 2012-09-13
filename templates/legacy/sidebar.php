
			<div id="right">
				<h1>Search</h1>
				<form method="post" action="search.php" style="display: inline;">
				<p>
					<input type="text" name="query" /> &nbsp; 
					<input class="submit" type="submit" name="submit" value="Go!" />
				</p>
				</form>
				<h1>Categories</h1>
				<div class="links">
					<?php echo build_list('cats'); ?>
				</div>
				<h1>
					Recently Added &nbsp; 
					<span style="float: right;">
						<a href="rss.php?feed=latest" title="RSS Feed for 'Latest' domains.">
							<img src="./templates/default/images/feed.png" alt="RSS Feed for 'Latest' domains." border="0" />
						</a>
					</span>
				</h1>
				<div class="links">
					<?php echo build_list('latest'); ?>
				</div>
<?php

$adsense = $config->get('adsense');

if ($adsense['pubid'] AND $adsense['header']['show'] == true AND dpm_page() != 'contact')
{
	echo build_adsense('sidebar');
}

unset($adsense);

?>
			</div>