<div class="messages">
	<?php echo $message; ?>
</div>
<?php echo $form_tag; ?>
	<div class="form-group <?php echo $language_class; ?>">
		<label for="<?php echo $language_id; ?>" class="control-label"><?php echo $language_label; ?></label>
		<br/>
		<div class="input-group">
				<span class="input-group-addon">
					<i class="glyphicon glyphicon-globe" aria-hidden="true"></i>
				</span>
			<?php echo $language_select_tag; ?>
		</div>
		<?php echo $language_error; ?>
		<span class="help-block"><?php echo $language_help; ?></span>
	</div>

	<input type="submit" value="<?php echo $submit_label; ?>" class="btn btn-primary">
</form>
