<?php include('header.php'); ?>
			<div id="content">
				<div id="intro">
					<br />
					<h2>Privacy Policy</h2>
					<br />
					<p>
						If you require any more information or have any questions about our privacy policy, 
						please feel free to <a href="./contact.php" title="Contact" rel="nofollow">contact us</a> by email.
					</p>
					<br />
					<p>
						At <?php echo $site; ?>, the privacy of our visitors is of extreme importance to us. 
						This privacy policy document outlines the types of personal information is received and 
						collected by <?php echo $site; ?> and how it is used.
					</p>
					<br />
					<p>
						<b>Log Files</b><br /><br />
						Like many other Web sites, <?php echo $site; ?> makes use of log files. The information 
						inside the log files includes internet protocol ( IP ) addresses, type of browser, 
						Internet Service Provider ( ISP ), date/time stamp, referring/exit pages, and number of 
						clicks to analyze trends, administer the site, track user's movement around the site, and 
						gather demographic information. IP addresses, and other such information are not linked to 
						any information that is personally identifiable.
					</p>
					<br />
					<p>
						<b>Cookies and Web Beacons</b><br /><br />
						<?php echo $site; ?> does use cookies to store information about visitors preferences, 
						record user-specific information on which pages the user access or visit, customize 
						Web page content based on visitors browser type or other information that the visitor 
						sends via their browser.
					</p>
					<br />
					<p>
						<b>Contact Forms</b><br /><br />
						In regards to our contact forms, if you submit a message to us via our contact forms, 
						we do collect your Name, Email Address, and IP Address.
					</p>
					<br />
					<p>
						We do not share this information with anyone other than those who provide support for the 
						internal operations of <?php echo $site; ?>.
					</p>

<?php

$adsense = $config->get('adsense');

if ($adsense['pubid'] AND ($adsense['header']['show'] == true OR $adsense['sidebar']['show'] == true)):
?>
					<br />
					<p>
						<b>DoubleClick DART Cookie</b><br /><br />
						<ul>
							<li>Google, as a third party vendor, uses cookies to serve ads on <?php echo $site; ?></li>
							<li>Google's use of the DART cookie enables it to serve ads to your users based on their visit to <?php echo $site; ?> and other sites on the Internet.</li>
							<li>Users may opt out of the use of the DART cookie by visiting the Google ad and content network privacy policy at the following URL - http://www.google.com/privacy_ads.html</li>
						</ul>
					</p>
					<br />
					<p>
						Some of our advertising partners may use cookies and web beacons on our site. Our 
						advertising partners include: Google Adsense
					</p>
					<br />
					<p>
						These third-party ad servers or ad networks use technology to the advertisements and links 
						that appear on <?php echo $site; ?> send directly to your browsers. They automatically receive 
						your IP address when this occurs. Other technologies ( such as cookies, JavaScript, or Web 
						Beacons ) may also be used by the third-party ad networks to measure the effectiveness of 
						their advertisements and / or to personalize the advertising content that you see.
					</p>
					<br />
					<p>
						<?php echo $site; ?> has no access to or control over these cookies that are used by 
						third-party advertisers.
					</p>
					<br />
					<p>
						You should consult the respective privacy policies of these third-party ad servers for 
						more detailed information on their practices as well as for instructions about how to 
						opt-out of certain practices. <?php echo $site; ?>'s privacy policy does not apply to, and 
						we cannot control the activities of, such other advertisers or web sites.
					</p>
<?php
endif;

?>

					<br />
					<p>
						<b>Controlling Your Privacy</b><br /><br />
						Note that you can change your browser settings to disable cookies if you have privacy 
						concerns. Disabling cookies for all sites is not recommended as it may interfere with 
						your use of some sites. The best option is to disable or enable cookies on a per-site 
						basis. Consult your browser documentation for instructions on how to block cookies and 
						other tracking mechanisms. 
					</p>
					<div id="pages">&nbsp;</div>
				</div>
			</div>
<?php include('sidebar.php'); ?>
<?php include('footer.php'); ?>