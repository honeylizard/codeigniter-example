<h1>Hi <?php echo $first_name; ?>,</h1>

<p>
	Reset your password, and we'll get you on your way.
</p>
<p>
	To change your <?php echo $application_name; ?> password,
    click <a href="<?php echo $reset_url; ?>">here</a> or paste the following link into your browser:
</p>
<p>
	<a href="<?php echo $reset_url; ?>">
		<?php echo $reset_url; ?>
	</a>
</p>
<p>
	This link will expire in 24 hours, so be sure to use it right away.
</p>
<p>
	Thank you for using <?php echo $application_name; ?>
	<br/>
	The <?php echo $application_name; ?> Team
</p>
