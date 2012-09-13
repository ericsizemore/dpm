<?php include('header.php'); ?>
			<div id="content">
				<div id="intro">
					<p>Use the following form to add a new category.<br /><br /></p>
<?php

if ($result != ''):
?>
					<div id="result"><?php echo $result; ?></div>
					<br />
<?php
endif;
?>
					<div id="table">
						<h1>Add Category</h1>
						<form method="post" action="add_category.php">
						<table style="text-align: left; width: 650px;" border="0" cellpadding="2" cellspacing="1">
						<tbody>
						<tr>
							<td><label for="category">Title:</label></td>
							<td><input type="text" name="category" id="category" value="<?php echo $_SESSION['form']['category']; ?>" /></td>
						</tr>
						<tr>
							<td valign="top"><label for="description">Description:</label></td>
							<td><textarea name="description" id="description" rows="5" cols="40"><?php echo $_SESSION['form']['description']; ?></textarea></td>
						</tr>
						<tr>
							<td><label for="keywords">Keywords:</label> <small>(please seperate by commas)</small></td>
							<td><input type="text" name="keywords" id="keywords" value="<?php echo $_SESSION['form']['cat_keywords']; ?>" /></td>
						</tr>
						<tr>
							<td colspan="2" class="center"><input type="submit" name="submit" value="Add" class="submit" /></td>
						</tr>
						</tbody>
						</table>
						</form>
						<br />
					</div>
					<div id="pages">&nbsp;</div>
				</div>
			</div>
<?php include('sidebar.php'); ?>
<?php include('footer.php'); ?>