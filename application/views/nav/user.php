<ul class="nav navbar-nav navbar-right">
	<li>
		<a href="<?php echo $profile_url; ?>">
			<span class="glyphicon glyphicon-user"></span>
			<?php echo $profile_label; ?>
		</a>
	</li>
	<li>
		<a href="<?php echo $settings_url; ?>">
			<span class="glyphicon glyphicon-cog"></span>
			<?php echo $settings_label; ?>
		</a>
	</li>
	<li>
		<a href="<?php echo $logout_url; ?>">
			<span class="glyphicon glyphicon-log-out"></span>
			<?php echo $logout_label; ?>
		</a>
	</li>
</ul>
<p class="navbar-text"><?php echo $name; ?></p>
