<?php

function wpam_mailpoet_admin_interface() {
    echo '<div class="wrap">';   
    echo '<h2>Affiliates Manager and MailPoet</h2>';

    if (isset($_POST['wpam_mailpoet_save_settings'])) {
        
        $list_id = $_REQUEST['wpam_mailpoet_list_id'];
        update_option('wpam_mailpoet_list_id', $list_id);
        
        echo '<div id="message" class="updated fade">';
        echo '<p>MailPoet Settings Saved!</p>';
        echo '</div>';
    }
    //echo '<div id="poststuff"><div id="post-body">';
    ?>

    <p style="background: #fff6d5; border: 1px solid #d1b655; color: #3f2502; margin: 10px 0;  padding: 5px 5px 5px 10px;">
        Read the <a href="https://wpaffiliatemanager.com/sign-affiliates-to-mailpoet-list/" target="_blank">usage documentation</a> to learn how to use the MailPoet integration addon
    </p>
    <div id="poststuff"><div id="post-body">
    <form action="" method="POST">

        <div class="postbox">
            <h3 class="hndle"><label for="title">MailPoet Integration Settings</label></h3>
            <div class="inside">
                <table class="form-table">
                    
                    <tr valign="top"><td width="25%" align="left">
                            MailPoet List ID
                        </td><td align="left">
                            <input name="wpam_mailpoet_list_id" type="text" size="20" value="<?php echo get_option('wpam_mailpoet_list_id'); ?>"/>                   
                            <p class="description">Your affiliates will be subscribed to this MailPoet list when they sign up for an account.</p>
                        </td>
                    </tr>
                    
                </table>
            </div></div>
        <input type="submit" name="wpam_mailpoet_save_settings" value="Save" class="button-primary" />

    </form>


    <?php
    echo '</div></div>'; //end of poststuff and post-body
    echo '</div>'; //end of wrap 
}
