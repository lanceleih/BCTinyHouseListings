<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Simple email class
 *
 * @package    OC
 * @category   Core
 * @author     Chema <chema@open-classifieds.com>, Slobodan <slobodan@open-classifieds.com>
 * @copyright  (c) 2009-2013 Open Classifieds Team
 * @license    GPL v3
 */


class Email {

    /**
     * sends an email using our configs
     * @param  string/array $to       array(array('name'=>'chema','email'=>'chema@'),)
     * @param  [type] $to_name   [description]
     * @param  [type] $subject   [description]
     * @param  [type] $body      [description]
     * @param  [type] $reply     [description]
     * @param  [type] $replyName [description]
     * @param  [type] $file      [description]
     * @return boolean
     */
    public static function send($to,$to_name='',$subject,$body,$reply,$replyName,$file = NULL,$content = NULL)
    {
        $result = FALSE;

        //multiple to but theres none...
        if (is_array($to) AND count($to)==0)
            return FALSE;
        
        $body = Text::nl2br($body);

        //get the unsubscribe link
        
        $email_encoded = NULL;
        //is sent to a single user get hash to auto unsubscribe
        if (!is_array($to) OR count($to)==1)
        {
            //from newsletter sent
            if (isset($to[0]['email']))
                $email_encoded = $to[0]['email'];
            else
                $email_encoded = $to;

            //encodig the email for extra security
            $encrypt = new Encrypt(Core::config('auth.hash_key'), MCRYPT_MODE_NOFB, MCRYPT_RIJNDAEL_128);
            $email_encoded = Base64::fix_to_url($encrypt->encode($email_encoded));
        }

        $unsubscribe_link = Route::url('oc-panel',array('controller'=>'auth','action'=>'unsubscribe','id'=>$email_encoded));

        //get the template from the html email boilerplate
        $body_original = $body;
        $body = View::factory('email',array('title'=>$subject,'content'=>$body,'unsubscribe_link'=>$unsubscribe_link))->render();

        switch (core::config('email.service')) {
            case 'elasticemail':
                $result =  ElasticEmail::send($to,$to_name, $subject, $body, $reply, $replyName);
                break;
            case 'mailgun':
                //todo

                break;
            case 'pepipost':
                //todo

                break;
            case 'smtp':
            case 'mail':
            default:
                $result = self::phpmailer($to,$to_name,$subject,$body,$reply,$replyName,$file);
                break;
        }

        // notify user (pusher)
        if (Core::config('general.pusher_notifications')){
            if (is_array($to)){
                foreach ($to as $user_email) {
                    Model_User::pusher($user_email['email'], Text::limit_chars(Text::removebbcode($body), 80, NULL, TRUE),$content);
                }
            } else 
                Model_User::pusher($to, Text::limit_chars(Text::removebbcode($body), 80, NULL, TRUE),$content);
        }

        return $result;
    }

    /**
     * sends an email using content from model_content
     * @param  string $to        
     * @param  string $to_name   
     * @param  string $from      
     * @param  string $from_name 
     * @param  string $content   seotitle from Model_Content
     * @param  array $replace   key value to replace at subject and body
     * @param  array $file      file to attach to email
     * @return boolean            s
     */
    public static function content($to, $to_name='', $from = NULL, $from_name =NULL, $content, $replace, $file=NULL)
    {
        
        $email = Model_Content::get_by_title($content,'email');

        //content found
        if ($email->loaded())
        { 
            if ($replace===NULL) 
                $replace = array();

            if ($from === NULL)
                $from = $email->from_email;

            if ($from_name === NULL )
                $from_name = core::config('general.site_name');

            if (isset($file) AND self::is_valid_file($file))
                $file_upload = $file;
            else
                $file_upload = NULL;

            //adding extra replaces
            $replace+= array('[SITE.NAME]'      =>  core::config('general.site_name'),
                             '[SITE.URL]'       =>  core::config('general.base_url'),
                             '[USER.NAME]'      =>  $to_name);

            if(!is_array($to))
                $replace += array('[USER.EMAIL]'=>$to);

            //adding anchor tags to any [URL.* match
            foreach ($replace as $key => $value) 
            {
                if(strpos($key, '[URL.')===0 OR $key == '[SITE.URL]'  AND $value!='')
                    $replace[$key] = '<a href="'.$value.'">'.parse_url($value, PHP_URL_HOST).'</a>';
            }

            $subject = str_replace(array_keys($replace), array_values($replace), $email->title);
            $body    = str_replace(array_keys($replace), array_values($replace), $email->description);

            return Email::send($to,$to_name,$subject,$body,$from,$from_name, $file_upload,$content); 
        }
        else 
            return FALSE;

    }

