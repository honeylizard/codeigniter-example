<div class="messages">
	<?php echo $message; ?>
</div>
<?php echo $form_tag; ?>
	<p>
		<?php echo $description; ?>
	</p>
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
	</div>
	<input type="submit" value="<?php echo $submit_label; ?>" class="btn btn-primary btn-block">
</form>
