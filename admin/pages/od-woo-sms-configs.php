<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://olivas.digital
 * @since      1.0.0
 *
 * @package    Od_Woo_Sms
 * @subpackage Od_Woo_Sms/admin
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<div class="wrap">
    <h1><?php _e( 'Transactional SMS\'s', 'od-woo-sms' ); ?></h1>
    <hr class="wp-header-end">
    <form method="POST" action="options.php" autocomplete="off">
        <div id="dashboard-widgets" class="metabox-holder">
            <div class="postbox-container">
                <?php settings_fields( 'od-woo-sms-configs' ); ?>
                <div class="postbox">
                    <div class="postbox-header">
                        <h2 class="hndle"><?php _e( 'Zenvia API - Authorization', 'od-woo-sms' ); ?></h2>
                    </div>
                    <div class="inside">
                        <div class="main">
                            <div class="field">
                                <label for="od_woo_sms_account"><?php _e( 'Account', 'od-woo-sms' ); ?></label>
                                <input type="text" name="od_woo_sms_account" value="<?= esc_attr( get_option('od_woo_sms_account') ) ?>">
                            </div>
                            <div class="field">
                                <label for="od_woo_sms_password"><?php _e( 'Password', 'od-woo-sms' ); ?></label>
                                <input type="password" name="od_woo_sms_password" value="<?= esc_attr( get_option('od_woo_sms_password') ) ?>">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="postbox">
                    <div class="postbox-header">
                        <h2 class="hndle"><?php _e( 'SMS Request', 'od-woo-sms' ); ?></h2>
                    </div>
                    <div class="inside">
                        <div class="main">
                            <div class="field">
                                <label for="od_woo_sms_from"><?php _e( 'From', 'od-woo-sms' ); ?></label>
                                <input id="od_woo_sms_from" type="text" name="od_woo_sms_from" value="<?= esc_attr( get_option('od_woo_sms_from') ) ?>">
                            </div>
                            <div class="field">
                                <?php
                                $to = get_option('od_woo_sms_to');
                                $customized = '' !== $to && 'billing_phone' !== $to && 'billing_cellphone' !== $to ? true : false;
                                $customized_selected = $customized ? 'selected="selected"' : '';
                                $customized_class = $customized ? 'visible' : '';
                                ?>
                                <label for="od_woo_sms_to"><?php _e( 'To', 'od-woo-sms' ); ?></label>
                                <select id="select-to">
                                    <option value="">-- selecione --</option>
                                    <option value="billing_phone" <?= 'billing_phone' === $to ? 'selected="selected"' : '' ?>><?php _e( 'Billing Phone', 'od-woo-sms' ); ?></option>
                                    <option value="billing_cellphone" <?= 'billing_cellphone' === $to ? 'selected="selected"' : '' ?>><?php _e( 'Billing Cellphone', 'od-woo-sms' ); ?></option>
                                    <option value="customized" <?= $customized_selected ?>><?php _e( 'Customized', 'od-woo-sms' ); ?></option>
                                </select>
                                <input id="od_woo_sms_to" type="text" name="od_woo_sms_to" placeholder="<?php _e( 'Select above or enter a custom field...', 'od-woo-sms' ); ?>" value="<?= esc_attr( get_option('od_woo_sms_to') ) ?>" class="od_woo_sms_to <?= $customized_class ?>">
                            </div>
                            <div class="field">
                                <?php
                                $submit_trigger = get_option('od_woo_sms_submit_trigger');
                                $submit_trigger = $submit_trigger ? array_flip($submit_trigger) : $submit_trigger;
                                $submit_trigger = $submit_trigger ? array_fill_keys(array_keys($submit_trigger), 1) : $submit_trigger;
                                $wc_emails = WC_Emails::instance();
                                $emails    = $wc_emails->get_emails();
                                if (!empty( $emails )) {
                                ?>
                                <label for="od_woo_sms_submit_trigger"><?php _e( 'Trigger Email', 'od-woo-sms' ); ?></label>
                                <select id="od_woo_sms_submit_trigger" name="od_woo_sms_submit_trigger[]" multiple>
                                <?php 
                                $emails_arr = array();
                                foreach ($emails as $email) { 
                                    $id = $email->title === 'Admin Delivery Reminder' ? 'admin_' . $email->id : $email->id;
                                    $selected = $submit_trigger && isset($submit_trigger[$id]) ? true : false;
                                ?>
                                    <option value="<?php echo $id ?>" <?= $selected ? 'selected="selected"' : '' ?>><?php echo $email->title; ?></option>
                                <?php
                                    $emails_arr[$id] = $email->title;
                                } 
                                ?>
                                </select>
                                <?php
                                }
                                ?>
                                <small><?php _e( 'Use the Ctrl key to select more than one.', 'od-woo-sms' ) ?></small>
                            </div>
                        </div>
                    </div>
                </div>
                <p class="submit" style="margin-top: 12px;">
                    <input class="button button-primary" type="submit" value="Salvar">
                </p>
            </div>
            <div class="postbox-container">
                <?php 
                $submit_trigger = get_option('od_woo_sms_submit_trigger');
                if ($submit_trigger) {

                foreach ($submit_trigger as $trigger) {

                    $msg = get_option($trigger.'_od_woo_sms_msg');
                ?>
                <div class="postbox">
                    <div class="postbox-header">
                        <h2 class="hndle"><?= $emails_arr[$trigger] ?> SMS</h2>
                    </div>
                    <div class="inside">
                        <div class="main">
                            <div class="field">
                                <label for="<?= $trigger ?>_od_woo_sms_msg"><?php _e( 'Message', 'od-woo-sms' ); ?></label>
                                <textarea id="<?= $trigger ?>_od_woo_sms_msg" name="<?= $trigger ?>_od_woo_sms_msg"><?= $msg ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <?php 
                }
                } else {
                ?>
                <div class="empty-container">
                    <?php _e( 'Save the data', 'od-woo-sms' ); ?>
                </div>
                <?php 
                }
                ?>
            </div>
        </div>
    </form>
</div>