    /**
     * returns true if file is of valid type.
     * Its used to check file sent to user from advert usercontact
     * @param array file
     * @return BOOL 
     */
    public static function is_valid_file($file)
    {
        //catch file
        $file = $_FILES['file'];
        //validate file
        if( $file !== NULL)
        {     
            if ( 
                ! Upload::valid($file) OR
                ! Upload::not_empty($file) OR
                ! Upload::type($file, array('jpg', 'jpeg', 'png', 'pdf','doc','docx')) OR
                ! Upload::size($file,'3M'))
                {
                    return FALSE;
                }
            return TRUE;
        }
    }

    /**
     * returns an array of administrators and moderators
     * @return array
     */
    public static function get_notification_emails()
    {
        $arr = array();

        $users = new Model_User();
        $users = $users->where('id_role','in',array(Model_Role::ROLE_ADMIN,Model_Role::ROLE_MODERATOR))
                ->where('status','=',Model_User::STATUS_ACTIVE)
                ->where('subscriber','=',1)
                ->cached()->find_all();

        foreach ($users as $user) 
        {
            $arr[] = array('name'=>$user->name,'email'=>$user->email);
        }

        return $arr;
    }

    /**
     * returns the spam score of a raw email using api from postmarkapp
     * @param  string $raw_email entire RAW email with headers etc....
     * @return numeric/false            spam score or FALSE is something went wrong....
     */
    public static function get_spam_score($raw_email)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, 'http://spamcheck.postmarkapp.com/filter');
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array('email' => $raw_email,'options'=>'short')));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);

        $response = curl_exec($ch);

        //something went wrong with the request
        if(empty($response) || curl_error($ch) || curl_getinfo($ch, CURLINFO_HTTP_CODE) !== 200){
            curl_close($ch);
            return FALSE;
        }

        curl_close($ch);

        $score = json_decode($response);

        if ($score->success == TRUE AND is_numeric($score->score))
            return $score->score;
        else
            return FALSE;
    }
  
    public static function phpmailer($to,$to_name='',$subject,$body,$reply,$replyName,$file = NULL)
    {
        require_once Kohana::find_file('vendor', 'php-mailer/phpmailer','php');
            
        $mail= new PHPMailer();
        $mail->CharSet = Kohana::$charset;

        if(core::config('email.service') == 'smtp')
        { 
            require_once Kohana::find_file('vendor', 'php-mailer/smtp','php');
            
            $mail->IsSMTP();
            $mail->Timeout = 5;

            //SMTP HOST config
            if (core::config('email.smtp_host')!="")
                $mail->Host       = core::config('email.smtp_host');              // sets custom SMTP server
            

            //SMTP PORT config
            if (core::config('email.smtp_port')!="")
                $mail->Port       = core::config('email.smtp_port');              // set a custom SMTP port
            

            //SMTP AUTH config
            if (core::config('email.smtp_auth') == TRUE)
            {
                $mail->SMTPAuth   = TRUE;                                                  // enable SMTP authentication
                $mail->Username   = core::config('email.smtp_user');              // SMTP username
                $mail->Password   = core::config('email.smtp_pass');              // SMTP password                        
            }

            // sets the prefix to the server
            $mail->SMTPSecure = core::config('email.smtp_secure');                  
                
        }

        $mail->From       = core::config('email.notify_email');
        $mail->FromName   = core::config('email.notify_name');
        $mail->Subject    = $subject;
        $mail->MsgHTML($body);

        if($file !== NULL) 
            $mail->AddAttachment($file['tmp_name'],$file['name']);

        $mail->AddReplyTo($reply,$replyName);//they answer here

        if (is_array($to))
        {
            foreach ($to as $contact) 
                $mail->AddBCC($contact['email'],$contact['name']);               
        }
        else
            $mail->AddAddress($to,$to_name);

        $mail->IsHTML(TRUE); // send as HTML

        //to multiple destinataries, check spam score
        if (is_array($to))
        {
            $mail->preSend();
            $spam_score = Email::get_spam_score($mail->getSentMIMEMessage());
            if ($spam_score >= 5 OR $spam_score === FALSE)
            {
                Alert::set(Alert::ALERT,"Please review your email. Got a Spam Score of " . $spam_score);
                return $spam_score;
            }
        }

        try {
            $result = $mail->Send();
        } catch (Exception $e) {
            $result = FALSE;
            $mail->ErrorInfo = $e->getMessage();
        }

        if(!$result) 
        {//to see if we return a message or a value bolean
            Alert::set(Alert::ALERT,"Mailer Error: " . $mail->ErrorInfo);
            return FALSE;
        } 
        else 
            return TRUE;
    }


} //end email