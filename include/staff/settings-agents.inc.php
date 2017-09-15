<?php
if (!defined('OSTADMININC') || !$thisstaff || !$thisstaff->isAdmin() || !$config)
    die('Access Denied');
?>
<section class="content-header">
    <h1><?php echo __('Agents Settings'); ?></h1>
</section>

<form action="settings.php?t=agents" method="post" class="save">
    <?php csrf_token(); ?>
    <input type="hidden" name="t" value="agents" >
    <section class="content">
        <div class="row">
		<!-- General Settings Box --> 
	    <div class="col-xs-12 col-lg-6">
            <div class="box">
                <div class="box-header with-border">
                     <h3 class="box-title"><?php echo __('General Settings'); ?></h3>
                </div>
                <div class="box-body">
				<div class="form-group">                        
					<label for="agent_name_format" class="col-sm-4 control-label"><?php echo __('Name Formatting'); ?></label>                 
					<div class="col-sm-6">                   
                	<div class="input-group">       
						      <select name="agent_name_format" class="form-control">
                 	<?php
								
                                    foreach (PersonsName::allFormats() as $n => $f) {
                                        list($desc, $func) = $f;
                                        $selected = ($config['agent_name_format'] == $n) ? 'selected="selected"' : '';
                                        ?>
                                        <option value="<?php echo $n; ?>" <?php echo $selected;
                                        ?>><?php echo __($desc); ?></option>
                                            <?php } ?>
                               </select>
							<span class="input-group-addon"><i class="help-tip fa fa-question-circle" href="#agent_name_format"></i></span>
                          </div>
					</div>
				</div>               
				<div class="form-group">                        
		<label for="hide_staff_name" class="col-sm-4 control-label"><?php echo __('Agent Identity Masking'); ?>:</label>                 
        	<div class="col-sm-6">                   
                	<div class="input-group">                           
                                <input type="checkbox" class="form-control" name="hide_staff_name" <?php echo $config['hide_staff_name'] ? 'checked="checked"' : ''; ?>>
                                 <?php echo __("Hide agent's name on responses."); ?>
				<span class="input-group-addon"><i class="help-tip fa fa-question-circle" href="#staff_identity_masking"></i></span>
                           </div>
		</div>
	    </div>                            
				<div class="form-group"> 
				
		<label for="agent_avatar" class="col-sm-4 control-label"><?php echo __('Avatar Source'); ?>:</label>                 
        	<div class="col-sm-6">                             
			<div class="input-group">   
                                <select name="agent_avatar" class="form-control">
                                  <?php
                                    require_once INCLUDE_DIR . 'class.avatar.php';
                                    foreach (AvatarSource::allSources() as $id => $class) {
                                        $modes = $class::getModes();
                                        if ($modes) {
                                            echo "<optgroup label=\"{$class::getName()}\">";
                                            foreach ($modes as $mid => $mname) {
                                                $oid = "$id.$mid";
                                                $selected = ($config['agent_avatar'] == $oid) ? 'selected="selected"' : '';
                                                echo "<option {$selected} value=\"{$oid}\">{$class::getName()} / {$mname}</option>";
                                            }
                                            echo "</optgroup>";
                                        } else {
                                            $selected = ($config['agent_avatar'] == $id) ? 'selected="selected"' : '';
                                            echo "<option {$selected} value=\"{$id}\">{$class::getName()}</option>";
                                        }
                                    }
                                    ?>                              
				</select>
                                <div class="error"><?php echo Format::htmlchars($errors['agent_avatar']); ?></div>
			</div>
                </div>           
            </div>          
				</div> <!-- Box Body -->
			</div><!-- Box -->
		 </div><!-- col-xs-12 col-lg-6 -->
		
		<!-- Authentication Settings Box --> 
        <div class="col-xs-12 col-lg-6">
		
		
		<div class="box">
			<div class="box-header with-border">
                    	<h3 class="box-title"><?php echo __('Authentication Settings'); ?></h3>
            </div>
			<div class="box-body">
				<div class="form-group">                        
						<label for="passwd_reset_period" class="col-sm-4 control-label"><?php echo __('Password Expiration Policy'); ?>:</label>                 
        					<div class="col-sm-6">
							   <select name="passwd_reset_period" class="form-control">
                                    				  <option value="0"> &mdash; <?php echo __('No expiration'); ?> &mdash;</option>
                                       				<?php
                                    					for ($i = 1; $i <= 12; $i++) {
                                        				echo sprintf('<option value="%d" %s>%s</option>', $i, (($config['passwd_reset_period'] == $i) ? 'selected="selected"' : ''), sprintf(_N('Monthly', 'Every %d months', $i), $i));
                                    					}
                                    				?>                              
							  </select>
                                			<font class="error"><?php echo $errors['passwd_reset_period']; ?></font>
							<span class="input-group-addon"><i class="help-tip fa fa-question-circle" href="#password_expiration_policy"></i></span>
							
						</div> 
				</div>
				<div class="form-group">                        
						<label for="allow_pw_reset" class="col-sm-4 control-label"><?php echo __('Allow Password Resets'); ?>:</label>                 
        					<div class="col-sm-6">
							   <input type="checkbox" class="form-control" name="allow_pw_reset" <?php echo $config['allow_pw_reset'] ? 'checked="checked"' : ''; ?>>
                               
							<span class="input-group-addon"><i class="help-tip fa fa-question-circle" href="#allow_password_resets"></i></span>
							
						</div> 
				</div>
				<div class="form-group">                        
						<label for="pw_reset_window" class="col-sm-4 control-label"><?php echo __('Reset Token Expiration'); ?>:</label>                 
        					<div class="col-sm-6">
							    <input type="text" class="form-control" name="pw_reset_window" size="6" value="<?php echo $config['pw_reset_window']; ?>"><em><?php echo __('minutes'); ?></em>
                                			<font class="error"><?php echo $errors['pw_reset_window']; ?></font>
							<span class="input-group-addon"><i class="help-tip fa fa-question-circle" href="#reset_token_expiration"></i></span>
							
						</div> 
				</div>
				<div class="form-group">                        
					<label for="staff_max_logins" class="col-sm-4 control-label"><?php echo __('Agent Excessive Logins'); ?>:</label>                 
        				<div class="col-sm-6">
							<select name="staff_max_logins" class="form-control">
										<?php
                                    		for ($i = 1; $i <= 10; $i++) {
                                        	echo sprintf('<option value="%d" %s>%d</option>', $i, (($config['staff_max_logins'] == $i) ? 'selected="selected"' : ''), $i);
											}
                                    	?>
							</select>  <?php echo __('failed login attempt(s) allowed before a lock-out is enforced'); ?>
							<br>
                             <select name="staff_login_timeout" class="form-control>
                                    <?php
                                    for ($i = 1; $i <= 10; $i++) {
                                        echo sprintf('<option value="%d" %s>%d</option>', $i, (($config['staff_login_timeout'] == $i) ? 'selected="selected"' : ''), $i);
                                    }
                                    ?>                              
							</select><?php echo __('minutes locked out'); ?>      

						</div> 
				</div>
				<div class="form-group">                        
					<label for="staff_session_timeout" class="col-sm-4 control-label"><?php echo __('Agent Session Timeout'); ?>:</label>                 
        				<div class="col-sm-6">
							 <input type="text" class="form-control" name="staff_session_timeout" size="6" value="<?php echo $config['staff_session_timeout']; ?>"> <?php echo __('minutes'); ?><em><?php echo __('(0 to disable)'); ?></em>. 
							<span class="input-group-addon"><i class="help-tip fa fa-question-circle" href="#staff_session_timeout"></i></span>
						</div> 
				</div>
				<div class="form-group">                        
						<label for="staff_ip_binding" class="col-sm-4 control-label"><?php echo __('Bind Agent Session to IP'); ?>:</label>                 
        					<div class="col-sm-6">
							 <input type="checkbox" class="form-control" name="staff_ip_binding"<?php echo $config['staff_ip_binding'] ? 'checked="checked"' : ''; ?>>
							<span class="input-group-addon"><i class="help-tip fa fa-question-circle" href="#bind_staff_session_to_ip"></i></span>
							
						</div> 
				</div>
			</div> <!-- Box Body -->
		</div> <!-- Box -->
        </div> <!-- "col-xs-12 col-lg-6" -->         

		</div> <!-- row -->
		<div class="row">
        <div class="col-xs-12 col-lg-6">
		
		
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title"><?php echo __('Authentication and Registration Templates &amp; Pages'); ?></h3>
                    </div>
                    <div class="box-body">
                        <?php
                        $res = db_query('select distinct(`type`), id, notes, name, updated from '
                                . PAGE_TABLE
                                . ' where isactive=1 group by `type`');
                        $contents = array();
                        while (list($type, $id, $notes, $name, $u) = db_fetch_row($res))
                            $contents[$type] = array($id, $name, $notes, $u);

                        $manage_content = function($title, $content) use ($contents) {
                            list($id, $name, $notes, $upd) = $contents[$content];
                            $notes = explode('. ', $notes);
                            $notes = $notes[0];
                            ?>
                            <tr>
                                <td colspan="2">
                                    <div style="padding:2px 5px">
                                        <a href="#ajax.php/content/<?php echo $id; ?>/manage"
                                           onclick="javascript:
                                                           $.dialog($(this).attr('href').substr(1), 201);
                                                   return false;" class="pull-left">
                                            <i class="icon-file-text icon-2x" style="color:#bbb;"></i>
                                        </a>
                                        <span style="display:inline-block;width:90%;width:calc(100% - 32px);padding-left:10px;line-height:1.2em">
                                            <a href="#ajax.php/content/<?php echo $id; ?>/manage"
                                               onclick="javascript:
                                                               $.dialog($(this).attr('href').substr(1), 201, null, {size: 'large'});
                                                       return false;"><?php echo Format::htmlchars($title); ?>
                                            </a>
                                        </span>
                                        <span class="faded"><?php echo Format::display($notes); ?>
                                            <br />
                                            <em><?php echo sprintf(__('Last Updated %s'), Format::datetime($upd));
                            ?></em>
                                        </span>
                                    </div>
                                </td>
                            </tr>
                        <?php };
                        ?>
                        <?php $manage_content(__('Agent Welcome Email'), 'registration-staff'); ?>
                        <?php $manage_content(__('Sign-in Login Banner'), 'banner-staff'); ?>
                        <?php $manage_content(__('Password Reset Email'), 'pwreset-staff'); ?>
                    </div> <!-- /box-body -->
                </div> <!-- /box -->
            </div>

        </div><!-- Row -->
            <div class="row">
                <div class="col-xs-12">
                    <div class="box">
                        <div class="box-body">
                            <button type="reset" class="btn btn-default"><?php echo __('Reset Changes'); ?></button>
                            <button type="submit" class="btn btn-primary"><?php echo __('Save Changes'); ?></button>
                        </div>
                    </div>
                </div>
            </div>
    </section>
</form>
