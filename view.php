<?php

defined('ABSPATH') or die('Nope nope nope...');

//Add the Select2 CSS file
wp_enqueue_style('select2-css', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css', array(), '4.1.0-rc.0');

//Add the Select2 JavaScript file
wp_enqueue_script('select2-js', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js', 'jquery', '4.1.0-rc.0');

?>
<style>
    .box-area {
        font-size: 15px;
        background: #fff;
        border: 1px solid #e5e5e5;
        /* margin-top: 10px; */
        padding: 20px 20px 20px 20px;
        margin-right: 20px;
    }

    .box-area ul li {
        list-style-type: disc;
        margin-left: 30px;
    }

    pre {
        background: #f3f3f3;
        padding: 10px;
        margin-top: 20px;
    }

    .box-area h2 {
        border-bottom: 1px solid #ececec;
        padding-bottom: 10px;
    }
</style>
<br />
<br />
<div class="box-area">
    <h2>Introduction</h2>
    <a href="https://emails-checker.net" target="_blank"><img src="<?php echo plugin_dir_url(__FILE__) . 'assets/emails-checker-logo.png'; ?>" style="float: right; width: 190px;"></a>
    You can use <a href="https://emails-checker.net" target="_blank">Emails-Checker.NET API</a> to verify email a ddresses in real-time on your website. Emails Checker Real-Time API engine perform the following checks:
    <br>
    <ul>
        <li>DNS validation, including MX record lookup</li>
        <li>Disposable email address detection realtime</li>
        <li>Misspelled domain detection</li>
        <li>Email Syntax verification (IETF/RFC standard conformance)</li>
        <li>Mailbox existence checking</li>
        <li>Catch-All Testing</li>
        <li>Greylisting detection</li>
        <li>SMTP connection and availability checking</li>
        <ul>
            <br>
            <b>Note:</b><br>
            Our Plugin will do basic syntax checking on site to avoid spam emails and reduce api usage. Invalid Syntax Emails will not be count for API Credits usage its <b>FREE</b>.
            <br />
            <br />
            <b>Where to Get Free Credits?</b><br>
            You can register for a <a href="https://app.emails-checker.net/register" target="_blank">FREE API key</a> (limited to 100 smtp email address checks).<br>
            If you have more than 100 contact forms submission monthly, please have a look at our very cheap pricing model <a href="https://emails-checker.net/pricing" target="_blank">subscription plans</a> we offer.
</div>

<?php
//status_color 
if (empty(get_option('urev_access_key_status_color'))) {
    $status_color = "gray";
} else {
    $status_color = get_option('urev_access_key_status_color');
}

//key_status
if (empty(get_option('urev_access_key_status'))) {
    $key_status = "NOT CONNECTED";
} else {
    $key_status = get_option('urev_access_key_status');
}

?>

<div class="box-area" style="font-size: 15px; background: #fff; border: 1px solid #e5e5e5; margin-top: 20px; padding: 0 20px 20px; margin-right: 20px;">
    <h2>Setup Your Plugin:</h2>
    <p>
        The plugin will look at every form request and any field that contains the word "email." which is directly linked to the function is_email.
    </p>
    <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="POST">

        <?php
        //print error messages
        if (!is_null($kprjOutput)) {
            echo $kprjOutput;
        }
        ?>

        <table class="form-table">
            <tbody>
                <tr valign="top">
                    <th scope="row">Connection Status</th>
                    <td>
                        <span style="
                        text-align: center;
                        width: 25%;
                        display: inline-block;
                        padding: 3px 6px;
                        color: #fff;
                        font-size: 12px;
                        font-weight: 700;
                        background-color:<?php echo $status_color  ?>"><?php echo $key_status ?></span>
                    </td>
                </tr>
                <?php if ($available_credits) : ?>
                    <tr valign="top">
                        <th scope="row">Available Balance</th>
                        <td>
                            <b>
                                <?php echo $available_credits ?>
                            </b>
                        </td>
                    </tr>
                <?php endif; ?>
                <tr valign="top">
                    <th scope="row"><label for="mailchimp_api_key">API Key</label></th>
                    <td>
                        <input type="hidden" value="1" name="update" />
                        <!--wp_nonce_field for security-->
                        <?php wp_nonce_field("urev_action_nonce", "urev_name_nonce"); ?>
                        <input type="text" style="width: 50%; color:<?php echo get_option('urev_access_key_status_color'); ?>;" value="<?php echo $urev_access_key; ?>" name="urev_access_key" />
                        <p class="help">
                            The API key for connecting with your Emails-Checket.net account. <a target="_blank" href="https://app.emails-checker.net/apis">Get your API key here.</a>
                        </p>
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row"><label for="urev_block_on_error">Block Emails when out of credits?</label></th>
                    <td>
                        <input type="checkbox" id="urev_block_on_error" name="urev_block_on_error" <?php echo get_option('urev_block_on_error') ? 'checked' : '' ?>>
                    </td>
                </tr>


                <tr valign="top">
                    <th scope="row"><label for="urev_allowed_email_types">Allowed Email Types</label></th>
                    <td>
                        <select name="urev_allowed_email_types[]" style="width: 50%;" id="example-select" multiple>
                            <option value="deliverable">DELIVERABLE</option>
                            <option value="unknown">UNKNOWN</option>
                            <option value="undeliverable">UNDELIVERABLE</option>
                            <option value="risky">RISKY</option>
                        </select>
                        <p class="help">
                            Recommended (DELIVERABLE,UNKNOWN)
                        </p>
                    </td>
                </tr>



            </tbody>
        </table>
        <hr>
        <br />
        <input type="submit" class="button-primary" name="save_urev_access_key" value="Save Settings" style="width: 25%;" />
    </form>

    <?php if (!empty($updated)) : ?>
        <p style="color:green;">Settings were updated successfully!</p>
    <?php endif; ?>



    <script>
        jQuery(document).ready(function($) {
            //initiate     
            $('#example-select').select2({
                multiple: true,
            });

            //fill the preselected
            let urev_allowed_email_types = <?php echo json_encode(get_option('urev_allowed_email_types')) ?>;
            console.log(urev_allowed_email_types);
            $("#example-select").val(urev_allowed_email_types).trigger("change");
        });
    </script>

</div>