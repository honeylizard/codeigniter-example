<div class="messages">
	<?php echo $message; ?>
</div>
<?php echo $form_tag; ?>
	<fieldset>
		<legend><?php echo $account_legend; ?></legend>
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
	</fieldset>
	<fieldset>
		<legend><?php echo $personal_legend; ?></legend>
		<div class="form-group <?php echo $first_name_class; ?>">
			<label for="<?php echo $first_name_id; ?>" class="control-label"><?php echo $first_name_label; ?></label>
			<br/>
			<?php echo $first_name_input_tag; ?>
			<?php echo $first_name_error; ?>
			<span class="help-block"><?php echo $first_name_help; ?></span>
		</div>

		<div class="form-group <?php echo $last_name_class; ?>">
			<label for="<?php echo $last_name_id; ?>" class="control-label"><?php echo $last_name_label; ?></label>
			<br/>
			<?php echo $last_name_input_tag; ?>
			<?php echo $last_name_error; ?>
			<span class="help-block"><?php echo $last_name_help; ?></span>
		</div>
	</fieldset>
	<input type="submit" value="<?php echo $submit_label; ?>" class="btn btn-primary">
</form>
