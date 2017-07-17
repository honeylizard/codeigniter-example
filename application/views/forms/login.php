<div class="messages">
	<?php echo $message; ?>
</div>
<?php echo $form_tag; ?>
    <div class="form-group">
		<label for="<?php echo $email_id; ?>" class="control-label"><?php echo $email_label; ?></label>
		<br/>
		<div class="input-group">
			<span class="input-group-addon">
				<i class="glyphicon glyphicon-envelope" aria-hidden="true"></i>
			</span>
			<?php echo $email_input_tag; ?>
		</div>
	</div>
    <div class="form-group">
		<label for="<?php echo $password_id; ?>" class="control-label"><?php echo $password_label; ?></label>
		<br/>
		<div class="input-group">
			<span class="input-group-addon">
				<i class="glyphicon glyphicon-lock" aria-hidden="true"></i>
			</span>
			<?php echo $password_input_tag; ?>
        </div>
	</div>
	<input type="submit" value="<?php echo $submit_label; ?>" class="btn btn-primary btn-block">
	<br/><br/>
	<a href="<?php echo $forgot_password_url; ?>"><?php echo $forgot_password_label; ?></a>
</form>
