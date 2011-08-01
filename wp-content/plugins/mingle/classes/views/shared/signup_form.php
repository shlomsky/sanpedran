<form name="registerform" id="registerform" action="" method="post">
<input type="hidden" id="mngl-process-form" name="mngl-process-form" value="Y" />
  <p>
	  <label><?php _e('First Name', 'mingle'); ?>:<br />
	  <input type="text" name="user_first_name" id="user_first_name" class="input" value="<?php echo $user_first_name; ?>" size="20" tabindex="10" /></label>
  </p>    
  <p>
    <label><?php _e('Last Name', 'mingle'); ?>:<br />
    <input type="text" name="user_last_name" id="user_last_name" class="input" value="<?php echo $user_last_name; ?>" size="20" tabindex="10" /></label>
  </p>
	<p>
		<label><?php _e('Username', 'mingle'); ?>*:<br />
		<input type="text" name="user_login" id="user_login" class="input" value="<?php echo $user_login; ?>" size="20" tabindex="10" /></label>
	</p>
	<p>
		<label><?php _e('E-mail', 'mingle'); ?>*:<br />
		<input type="text" name="user_email" id="user_email" class="input" value="<?php echo $user_email; ?>" size="25" tabindex="20" /></label>
	</p>
  <p>
    <label><?php _e('Sex', 'mingle'); ?>*:&nbsp;<?php echo MnglProfileHelper::sex_dropdown('mngl_user_sex', $mngl_user_sex); ?></label>
  </p>
	<p id="reg_passmail"><?php _e('A password will be e-mailed to you.', 'mingle'); ?></p>
	<br class="clear" />
	<p class="submit"><input type="submit" name="wp-submit" id="wp-submit" class="mngl-share-button" value="<?php _e('Sign Up', 'mingle'); ?>" tabindex="100" /></p>
</form>
