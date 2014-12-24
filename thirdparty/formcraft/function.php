<?php  
  /* 
  Plugin Name: FormCraft
  Author: nCrafts, http://ncrafts.net/
  Version: 1.3.8
  License: Governed by CodeCanyon Licensing. A standard license allows usage on one site. For more details: http://codecanyon.net/licenses/regular
  */

  require_once('config.fc.php');

  if (!isset($_SESSION)) 
  {
    session_start();
  }

  global $db_name;


  if (function_exists($_POST['action']))
  {
    $_POST['action']();
  }

  function formcraft_login()
  {

    global $user, $password;
    if (empty($_POST['user']) || empty($_POST['password']))
    {
      $result['done'] = 'error';
      $result['message'] = 'Incorrect user / password';
      echo json_encode($result);
      die();
    }
    if ( $_POST['user']!=$user || $_POST['password']!=$password )
    {
      $result['done'] = 'error';
      $result['message'] = 'Incorrect user / password.';
      echo json_encode($result);
      die();
    }
    $_SESSION['username'] = $_POST['user'];
    $result['done'] = 'login';
    $result['message'] = 'Redirecting ...';
    echo json_encode($result);
    die();
  }


  function formcraft_test_email()
  {

    global $db_name;
    $id = $_POST['id'];

    $result = ORM::for_table('builder')->find_one($id);

    $con = stripslashes($result['con']);
    $rec = stripslashes($result['recipients']);

    $con = json_decode($con, 1);
    $rec = json_decode($rec, 1);

    if (sizeof($rec)==0)
    {
      echo "No email recipient added";
      die();
    }
    $sender_name = $con[0]['from_name'];
    $sender_email = $con[0]['from_email'];
    $counter = 0;

    if ($con[0]['mail_type']=='smtp')
    {

      require_once("php/class.phpmailer.php");


      foreach($rec as $send_to)
      {

        $to = $send_to['val'];

        $mail = new PHPMailer();

        $mail->IsSMTP();
        $mail->Host = $con[0]['smtp_host'];

        $mail->CharSet = 'UTF-8';

        $mail->SMTPAuth = true;
        $mail->Username = $con[0]['smtp_email'];
        $mail->Password = $con[0]['smtp_pass'];
        $mail->FromName = $con[0]['smtp_name'];
        $mail->AddAddress($to);
        $mail->From = $con[0]['smtp_email'];
        $mail->IsHTML(true);

        if ($con[0]['if_ssl']=='ssl')
        {
          $mail->SMTPSecure = 'ssl';
          $mail->Port = 465;
        }

        $mail->Subject = 'Test Email from FormCraft';
        $mail->Body = 'Test Email from FormCraft';

        if ($mail->Send())
        {
          $counter++;
        }

      }

      } // End of SMTP Email
      else
      {


        $headers = "From: $sender_name <$sender_email>\r\nReply-To: $sender_email\r\n";
        $headers.= 'MIME-Version: 1.0' . "\r\n";
        $headers.= 'Content-type: text/html; charset=utf-8' . "\r\n";

        $subject = 'Test Email from FormCraft';
        $message = 'Test Email from FormCraft';


        foreach($rec as $send_to)
        {
          $to = $send_to['val'];
          if (mail($to, $subject, $message, $headers))
          {
            $counter++;
          }
        }
      } // End of PHP Function Email

      echo $counter.' email(s) sent.';

      die();
    }



    function formcraft_increment2()
    {
      formcraft_increment($_POST['id']);
    }


    function formcraft_increment($id)
    {

      global $db_name;

      if (!isset($id))
      {
        if (isset($_POST['id']))
        {
          $id = $_POST['id'];
        }
        else if (isset($_GET['id']))
        {
          $id = $_GET['id'];
        }
      }


      $result = ORM::for_table('builder')->find_one($id);
      $result->views = $result->views + 1; $result->save();

      $date = date('Y-m-d');
      $newResult = ORM::for_table('info_table')->where('id', $id.$date)->find_one();

      if ($newResult>=1)
      {
        $newResult->views =  $newResult->views + 1; $newResult->save();
      }
      else
      {
        $newResult = ORM::for_table('info_table')->create();
        $newResult->id = $id.$date; $newResult->form = $id; $newResult->time = $date; $newResult->views = 1; $newResult->submissions = 0;
        $newResult->save();
      }


    }



    function formcraft_chart()
    {
      error_reporting(0);

      global $db_name;

      if (ctype_digit($_POST['id']))
      {
        $temp = ORM::for_table('info_table')->where('form', $_POST['id'])->order_by_asc('time')->findMany();
      }
      else
      {
        $temp = ORM::for_table('info_table')->order_by_asc('time')->findMany();
      }

      foreach ($temp as $key => $temp12)
      {
        $subs[] = array('id'=>$temp12->form,'time'=>$temp12->time,'views'=>$temp12->views,'submissions'=>$temp12->submissions);
      }


      foreach ($subs as $key => $value) 
      {
        if ($subs[$key]['time']==$subs[$key+1]['time'])
        {
          $subs[$key+1]['views'] = $subs[$key+1]['views']+$subs[$key]['views'];
          $subs[$key+1]['submissions'] = $subs[$key+1]['submissions']+$subs[$key]['submissions'];
          unset($subs[$key]);
        }
      }



      foreach ($subs as $key => $value)
      {

        $dt = date_parse($subs[$key]['time']);
        $diff_m = abs(($dt['month']-date('m'))*30);
        $diff_d = date('d')-$dt['day'];
        $month = date('Y-m-d');
        $diff = $diff_m+$diff_d;
        $subs[$key]['time'] = date("d M", strtotime($subs[$key]['time']));


        if ($diff<30)
        {
          $temp2 = array();
          $temp2[] = array('v' => (string) $subs[$key]['time'], 'f' => null); 
          $temp2[] = array('v' => (int) $subs[$key]['views'], 'f' => null); 
          $temp2[] = array('v' => (int) $subs[$key]['submissions'], 'f' => null); 
          $rows2[] = array('c' => $temp2);    
        }

      }


      echo '
      {
        "cols": 
        [
        {"id":"","label":"Day","pattern":"","type":"string"},
        {"id":"","label":"Views","pattern":"","type":"number"},
        {"id":"","label":"Submissions","pattern":"","type":"number"}
        ],
        "rows": 
        '.stripslashes(json_encode($rows2)).'}';
        die();

      }


      function formcraft_delete_file()
      {

        $url = $_POST['url'];
        $file_name = "file-upload/server/php/files/".basename(urldecode($url));
        $file_name2 = "file-upload/server/php/files/thumbnail/".basename(urldecode($url));

        if (is_file($file_name))
        {
          unlink($file_name);
          unlink($file_name2);
          echo "Deleted";
        }
        else
        {
          echo "Not";
        }
        die();
      }

      function formcraft_sub_upd()
      {


        global $db_name;

        $id = $_POST['id'];
        $type = $_POST['type'];

        if ($type=='upd')
        {
          $result = ORM::for_table('submissions')->find_one($id);
          $result->seen = '1'; $result->save();
        }
        else if ($type=='del')
        {
          $result = ORM::for_table('submissions')->find_one($id)->delete();
          if ($result)
          {    
            echo 'D';
          }
        }
        else if ($type=='read')
        {
          $result = ORM::for_table('submissions')->find_one($id);
          $result->seen = '0'; $result->save();
          if ($result)
          {    
            echo 'D';
          }
        }
        die();

      }

      function formcraft_name_update()
      {


        global $db_name;

        $result = ORM::for_table('builder')->find_one($_POST['id']);
        $result->name = $_POST['name']; $result->save();

        echo 'D';

        die();

      }






      function formcraft_submit()
      {

        global $errors, $id, $db_name;
        $id = $_POST['id'];

        $conten = file_get_contents('php://input');
        $conten = explode('&', $conten);
        $nos = sizeof($conten);
        $title = $_POST['title'];

        $i = 0;
        while ($i<$nos)
        {
          $cont = explode('=', $conten["$i"]);
          $content[$cont[0]]=$cont[1];
          $content_ex = explode('_',$cont[0]);
          if ( !($content_ex[0]=='id') && !($content_ex[0]=='action') )
          {
            $new[$i]['label'] = $content_ex[0];
            $new[$i]['value'] = urldecode($cont[1]);
            $new[$i]['type'] = $content_ex[1];
            $new[$i]['validation'] = $content_ex[2];
            $new[$i]['required'] = $content_ex[3];
            $new[$i]['min'] = $content_ex[4];
            $new[$i]['max'] = $content_ex[5];
            $new[$i]['tooltip'] = $content_ex[6];
            $new[$i]['custom'] = $content_ex[7];
            $new[$i]['custom2'] = $content_ex[8];
            $new[$i]['custom3'] = $content_ex[9];
            $new[$i]['custom4'] = $content_ex[10];
          }
          $i++;
        }

        $myrows = ORM::for_table('builder')->find_one($id);


        $con = stripslashes($myrows['con']);
        $title = stripslashes($myrows['name']);
        $rec = stripslashes($myrows['recipients']);

        $con = json_decode($con, 1);
        $rec = json_decode($rec, 1);


  // Run the Validation Functions
        $i = 0;

        $ar_inc = 1;
        while ($i<$nos)
        {
          if ($new[$i]['custom']=='autoreply')
            {$autoreply[$ar_inc]=$new[$i]['value']; $ar_inc++;}
          $new[$i]['custom3'] = 'zz'.$new[$i]['custom3'];


   // Prepare List for MailChimp
          if ($new[$i]['type']=='email' && (strpos($new[$i]['custom3'], 'm')==true) )
            { $mc_add[]=$new[$i]['value'];}

   // Prepare List for AWeber
          if ($new[$i]['type']=='email' && (strpos($new[$i]['custom3'], 'a')==true) )
            { $aw_add[]=$new[$i]['value'];}

   // Prepare List for Campaign Monitor
          if ($new[$i]['type']=='email' && (strpos($new[$i]['custom3'], 'c')==true) )
            { $campaign_add[]=$new[$i]['value'];}

  // Prepare List of Custom Variables for MC or MM
          if ( $new[$i]['type']!='email' && isset($new[$i]['custom']) )
          {
            if (!empty($new[$i]['value']))
            {
              $custom_var[$new[$i]['custom']] = $new[$i]['value'];            
            }
          }



          if ($new[$i]['custom2']=='replyto')
            {$replyto = $new[$i]['value'];}

          if ($new[$i]['type']=='upload' && $new[$i]['value']=='0')
            {$new[$i]['value']=null;}


          formcraft_no_val($new[$i]['value'], $new[$i]['required'], $new[$i]['min'], $new[$i]['max'], $new[$i]['tooltip'], $con[0]);


          if (function_exists('formcraft_'.$new[$i]['validation']))
          {
            $fncall = 'formcraft_'.$new[$i]['validation'];
            $fncall($new[$i]['value'], $new[$i]['validation'], $new[$i]['required'], $new[$i]['min'], $new[$i]['max'], $new[$i]['tooltip'], $con[0]);
          }

          $i++;
        }

        if( sizeof($errors) )
        {
          if ($con[0]['error_gen']!=null)
          {
            $errors['errors'] = $con[0]['error_gen'];
          }
          else
          {
            $errors['errors'] = 'none';
          }
          $errors = json_encode($errors);
          echo $errors;
        }
        else
        {   

          $sender_name = $con[0]['from_name'];
          $sender_email = $con[0]['from_email'];

          $success_sent = 0;


          require_once('addon.php');


          if (defined('FORMCRAFT_ADD'))
          {

            if ($con[0]['mc_double']=='true') {$con[0]['mc_double']=true;} else {$con[0]['mc_double']=false;}
            if ($con[0]['mc_welcome']=='true') {$con[0]['mc_welcome']=true;} else {$con[0]['mc_welcome']=false;}

            if ($con[0]['mc_list'] && isset($mc_add) && function_exists('mailchimp_fc'))
            {
              mailchimp_fc($mc_add, $custom_var, $con[0]['mc_list'], $con[0]['mc_double'], $con[0]['mc_welcome']);        
            }

            if ($con[0]['aw_list'] && isset($aw_add) && function_exists('aweber_fc'))
            {
              aweber_fc($aw_add, $custom_var, $con[0]['aw_list']);        
            }

            if ($con[0]['campaign_list'] && isset($campaign_add) && function_exists('campaign_fc'))
            {
              campaign_fc($campaign_add, $custom_var, $con[0]['campaign_list']);        
            }

          }




      // Make the Email
          $label_style = "padding: 4px 8px 4px 0px; margin: 0; width: 180px; font-size: 13px; font-weight: bold";
          $value_style = "padding: 4px 8px 4px 0px; margin: 0; font-size: 13px";
          $divider_style = "padding: 10px 8px 4px 0px; margin: 0; font-size: 16px; font-weight: bold; border-bottom: 1px solid #ddd";

          $i=0;
          $att=1;

          $email_body = '';

          while ($i<$nos)
          {
            if ($new[$i]['label']!='files')
            {
              $new[$i]['label'] = urldecode($new[$i]['label']);
              $new[$i]['value'] = $new[$i]['value'];                 
            }

            if ( !(empty($new[$i]['type'])) && !($new[$i]['type']=='captcha') && !($new[$i]['type']=='hidden') && !($new[$i]['label']=='files') && !($new[$i]['label']=='divider') && !($new[$i]['type']=='radio') && !($new[$i]['type']=='check')  && !($new[$i]['type']=='smiley') && !($new[$i]['type']=='stars') && !($new[$i]['type']=='matrix') )
            {
              $email_body .= "<tr><td style='$label_style'> ".$new[$i]['label']."</td><td style='$value_style'>".$new[$i]['value']."</td></tr>";
            }
            else if ( $new[$i]['label']=='files' )
            {
              $email_body .= "<tr><td style='$label_style'>Attachment($att)</td><td style='$value_style'><a href='".$new[$i]['value']."'>".$new[$i]['value']."</a></td></tr>";
              $att++;
            }
            else if ( $new[$i]['label']=='divider' )
            {
              $email_body .= "</table><table style='border: 0px; color: #333; width: 100%'><tr><td style='$divider_style'>".$new[$i]['value']."</td></tr></table><table>";
            }
            else if ( $new[$i]['type']=='hidden' && $new[$i]['label']=='location' )
            {
              $email_body .= "<tr><td style='color: #999; padding-bottom: 10px'> ".$new[$i]['value']."</td><td></td></tr>";
            }
            else if (  $new[$i]['type']=='radio' || $new[$i]['type']=='check' || $new[$i]['type']=='smiley' || $new[$i]['type']=='stars' || $new[$i]['type']=='matrix' )
            {
              if ( $new[$i]['value']==true )
              {
                $email_body .= "<tr><td style='$label_style'>".$new[$i]['label']."</td><td style='$value_style'> ".$new[$i]['value']."</td></tr>";
              }
            }

            $i++;
          }

          $email_body = "<h1 style='margin-bottom: 20px; border-bottom: 1px solid #eee; color: #666'>".$title."</h1><table style='border: 0px; color: #333; width: 100%'>".$email_body."</table>";


        $pattern = '/\[.*?\]/';
        preg_match_all($pattern, $con['0']['autoreply'], $matches);
        foreach ($new as $field)
        {

            foreach ($matches[0] as $match)
            {

                $match2 = str_replace('[','',$match);
                $match2 = str_replace(']','',$match2);
                if ($field['label']==$match2)
                {
                    $con['0']['autoreply'] = str_replace($match, $field['value'], $con['0']['autoreply']);
                }

            }

        }


        $pattern = '/\[.*?\]/';
        preg_match_all($pattern, $con['0']['autoreply_s'], $matches);
        foreach ($new as $field)
        {

            foreach ($matches[0] as $match)
            {

                $match2 = str_replace('[','',$match);
                $match2 = str_replace(']','',$match2);
                if ($field['label']==$match2)
                {
                    $con['0']['autoreply_s'] = str_replace($match, $field['value'], $con['0']['autoreply_s']);
                }
            }
        }


          if ($con[0]['mail_type']=='smtp')
          {


            require_once("php/class.phpmailer.php");



            foreach($rec as $send_to)
            {

              $to = $send_to['val'];

              $mail = new PHPMailer();

              $mail->IsSMTP();
              $mail->Host = $con[0]['smtp_host'];
              $mail->CharSet = 'UTF-8';
              $mail->SMTPAuth = true;
              $mail->Username = $con[0]['smtp_email'];

              $mail->Password = $con[0]['smtp_pass'];
              $mail->FromName = $con[0]['smtp_name'];
              $mail->AddAddress($to);
              if ($con[0]['if_ssl']=='ssl')
              {
                $mail->SMTPSecure = 'ssl';
                $mail->Port = 465;
              }

              if ($replyto)
              {
                $mail->AddReplyTo($replyto);
              }
              else
              {
                $mail->AddReplyTo($con[0]['smtp_email']);
              }


              $mail->From = $con[0]['smtp_email'];
              $mail->IsHTML(true);

              if (isset($con[0]['email_sub']))
              {
                if (strpos($con[0]['email_sub'], '{{form_name}}'))
                {
                  $con[0]['email_sub'] = explode("{{form_name}}", $con[0]['email_sub']);
                  $subject = $con[0]['email_sub'][0].$title.$con[0]['email_sub'][1];
                }
                else
                {
                  $subject = $con[0]['email_sub'];
                }
              }
              else
              {
                $subject = "New Submission for '".$title."'";
              }

              $mail->Subject = $subject;
              $mail->Body = $email_body;

              if($mail->Send())
              {
                $success_sent++;
                if ($autoreply)
                {
                  foreach ($autoreply as $ar_to)
                  {
                    if (!(empty($ar_to)))
                    {

                      $mail->Subject = $con['0']['autoreply_s'];
                      $mail->Body = "<div style='white-space: pre-line'>".$con['0']['autoreply']."</div>";
                      $mail->ClearAddresses();
                      $mail->AddAddress($ar_to);
                      $mail->AddReplyTo($con[0]['smtp_email']);
                      $mail->Send(); 

                    }                   
                  }         
                }

              }

            }

          }

          else
          {


            if ($replyto)
            {
              $headers = "From: $sender_name <$sender_email>\r\nReply-To: $replyto\r\n";
            }
            else
            {
              $headers = "From: $sender_name <$sender_email>\r\nReply-To: $sender_email\r\n";
            }

            $headers.= 'MIME-Version: 1.0' . "\r\n";
            $headers.= 'Content-type: text/html; charset=utf-8' . "\r\n";
            $headers.= 'X-Mailer: php' . "\r\n" . "\r\n";

            if (isset($con[0]['email_sub']))
            {
              if (strpos($con[0]['email_sub'], '{{form_name}}'))
              {
                $con[0]['email_sub'] = explode("{{form_name}}", $con[0]['email_sub']);
                $subject = $con[0]['email_sub'][0].$title.$con[0]['email_sub'][1];
              }
              else
              {
                $subject = $con[0]['email_sub'];
              }
            }
            else
            {
              $subject = "New Submission for '".$title."'";
            }


            foreach($rec as $send_to)
            {
              $to = $send_to['val'];
              if (mail($to,$subject,$email_body,$headers))
              {
                $success_sent++;
              }
            }

            if ($autoreply)
            {
              foreach ($autoreply as $ar_to)
              {
                if (!(empty($ar_to)))
                {
                  $headers = "From: $sender_name <$sender_email>\r\n".'Reply-To:'.$sender_email."\r\n";
                  $headers .= 'MIME-Version: 1.0' . "\r\n";
                  $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";

                  $subject = $con['0']['autoreply_s'];
                  $message = "<div style='white-space: pre-line'>".$con['0']['autoreply']."</div>";
                  $to = $ar_to;
                  mail($to,$subject,$message,$headers);
                }
              }
            }


          }

          $new_json = json_encode($new);

          $date = date('d M Y (H:i)');
          $date2 = date('Y-m-d');

          $temp1 = ORM::for_table('info_table')->where('id', $id.$date2)->find_one();

          if ($temp1>=1)
          {
            $query = ORM::for_table('info_table')->where('id', $id.$date2)->find_one();
            $query->submissions = $query->submissions + 1;
            $query->save();
          }
          else
          {
            $query = ORM::for_table('info_table')->create();
            $query->id = $id.$date2; $query->form = $id; $query->time = $date2; $query->views = 0; $query->submissions = 1;
            $query->save();
          }

          $query = ORM::for_table('submissions')->create();
          $query->content = $new_json; $query->seen = null; $query->added = $date; $query->form_id = $id;
          $result_v = $query->save();


          if ($result_v)
          {

            $result['done'] = true;
            $temp1 = ORM::for_table('builder')->find_one($id);
            $temp1->submits = $temp1->submits + 1; $temp1->save();

          }


          /* Display Success Message if Form Submission Updated in DataBase */
          if($result)
          {
            $error['sent']="true";
            $error['msg']="Message Sent";

            if ( (isset($con[0]['redirect'])) && !(empty($con[0]['redirect'])) )
            {
              $error['redirect']=$con[0]['redirect'];
            }

            if (isset($con[0]['success_msg']))
            {
              $error['msg']=$con[0]['success_msg'];
            }

            echo json_encode($error);
          }
          else
          {
            $error['sent']="false";  
            $error['msg']="The message could not be sent";

            if (isset($con[0]['failed_msg']))
            {
              $error['msg']=$con[0]['failed_msg'];
            }

            echo json_encode($error);

          }

        }
        die();
      }

      function formcraft_email($value, $valid, $req, $min, $max, $tool, $con)
      {
        global $errors;
        $a=0;

        if ( (!(empty($value))) && !(filter_var($value, FILTER_VALIDATE_EMAIL)) )
        {
          if (isset($con['error_email']))
          {
            $errors[$tool][$a] = $con['error_email'];
          }
          else
          {
            $errors[$tool][$a] = 'Incorrect email format.';
          }
          $a++;
        }

      }
      function formcraft_url($value, $valid, $req, $min, $max, $tool, $con)
      {
        global $errors;
        $a=0;

        if ( (!(empty($value))) && !(filter_var($value, FILTER_VALIDATE_URL)) )
        {

          if (isset($con['error_url']))
          {
            $errors[$tool][$a] = $con['error_url'];
          }
          else
          {
            $errors[$tool][$a] = 'Incorrect URL format.';
          }
          $a++;
        }

      }
      function formcraft_captcha($value, $valid, $req, $min, $max, $tool, $con)
      {
        global $errors;
        global $id;
        $a=0;


        if (isset($_SESSION["security_number_$id"]))
        {
          if ( !(strtolower($_SESSION["security_number_$id"])==strtolower($value)) )
          {

            if (isset($con['error_captcha']))
            {
              $errors[$tool][$a] = $con['error_captcha'];
            }
            else
            {
              $errors[$tool][$a] = "Incorrect Captcha";
            }
            $a++;
          }
        }
        else
        {

          if ( !(strtolower($_SESSION["security_number"])==strtolower($value)) )
          {

            if (isset($con["error_captcha"]))
            {
              $errors[$tool][$a] = $con["error_captcha"];
            }
            else
            {
              $errors[$tool][$a] = "Incorrect Captcha";
            }
            $a++;
          }

        }


      }
      function formcraft_integers($value, $valid, $req, $min, $max, $tool, $con)
      {
        global $errors;
        $a=0;



        if ( (!(empty($value))) && !(is_numeric($value)) )
        {
          if (isset($con['error_only_integers']))
          {
            $errors[$tool][$a] = $con['error_only_integers'];
          }
          else
          {
            $errors[$tool][$a] = 'Only integers allowed';
          }
          $a++;
        }

      }

      function formcraft_no_val($value, $req, $min, $max, $tool, $con)
      {
        global $errors;
        $a=0;

        if ( ( $req==1 || $req=='true' ) && empty($value) && $value!='0' )
        {
          if (isset($con['error_required']))
          {
            $errors[$tool][$a] = $con['error_required'];
          }
          else
          {
            $errors[$tool][$a] = 'This field is required';
          }
          $a++;
        }
        if ( (!(empty($min))) && (!(empty($value))) && (strlen($value)<$min) )
        {
          if (isset($con['error_min']))
          {
            if (strpbrk($con['error_min'],'{{min_chars}}'))
            {
              $con['error_min'] = explode("{{min_chars}}", $con['error_min'] );
              $errors[$tool][$a] = $con['error_min'][0].$min.$con['error_min'][1];
            }
            else
            {
              $errors[$tool][$a] = $con['error_min'];
            }
          }
          else
          {
            $errors[$tool][$a] = 'At least '.$min.' characers required';
          }
          $a++;
        }
        if ( (!(empty($max))) && (!(empty($value))) && (strlen($value)>$max) )
        {
          if (isset($con['error_max']))
          {
            if (strpbrk($con['error_max'],'{{max_chars}}'))
            {
              $con['error_max'] = explode("{{max_chars}}", $con['error_max'] );
              $errors[$tool][$a] = $con['error_max'][0].$max.$con['error_max'][1];
            }
            else
            {
              $errors[$tool][$a] = $con['error_max'];
            }
          }
          else
          {
            $errors[$tool][$a] = 'At most '.$max.' characers allowed';
          }
          $a++;
        }

      }

      function formcraft_alphabets($value, $valid, $req, $min, $max, $tool, $con)
      {
        global $errors;
        $a=0;

        if ( (!(empty($value))) && !(ctype_alpha($value)) )
        {

          $errors[$tool][$a] = 'Only alphabets allowed';
          $a++;
        }

      }

      function formcraft_alpha($value, $valid, $req, $min, $max, $tool, $con)
      {
        global $errors;
        $a=0;

        if ( (!(empty($value))) && !(ctype_alnum($value)) )
        {

          $errors[$tool][$a] = 'Only alphabets and numbers allowed';
          $a++;
        }

      }

      function formcraft_update() 
      {

        $form = ORM::for_table('builder')->find_one($_POST['id']);


        if (get_magic_quotes_gpc())
        {
          $form->build = $_POST['build'];
          $form->options = $_POST['option'];
          $form->con = $_POST['con'];
          $form->html = $_POST['content'];
          $form->recipients = $_POST['rec'];
        }
        else
        {
          $form->build = addslashes($_POST['build']);
          $form->options = addslashes($_POST['option']);
          $form->con = addslashes($_POST['con']);
          $form->html = addslashes($_POST['content']);
          $form->recipients = addslashes($_POST['rec']);
        }

        $result = $form->save();
        return $result ? 'true' : 'false';
      }


      function formcraft_add() 
      {
        global $db_name, $path;

        if (get_magic_quotes_gpc())
        {
        $_POST['name'] = $_POST['name'];
        $_POST['desc'] = $_POST['desc'];
        }
        else
        {
        $_POST['name'] = addslashes($_POST['name']);
        $_POST['desc'] = addslashes($_POST['desc']);
        }

        if (empty($_POST['name']))
        {
          $output['Error'] = 'Name is required';
          echo json_encode($output);
          die();
        }
        if (strlen($_POST['name'])<2)
        {
          $output['Error'] = 'Name is too short';
          echo json_encode($output);
          die();
        }
        if (strlen($_POST['name'])>90)
        {
          $output['Error'] = 'Name is too long';
          echo json_encode($output);
          die();
        }
        if (strlen($_POST['desc'])>500)
        {
          $output['Error'] = 'Description is too long';
          echo json_encode($output);
          die();
        }
        if ( (!(empty($_POST['desc']))) && strlen($_POST['desc'])<3)
        {
          $output['Error'] = 'Description is too short';
          echo json_encode($output);
          die();
        }

        $dt = date('d M Y (H:i)');

        $db = ORM::get_db();

        if ($_POST['type_form']=='duplicate')
        {
          $dup = $_POST['duplicate'];

          $old = ORM::for_table('builder')->find_one($dup);

          $new = ORM::for_table('builder')->create();
          $new->name = $_POST['name'];
          $new->description = $_POST['desc'];
          $new->html = $old->html;
          $new->build = $old->build;
          $new->options = $old->options;
          $new->con = $old->con;
          $new->recipients = $old->recipients;
          $new->added = $dt;
          $result = $new->save();

        }
        else if ($_POST['type_form']=='import')
        {

          if (empty($_POST['import_form']))
          {
            $result2['Error'] = 'Upload a form to be imported';
            echo json_encode($result2);
            die();
          }


          $data = file_get_contents('file-upload/server/php/files/'.$_POST['import_form']);

          $data = json_decode($data, 1);

          $data = str_replace($data['dir'].'/formcraft', $path, $data);

          if (empty($data))
          {
            $result2['Error'] = 'Import failed';
            echo json_encode($result2);
            unlink('file-upload/server/php/files/'.$_POST['import_form']);
            die();
          }


          $new = ORM::for_table('builder')->create();
          $new->name = $_POST['name'];
          $new->description = $_POST['desc'];
          $new->html = $data['html'];
          $new->build = $data['build'];
          $new->options = $data['options'];
          $new->con = $data['con'];
          $new->recipients = $data['recipients'];
          $new->added = $dt;
          $result = $new->save();

          unlink('file-upload/server/php/files/'.$_POST['import_form']);

        }
        else
        {

          $new = ORM::for_table('builder')->create();
          $new->name = $_POST['name'];
          $new->description = $_POST['desc'];
          $new->views = 0;
          $result = $new->save();
        }

        if($result)
        {
          $output['Added']= $new->id;
          $output['Done']=true;
          echo json_encode($output);
        }

        die();
      }




      function formcraft_del() 
      {
        echo ORM::for_table('builder')->find_one($_POST['id'])->delete() ? 'Deleted' : 'Failed';
      }

      function formcrafts_register_scripts () 
      {

        global $path;

        ?>


        <link href='<?php echo $path; ?>/css/bootstrap.css' rel='stylesheet' type='text/css'>
        <link href='<?php echo $path; ?>/css/font-awesome/css/font-awesome.min.css' rel='stylesheet' type='text/css'>
        <link href='<?php echo $path; ?>/css/jquery-ui.css' rel='stylesheet' type='text/css'>
        <link href='<?php echo $path; ?>/time/css/bootstrap-timepicker.min.css' rel='stylesheet' type='text/css'>
        <link href='<?php echo $path; ?>/css/boxes.css' rel='stylesheet' type='text/css'>
        <link href='<?php echo $path; ?>/css/ratings.css' rel='stylesheet' type='text/css'>
        <link href='<?php echo $path; ?>/css/nform_style.css' rel='stylesheet' type='text/css'>


        <script src='<?php echo $path; ?>/js/jquery.min.js'></script>
        <script src='<?php echo $path; ?>/js/jquery-migrate.min.js'></script>

        <script src='<?php echo $path; ?>/js/jquery.ui.core.min.js'></script>
        <script src='<?php echo $path; ?>/js/jquery.ui.widget.min.js'></script>
        <script src='<?php echo $path; ?>/js/jquery.ui.mouse.min.js'></script>
        <script src='<?php echo $path; ?>/js/jquery.ui.slider.min.js'></script>

        <script src='<?php echo $path; ?>/js/form.js'></script>
        <script src='<?php echo $path; ?>/js/form_only.js'></script>
        <script src='<?php echo $path; ?>/file-upload/js/jquery.iframe-transport.js'></script>
        <script src='<?php echo $path; ?>/bootstrap/js/bootstrap.min.js'></script>
        <script src='<?php echo $path; ?>/libraries/js_libraries.js'></script>
        <script type="text/javascript">
          window.ajax = <?php echo "'$path/function.php'"; ?>;
          window.base = <?php echo "'$path'"; ?>;
        </script>


        <?php

      }



      function formcraft( $id, $type = '', $opened = '0', $text = 'Click Here', $class = '', $background = '#eee', $text_color = '#333', $preview = false )
      {

        global $db_name;

        formcrafts_register_scripts();
        $row = ORM::for_table('builder')->find_one($id);

        $temp_css = json_decode(stripslashes($row['con']), true);
        $css = $temp_css[0]['custom_css'];

        if ($type=='popup')
        {
          $temp= '
          <a href="#myModal'.$id.'" role="button" data-toggle="modal" id="'.$id.'_a" onClick="javascript:increment_form(this.id)" class="'.$class.' modal_trigger">'.$text.'</a>

          <!-- Modal -->
          <div id="myModal'.$id.'" class="modal hide" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none">
            <button type="button" class="close modal_close"    aria-hidden="true">×</button>
            <div>
              ';

              $temp.= stripslashes($row['html']);

              $temp.='
            </div>
          </div>';

          echo "<style>$css</style>".$temp;

        }
        elseif ($type=='sticky')
        {

          if ($opened)
          {
            $class = 'sticky_cover open';
          }
          else
          {
            $class = 'sticky_cover';
          }


          $temp = "

          <div id='nform_sticky' class='".$class." bootstrap'>

            <span id='".$id."_a' onClick='javascript:increment_form(this.id)' class='sticky_toggle' style='background-color: $background; color: $text_color'>".$text." <i class='icon-angle-up'></i></span>


            <div class='sticky_nform'>";

              $temp.= stripslashes($row['html']);

              $temp.= "
            </div>
          </div>";

          echo "<style>$css</style>".$temp;

        }
        else if ($type=='fly')
        {

          if ($opened)
          {
            $class = 'fly_cover open';
          }
          else
          {
            $class = 'fly_cover';
          }

          $temp = "
          <div id='nform_fly' class='".$class." bootstrap'>
            <span id='".$id."_a' title='Open/Close' onClick='javascript:increment_form(this.id)' class='fly_toggle' style='background-color: $background; color: $text_color'>".$text."</span>
            <div class='fly_form'>
              <span id='".$id."_a' class='close modal_close' >×</span>
              ";

              $temp.= stripslashes($row['html']);

              $temp.= "
            </div>
          </div>";

          echo "<style>$css</style>".$temp;

        }
        else 
        {
          if ($preview==false)
          {
            formcraft_increment($id);
          }

          echo "<style>$css</style>".stripslashes($row['html']);
        }

      }



      ?>