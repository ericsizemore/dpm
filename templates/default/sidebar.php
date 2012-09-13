
			<div id="left">
				<div id="padding">
					<h3>Search Domains</h3>
					<div id="search">
						<form method="post" action="search.php">
							<fieldset>
								<div id="box">
									<input type="text" name="query" class="input1" />
								</div>
								<input type="submit" class="button" name="submit" value="Search!" />
							</fieldset>
						</form>
					</div>
					<h3>Categories</h3>
					<ul class="categories">
						<?php echo build_list('cats'); ?>
					</ul>
					<h3>
						Recently Added &nbsp; 
						<span style="float: right;">
							<a href="rss.php?feed=latest" title="RSS Feed for 'Latest' domains.">
								<img src="./templates/default/images/feed.png" alt="RSS Feed for 'Latest' domains." border="0" />
							</a>
						</span>
					</h3>
					<ul class="categories">
						<?php echo build_list('latest'); ?>
					</ul>
<?php

$adsense = $config->get('adsense');

if ($adsense['pubid'] AND $adsense['sidebar']['show'] == true AND dpm_page() != 'contact')
{
	echo build_adsense('sidebar');
}

unset($adsense);

?>
				</div>
			</div>