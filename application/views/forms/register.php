<div class="messages">
    <?php echo $message; ?>
</div>
<?php echo $form_tag; ?>
	<div class="form-group <?php echo $email_class; ?>">
		<label for="<?php echo $email_id; ?>" class="control-label"><?php echo $email_label; ?></label>
		<br/>
		<div class="input-group">
			<span class="input-group-addon">
				<i class="glyphicon glyphicon-envelope" aria-hidden="true"></i>
			</span>
            <?php echo $email_input_tag; ?>
		</div>
        <?php echo $email_error; ?>
        <span class="help-block"><?php echo $email_help; ?></span>
	</div>
	<div class="form-group <?php echo $password_class; ?>">
		<label for="<?php echo $password_id; ?>" class="control-label"><?php echo $password_label; ?></label>
		<br/>
		<div class="input-group">
			<span class="input-group-addon">
				<i class="glyphicon glyphicon-lock" aria-hidden="true"></i>
			</span>
			<?php echo $password_input_tag; ?>
		</div>
		<?php echo $password_error; ?>
        <span class="help-block"><?php echo $password_help; ?></span>
	</div>
	<div class="form-group <?php echo $confirm_password_class; ?>">
		<label for="<?php echo $confirm_password_id; ?>" class="control-label"><?php echo $confirm_password_label; ?></label>
		<br/>
		<div class="input-group">
			<span class="input-group-addon">
				<i class="glyphicon glyphicon-lock" aria-hidden="true"></i>
			</span>
			<?php echo $confirm_password_input_tag; ?>
		</div>
        <?php echo $confirm_password_error; ?>
        <span class="help-block"><?php echo $confirm_password_help; ?></span>
	</div>
	<input type="submit" value="<?php echo $submit_label; ?>" class="btn btn-primary btn-block">
</form>
