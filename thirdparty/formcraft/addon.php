<?php  


require_once('config.fc.php');

define('FORMCRAFT_ADD','1');

if (!isset($_SESSION)) 
{
    session_start();
}

$restricted = array('10000','1','2','3','4','5','6','7','8','9','10','11','12','13','14','15','16','17','18','19','20','21','22','23','24','25');


if ( isset($_POST['action']) && function_exists($_POST['action']))
{
    $_POST['action']();
}

function mailchimp_fc($emails, $custom, $list_id, $double, $welcome)
{
    global $db_name;
    require_once('config.fc.php');

    $api = ORM::for_table('add_table')->where('application', 'mailchimp')->findOne()->code1;

    if($emails==null){ return false; } 
    require_once('MCAPI.class.php');
    $api = new MCAPI($api);

    foreach($emails as $email)
    {
        if(preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*$/i", $email)) {
            $api->listSubscribe($list_id, $email, $custom,'',$double,true,'',$welcome);
        }
    }
}

function campaign_fc($emails, $custom, $list_id){
    global $db_name;
    require_once('config.fc.php');

    $api = ORM::for_table('add_table')->where('application', 'campaign')->findOne()->code1;

    if($emails==null){ return false; } 

    require_once('campaign/csrest_subscribers.php');

    $auth = array('api_key' => $api);
    $wrap = new CS_REST_Subscribers($list_id, $auth);

    foreach($emails as $email)
    {
        $params = '';
        if(preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*$/i", $email)) 
        {
            $params['EmailAddress'] = $email;
            foreach ($custom as $key => $val)
            {
                $params[$key] = $val;
            }
            $result = $wrap->add($params);
        }
    }
}

function aweber_fc($emails, $custom, $list_id){
    global $db_name;
    require_once('config.fc.php');


    $key = ORM::for_table('add_table')->where('application', 'aweber')->findOne()->code1;

    $key = json_decode($key);


    if($emails==null){ return false; } 
    require_once('aweber/aweber_api/aweber_api.php');

    foreach($emails as $email)
    {
        $params = '';
        if(preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*$/i", $email)) 
        {


            $aweber = new AWeberAPI($key[0], $key[1]);

            try { 

                $account = $aweber->getAccount($key[2], $key[3]);
                $lists = $account->lists->find(array('name' => $listName));

                $list = $lists[0];
                $listURL = $list->self_link;

                $list = $account->loadFromUrl($listURL);

                $params['email'] = $email;

                foreach ($custom as $key => $val)
                {
                    $params[$key] = $val;
                }

                $subscribers = $list->subscribers;
                $new_subscriber = $subscribers->create($params);
            } catch(AWeberAPIException $exc) { 
                $error = "AWeberAPIException:\n\rType: $exc->type\n\rMsg : $exc->message\n\rDocs: $exc->documentation_url\n\r\n\r"; 
                error_log($error, 3, "errors_fc.log");
            }
        }
    }
}


function formcraft_add_update()
{
    global $db_name;


        // MailChimp
    if ($_POST['app']=='mailchimp')
    {

        require_once('MCAPI.class.php');

        $api = new MCAPI($_POST['code']);

        if ($api->ping())
        {

            $temp = ORM::for_table('add_table')->where('application', $_POST['app'])->findOne();
            $res = $temp->code1;

            if ($res==$_POST['code'])
            {
                echo 'saved';
                die();
            }


            $temp = ORM::for_table('add_table')->where('application', $_POST['app'])->find_result_set()->set('code1', $_POST['code'])->save();


            if ($result)
            {
                echo "saved";
            }

            die();

        }
        else
        {
            echo 'Connection failed';
            if ($_POST['code']==null)
            {
            $temp = ORM::for_table('add_table')->where('application', $_POST['app'])->find_result_set()->set('code1', NULL)->save();

            }
            die();
        }

    }


        // Campaign Monitor
    if ($_POST['app']=='campaign')
    {
        if ($_POST['code']==null)
        {

            $temp = ORM::for_table('add_table')->where('application', $_POST['app'])->find_result_set()->set('code1', NULL)->save();

        }
        else
        {
            require_once('campaign/csrest_general.php');
            $auth = array('api_key' => $_POST['code']);
            $wrap = new CS_REST_General($auth);

            $result = $wrap->get_clients();
            if($result->was_successful()) {

            $temp = ORM::for_table('add_table')->where('application', $_POST['app'])->find_result_set()->set('code1', $_POST['code'])->save();


                echo "saved";

            }
            else 
            {
                echo $result->response->Message;
            }
        }
    }


    if ($_POST['app']=='aweber')
    {
        require('aweber/aweber_api/aweber_api.php');

        try {
            $authorization_code = $_POST['code'];
            $auth = AWeberAPI::getDataFromAweberID($authorization_code);
            list($consumerKey, $consumerSecret, $accessKey, $accessSecret) = $auth;
            $size = sizeof($auth);

            if ($size==4)
            {

                $auth = json_encode($auth);


            $temp = ORM::for_table('add_table')->where('application', $_POST['app'])->find_result_set()->set('code1', $auth)->save();


                if ($temp)
                {
                    echo "saved";
                    die();
                }
            }
            else
            {
                echo 'Unidentified token response';
            }
            if ($_POST['code']==null)
            {
            $temp = ORM::for_table('add_table')->where('application', $_POST['app'])->find_result_set()->set('code1', NULL)->save();

            }
            die();
        }
        catch(AWeberAPIException $exc) {
            echo "<strong>Error</strong><br>
            Type: $exc->type<br>
            Message: $exc->message <br>          
            Regenerate the code and try again.";
            die();
        }

    }

    die();


}




function formcraft_add_builder()
{

    if (defined('FORMCRAFT_ADD'))
    {

                $mc = ORM::for_table('add_table')->where('application', 'mailchimp')->find_one()->code1;
                $aw = ORM::for_table('add_table')->where('application', 'aweber')->find_one()->code1;
                $campaign = ORM::for_table('add_table')->where('application', 'campaign')->find_one()->code1;

    }

    if ($mc)
    {
        ?>
        <Style>
            .mc_show
            {
                display: block !important;
            }
        </Style>

        <div class="accordion-group">
            <div class="accordion-heading">
                <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion_fo" href="#form_options_mailchimp" style='color: green'>
                    Integration: MailChimp
                </a>
            </div>

            <div id="form_options_mailchimp" class="accordion-body collapse">
                <div class="accordion-inner l2">

                    <div class='global_holder'>
                        <div class='gh_head'>
                            <img src='images/mc.png' alt='MailChimp' style='width: 150px'>
                        </div>
                        <span class='settings_desc'>You can use the forms to easily add to your list of subscribers on MailChimp.</span><br>

                        <p>Step 1 of 2<span class='settings_desc'>
                            Enter your List ID below. Click <a href='http://kb.mailchimp.com/article/how-can-i-find-my-list-id' target='_blank'>here</a> to know how to get the List ID.</span></p>
                            <input ng-model='con[0].mc_list' style='width: 100%' type='text'>
                            <br>
                            <label class='label_radio circle-ticked'>
                                <input type='checkbox' ng-model='con[0].mc_double' ng-true-value='true'>
                                <div class='label_div' style='background: #f3f3f3'>Double Opt-In</div>
                            </label>
                            <label class='label_radio circle-ticked'>
                                <input type='checkbox' ng-model='con[0].mc_welcome' ng-true-value='true'>
                                <div class='label_div' style='background: #f3f3f3'>Send Welcome Email</div>
                            </label>
                            <br><br>
                            <p>Step 2 of 2<span class='settings_desc'>
                                Now add an email field and check the option 'Add to MailChimp'.<br>That's it!</span>
                            </p>

                        </div>

                    </div>
                </div>
            </div>

            <?php
        }

        if ($campaign)
        {
            ?>
            <Style>
                .cm_show
                {
                    display: block !important;
                }
            </Style>

            <div class="accordion-group">
                <div class="accordion-heading">
                    <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion_fo" href="#form_options_campaign" style='color: green'>
                        Integration: Campaign Monitor
                    </a>
                </div>

                <div id="form_options_campaign" class="accordion-body collapse">
                    <div class="accordion-inner l2">

                        <div class='global_holder'>
                            <div class='gh_head'>
                                <img src='images/cm.png' alt='Campaign Monitor' style='width: 150px'>
                            </div>
                            <span class='settings_desc'>You can use the forms to easily add to your list of subscribers on Campaign Monitor.</span><br>

                            <p>Step 1 of 2<span class='settings_desc'>
                                Enter your List ID below.</span></p>
                                <input ng-model='con[0].campaign_list' style='width: 100%' type='text'>
                                <br>
                                <p>Step 2 of 2<span class='settings_desc'>
                                    Now add an email field and check the option 'Add to Campaign Monitor'.<br>That's it!</span>
                                </p>

                            </div>

                        </div>
                    </div>
                </div>

                <?php

            }

            if ($aw)
            {

                ?>
                <Style>
                    .aw_show
                    {
                        display: block !important;
                    }
                </Style>

                <div class="accordion-group">
                    <div class="accordion-heading">
                        <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion_fo" href="#form_options_aweber" style='color: green'>
                            Integration: AWeber
                        </a>
                    </div>

                    <div id="form_options_aweber" class="accordion-body collapse">
                        <div class="accordion-inner l2">

                            <div class='global_holder'>
                                <div class='gh_head'>
                                    <img src='images/aweber.png' alt='AWeber' style='width: 150px; margin-left: -12px'>
                                </div>
                                <span class='settings_desc'>You can use the forms to easily add to your list of subscribers on AWeber.</span><br>

                                <p>Step 2 of 3<span class='settings_desc'>
                                    Enter your List ID below. Click <a href='http://snibz.uservoice.com/knowledgebase/articles/93851-finding-your-aweber-id' target='_blank'>here</a> to know how to get the List ID.</span></p>
                                    <input ng-model='con[0].aw_list' style='width: 100%' type='text'>
                                    <br><br>
                                    <p>Step 3 of 3<span class='settings_desc'>
                                        Now add an email field and check the option 'Add to AWeber'.<br>That's it!</span>
                                    </p>

                                </div>

                            </div>
                        </div>
                    </div>


                    <?php



                }




            }

            function formcraft_add_content()
            {
                
                $mc = ORM::for_table('add_table')->where('application', 'mailchimp')->find_one()->code1;
                $aw = ORM::for_table('add_table')->where('application', 'aweber')->find_one()->code1;
                $campaign = ORM::for_table('add_table')->where('application', 'campaign')->find_one()->code1;
                

                ?>

                <form>

                    <div class='add_span_cover' id='addcover_1'>
                        <div class='add_span'>
                            <div class='as_img'>
                                <img src='images/mc.png' alt='MailChimp' style='width: 160px'>
                            </div>

                            <?php if ($mc)
                            {
                                ?>
                                <div class='addon_c' style='color: green; font-weight: bold'>Connected</div>
                                <div class='addon_nc' style='color: red; font-weight: bold; display: none'>Not Connected</div>
                                <?php
                            }
                            else
                            {
                                ?>
                                <div class='addon_c' style='color: green; font-weight: bold; display: none'>Connected</div>
                                <div class='addon_nc' style='color: red; font-weight: bold'>Not Connected</div>
                                <?php
                            }
                            ?>

                            <div class='op_div' style='opacity: <?php if ($mc) {echo '.3';} else {echo '1';}?>'>

                                <label>Enter the API Key</label>
                                <input type='text' style='width: 270px' id='add_1' app='mailchimp'>
                                <br>
                                <p>Click <a href='http://kb.mailchimp.com/article/where-can-i-find-my-api-key' target='_blank'>here</a> to know how to get an API key.
                                </p>
                                <button id='addbtn_1' type='button' class='sbtn sbtn-small fc_addbtn'>Save</button>
                                <div class='response' style='margin-top: 10px; font-size: 13px'></div>

                            </div>
                        </div>
                    </div>




                    <div class='add_span_cover' id='addcover_2'>
                        <div class='add_span'>
                           <div class='as_img'>
                             <img src='images/cm.png' alt='MailChimp' style='width: 160px'>
                         </div>

                         <?php if ($campaign)
                         {
                            ?>
                            <div class='addon_c' style='color: green; font-weight: bold'>Connected</div>
                            <div class='addon_nc' style='color: red; font-weight: bold; display: none'>Not Connected</div>
                            <?php
                        }
                        else
                        {
                            ?>
                            <div class='addon_c' style='color: green; font-weight: bold; display: none'>Connected</div>
                            <div class='addon_nc' style='color: red; font-weight: bold'>Not Connected</div>
                            <?php
                        }
                        ?>

                        <div class='op_div' style='opacity: <?php if ($campaign) {echo '.3';} else {echo '1';}?>'>

                            <label>Enter the API Key</label>
                            <input type='text' style='width: 270px' id='add_2' app='campaign'>
                            <br>
                            <p>Click <a href='http://help.campaignmonitor.com/topic.aspx?t=206' target='_blank'>here</a> to know how to get an API key.
                            </p>
                            <button id='addbtn_2' type='button' class='sbtn sbtn-small fc_addbtn'>Save</button>
                            <div class='response' style='margin-top: 10px; font-size: 13px'></div>

                        </div>
                    </div>
                </div>



                <div class='add_span_cover' id='addcover_3'>
                    <div class='add_span'>
                        <div class='as_img'>
                            <img src='images/aweber.png' alt='MailChimp' style='width: 160px'>
                        </div>

                        <?php if ($aw)
                        {
                            ?>
                            <div class='addon_c' style='color: green; font-weight: bold'>Connected</div>
                            <div class='addon_nc' style='color: red; font-weight: bold; display: none'>Not Connected</div>
                            <?php
                        }
                        else
                        {
                            ?>
                            <div class='addon_c' style='color: green; font-weight: bold; display: none'>Connected</div>
                            <div class='addon_nc' style='color: red; font-weight: bold'>Not Connected</div>
                            <?php
                        }
                        ?>

                        <div class='op_div' style='opacity: <?php if ($aw) {echo '.3';} else {echo '1';}?>'>

                            <label>Enter the Authorization Code</label>
                            <textarea style='width: 270px' rows='4' id='add_3' app='aweber'></textarea>
                            <br>
                            <p>Click <a href='https://auth.aweber.com/1.0/oauth/authorize_app/a908ab91' target='_blank'>here</a> to get the code.
                            </p>
                            <button id='addbtn_3' type='button' class='sbtn sbtn-small fc_addbtn' style='vertical-align: top'>Save</button>
                            <div class='response' style='margin-top: 10px; font-size: 13px'></div>

                        </div>
                    </div>
                </div>

            </form>
            <?php 

        }

        ?>