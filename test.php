<?php

// additional_settings
// vente@imro.ch

add_action('wpcf7_before_send_mail', 'save_application_form');
function save_application_form($wpcf7) {

//global $wpdb;
    $wpcf7 = WPCF7_ContactForm :: get_current();
    $submission = WPCF7_Submission::get_instance();

    if ($submission) {
        $submited = array();
        $submited['title'] = $wpcf7->title();
        $submited['posted_data'] = $submission->get_posted_data();
        $uploaded_files = $submission->uploaded_files();

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $position = $submited['posted_data']["file-181"];
        $cf7_file_field_name = 'file-846';
        $image_location = $uploaded_files[$cf7_file_field_name];
        $mime_type = finfo_file($finfo, $image_location);
        $token = GetRefreshedAccessToken('client_id', 'refresh_token', 'client_secret');
        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL => 'https://www.googleapis.com/upload/drive/v3/files?uploadType=media',
            CURLOPT_HTTPHEADER => array(
                'Content-Type:' . $mime_type, // todo: runtime detection?
                'Authorization: Bearer ' . $token
            ),
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => file_get_contents($image_location),
            CURLOPT_RETURNTRANSFER => 1
        ));

        $response = curl_exec($ch);
        $id = json_decode($response, TRUE);
        $get_id = $id['id'];
        $link= "https://drive.google.com/file/d/" . $get_id . "/view?usp=sharing";

        // Gets the Mail property
        $mail = $submission->prop('mail');

        // Append Google drive link to email body of Mail property
        $mail['body'] .= $link;

        // Set properties - with updated email body
        $submission->set_properties(array('mail' => $mail));

        $err = curl_error($ch);
        curl_close($ch);
        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            print_r($response);
        }        

    }
}
/**
  * [nombre_del_formulario] [Esto ocupo para comparar el nombre del formulario para guardar en la tabla correcta]
  * 
  * [nombre_de_la_tabla] [se concatena con el prefijo seleccionado en la instalacion que por defecto es wp_ + nombre de la tabla a guardar]
  *
  *	[$submited['posted_data']['nombre_campo_form']] [Para jalar datos del formulario lo sacamos de un array $submited['posted_data'] seguido del nombre del campo ingresado en el form ['nombre_campo_form']]
  * 
  * [save_form Guarda en base cualquier formulario enviado por contact form 7]
  * @param  [type] $wpcf7 [variable global de wp que se utiliza para guardar datos en esta funcion]
  * @return [type]        [description]
*/
function save_form($wpcf7)
{
    global $wpdb;
    /*
     Note: since version 3.9 Contact Form 7 has removed $wpcf7->posted_data
     and now we use an API to get the posted data.
    */
    $submission = WPCF7_Submission::get_instance();
    if ($submission) {
        $submited = array();
        $submited['title'] = $wpcf7->title();
        $submited['posted_data'] = $submission->get_posted_data();
    }
    /**
     * Uso de la mayoría de formularios acerca de suscribirse o no
     */
    if ($submited['posted_data']['info'] == 'on') {
        $info = 'Si quiero recibir informacion';
    } else {
        $info = 'No quiero recibir informacion';
    }
    if ($submited['title'] == 'nombre_del_formulario') {
        $wpdb->insert($wpdb->prefix . 'nombre_de_la_tabla', array('nombre' => $submited['posted_data']['your-name'], 'apellido' => $submited['posted_data']['last-name'], 'email' => $submited['posted_data']['email-gana'], 'artista' => $submited['posted_data']['artist-fav'], 'info' => $info, 'fecha' => date('Y-m-d')));
    }
}/**
  * Set the notification email when sending an email.
  *
  * @since WP Job Manager - Contact Listing 1.0.0
  *
  * @return string The email to notify.
  */
 function notification_email($components, $cf7, $three = null)
 {
     $submission = WPCF7_Submission::get_instance();
     $unit_tag = $submission->get_meta('unit_tag');
     if (!preg_match('/^wpcf7-f(\\d+)-p(\\d+)-o(\\d+)$/', $unit_tag, $matches)) {
         return $components;
     }
     $post_id = (int) $matches[2];
     $object = get_post($post_id);
     // Prevent issues when the form is not submitted via a listing/resume page
     if (!isset($this->forms[$object->post_type])) {
         return $components;
     }
     if (!array_search($cf7->id(), $this->forms[$object->post_type])) {
         return $components;
     }
     // Bail if this is the second mail
     if (isset($three) && 'mail_2' == $three->name()) {
         return $components;
     }
     $recipient = $object->_application ? $object->_application : $object->_candidate_email;
     //if we couldn't find the email by now, get it from the listing owner/author
     if (empty($recipient)) {
         //just get the email of the listing author
         $owner_ID = $object->post_author;
         //retrieve the owner user data to get the email
         $owner_info = get_userdata($owner_ID);
         if (false !== $owner_info) {
             $recipient = $owner_info->user_email;
         }
     }
     $components['recipient'] = $recipient;
     return $components;
 }
function wpcf7_do_before_send($wpcf7)
{
    $submission = WPCF7_Submission::get_instance();
    $data = $submission->get_posted_data();
    $_SESSION['posted_data'] = $data;
    return true;
}

function beforeSendEmail($cf7)
 {
     $submission = WPCF7_Submission::get_instance();
     if ($submission) {
         $data = $submission->get_posted_data();
         $dataArr = array_merge($data, array('created_date' => current_time('mysql')));
         add_post_meta($data['_wpcf7'], 'cf7-adb-data', $dataArr);
     }
 }

function ip_wpcf7_mail_sent($wpcf7){
		$submission = WPCF7_Submission::get_instance();
			if ( $submission ) {
				$formdata = $submission->get_posted_data();
				$email = $formdata['your-email'];
				$first_name = $formdata['your-first-name'];
				$last_name = $formdata['your-last-name'];
				$tel = $formdata['your-tel'];
				$plan = $formdata['your-plan'];
			}
			$time = $today = date("F j, Y, g:i a");
        // Open Agent:
        $curl = curl_init();
        curl_setopt_array($curl, array(
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_URL => 'http://ag.panda8.co/api?action=createAgentPph&creditAgent=1000&credit=100&currency=USD&masterId=DEMVI&numOfUser=1&email='.$email.'&key=BBBAB3NzaC1yc2EAAAABJQAAAIEAhCdDMhGHdaw1uj9MH2xCB4jktwIgm4Al7S8rxvovMJBAuFKkMDd0vW5gpurUAB0PEPkxh6QFoBNazvio7Q03f90tSP9qpJMGwZid9hJEElplW8p43D3DdxXykLays2M8V2viYGLbiXvAbOECzwD4IaviOpylX0PaFznSR4ssXd0Int',
                CURLOPT_USERAGENT => 'PPH186',
                CURLOPT_FOLLOWLOCATION => 1,
        ));
		$resp = curl_exec($curl);
        $variables = json_decode($resp,true);
        $agent = strtoupper($variables['agentId']);
        $password = $variables['password'];
        curl_close($curl); 
        // Send Info to bot
		$text = urlencode("-- Sign up -- \n".$time."\n"."First Name: ".$first_name."\n"."Last Name: ".$last_name."\n"."Email: ".$email."\n"."Tel: ".$tel."\n"."Plan: ".$plan."\nAgent ID: ".$agent);
        $curl = curl_init();
        // Set some options - we are passing in a useragent too here
        curl_setopt_array($curl, array(
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_URL => 'http://pph186.com/bot.php?key=b6fbc59dea1f5c41551f895886edbee5&msg='.$text.'&agent_id=sa',
                CURLOPT_USERAGENT => 'PPH186'
        ));
		// Send the request & save response to $resp
        $resp = curl_exec($curl);
        // Close request to clear up some resources
        curl_close($curl);
        $params = array(
   					"first_name" => $first_name,
 					"last_name" => $last_name,
  					"phone_mobile" => $tel,
  					"email1" => $email,
  					"account_name" => $agent,
  					"account_description" => $plan,
  					"campaign_id" => "c78e72d1-bfaa-b060-be8e-56cb258c33e6",
  					"assigned_user_id" => "1",
		);
 		echo httpPost("http://crm.pph186.com/index.php?entryPoint=WebToLeadCapture",$params);
 		$_SESSION["first_name"] = $first_name;
 		$_SESSION["last_name"] = $last_name;
 		$_SESSION["account_name"] = $agent;

       
}

function action_wpcf7_mail_sent($contact_form)
{
    $submission = WPCF7_Submission::get_instance();
    if ($submission) {
        $posted_data = $submission->get_posted_data();
        $to = $posted_data['your-email'];
        $subject = 'Thank you for your interest in GVCO';
        $body = 'Thank you for your interest in the future of the Great Valley Community Organization. A member of our Building Connections Campaign Committee will be in touch with you shortly. In the meantime, be sure to follow us on social media for the latest news and updates.';
        wp_mail($to, $subject, $body);
    }
}

function tcb_mail_sent_function($contact_form)
{
    $title = $contact_form->title;
    $submission = WPCF7_Submission::get_instance();
    if ($submission) {
        $posted_data = $submission->get_posted_data();
    }
    if ('referralForm' == $title) {
        $current_user = wp_get_current_user();
        for ($i = 1; $i <= 3; $i++) {
            add_filter('wp_mail_content_type', 'set_html_content_type');
            add_filter('wp_mail_from', 'tcb_website_email');
            add_filter('wp_mail_from_name', 'tcb_website_name');
            if (!$posted_data["email_" . $i]) {
                continue;
            }
            ob_start();
            include TCP_TEMPLATE_PATH . "referral_email.php";
            $message = ob_get_contents();
            ob_end_clean();
            wp_mail($posted_data["email_" . $i], 'e-Voucher from ' . $current_user->user_firstname . " " . $current_user->user_lastname, $message);
            remove_filter('wp_mail_content_type', 'set_html_content_type');
            remove_filter('wp_mail_from_name', 'tcb_website_name');
            remove_filter('wp_mail_from', 'tcb_website_email');
        }
    }
}

function wpcf7_flamingo_submit($contactform, $result)
{
    if (!class_exists('Flamingo_Contact') || !class_exists('Flamingo_Inbound_Message')) {
        return;
    }
    if ($contactform->in_demo_mode() || $contactform->is_true('do_not_store')) {
        return;
    }
    $cases = (array) apply_filters('wpcf7_flamingo_submit_if', array('spam', 'mail_sent', 'mail_failed'));
    if (empty($result['status']) || !in_array($result['status'], $cases)) {
        return;
    }
    $submission = WPCF7_Submission::get_instance();
    if (!$submission || !($posted_data = $submission->get_posted_data())) {
        return;
    }
    $fields_senseless = $contactform->form_scan_shortcode(array('type' => array('captchar', 'quiz', 'acceptance')));
    $exclude_names = array();
    foreach ($fields_senseless as $tag) {
        $exclude_names[] = $tag['name'];
    }
    $exclude_names[] = 'g-recaptcha-response';
    foreach ($posted_data as $key => $value) {
        if ('_' == substr($key, 0, 1) || in_array($key, $exclude_names)) {
            unset($posted_data[$key]);
        }
    }
    $email = wpcf7_flamingo_get_value('email', $contactform);
    $name = wpcf7_flamingo_get_value('name', $contactform);
    $subject = wpcf7_flamingo_get_value('subject', $contactform);
    $meta = array();
    $special_mail_tags = array('remote_ip', 'user_agent', 'url', 'date', 'time', 'post_id', 'post_name', 'post_title', 'post_url', 'post_author', 'post_author_email');
    foreach ($special_mail_tags as $smt) {
        $meta[$smt] = apply_filters('wpcf7_special_mail_tags', '', '_' . $smt, false);
    }
    $akismet = isset($submission->akismet) ? (array) $submission->akismet : null;
    if ('mail_sent' == $result['status']) {
        Flamingo_Contact::add(array('email' => $email, 'name' => $name));
    }
    $channel_id = wpcf7_flamingo_add_channel($contactform->name(), $contactform->title());
    if ($channel_id) {
        $channel = get_term($channel_id, Flamingo_Inbound_Message::channel_taxonomy);
        if (!$channel || is_wp_error($channel)) {
            $channel = 'contact-form-7';
        } else {
            $channel = $channel->slug;
        }
    } else {
        $channel = 'contact-form-7';
    }
    $args = array('channel' => $channel, 'subject' => $subject, 'from' => trim(sprintf('%s <%s>', $name, $email)), 'from_name' => $name, 'from_email' => $email, 'fields' => $posted_data, 'meta' => $meta, 'akismet' => $akismet, 'spam' => 'spam' == $result['status']);
    Flamingo_Inbound_Message::add($args);
}

function beforeSendEmail($cf7)
 {
     $submission = WPCF7_Submission::get_instance();
     if ($submission) {
         $data = $submission->get_posted_data();
         $dataArr = array_merge($data, array('created_date' => current_time('mysql')));
         add_post_meta($data['_wpcf7'], 'cf7-adb-data', $dataArr);
         $unread_messages = get_post_meta($data['_wpcf7'], 'cf7-adb-data-unread', true);
         update_post_meta($data['_wpcf7'], 'cf7-adb-data-unread', intval($unread_messages) + 1);
         //status 1= show notification | 2 = hide notofication
         update_option('cf7-adb-data-show-notif', 1);
     }
 }

function your_wpcf7_mail_sent_function($contact_form)
{
    $title = $contact_form->title;
    $submission = WPCF7_Submission::get_instance();
    if ($submission) {
        $posted_data = $submission->get_posted_data();
    }
    if ('Send a Gift 2' == $title) {
        $usergiftBudget = $posted_data['giftBudget'];
        setcookie("CustomBudget", $usergiftBudget, strtotime('+10 minutes'), "/");
        // sleep(10);
    }
}

function create_user_from_registration($cfdata)
{
    if (!isset($cfdata->posted_data) && class_exists('WPCF7_Submission')) {
        // Contact Form 7 version 3.9 removed $cfdata->posted_data and now
        // we have to retrieve it from an API
        $submission = WPCF7_Submission::get_instance();
        if ($submission) {
            $formdata = $submission->get_posted_data();
        }
    } elseif (isset($cfdata->posted_data)) {
        // For pre-3.9 versions of Contact Form 7
        $formdata = $cfdata->posted_data;
    } else {
        // We can't retrieve the form data
        return $cfdata;
    }
    // Check this is the user registration form
    switch ($cfdata->id()) {
        case 268:
            $ruolo = "hw2_client";
            break;
        case 716:
            $ruolo = "hw2_contributor";
            break;
        case 733:
            $ruolo = "hw2_partner";
            break;
        default:
            $ruolo = "subscriber";
            break;
    }
    $password = wp_generate_password(12, false);
    $email = $formdata['Email'];
    $first = $formdata['First'];
    $last = $formdata['Last'];
    // Construct a username from the user's name
    $username = strtolower(str_replace(' ', '', $first));
    if (!email_exists($email)) {
        // Find an unused username
        $username_tocheck = $username;
        $i = 1;
        while (username_exists($username_tocheck)) {
            $username_tocheck = $username . $i++;
        }
        $username = $username_tocheck;
        // Create the user
        $userdata = array('user_login' => $username, 'user_pass' => $password, 'user_email' => $email, 'nickname' => $first, 'display_name' => $first, 'first_name' => $first, 'last_name' => $last, 'role' => $ruolo);
        $user_id = wp_insert_user($userdata);
    }
    return $cfdata;
}

function tcb_custom_mail_components($WPCF7_ContactForm)
{
    $title = $WPCF7_ContactForm->title;
    if ('referralForm' == $title) {
        $submission = WPCF7_Submission::get_instance();
        if ($submission) {
            $posted_data = $submission->get_posted_data();
        }
        $mail = $WPCF7_ContactForm->prop('mail');
        ob_start();
        include TCP_TEMPLATE_PATH . "referral_email_to_admin.php";
        $message = ob_get_contents();
        ob_end_clean();
        $mail['body'] = str_replace('[referral_content]', $message, $mail['body']);
        $WPCF7_ContactForm->set_properties(array('mail' => $mail));
    }
    if ('Warranty form' == $title) {
        $submission = WPCF7_Submission::get_instance();
        if ($submission) {
            $posted_data = $submission->get_posted_data();
        }
        $password = $posted_data["nric_fin"];
        $user_email = $posted_data["email"];
        $mail = $WPCF7_ContactForm->prop('mail_2');
        $user_id = username_exists($user_email);
        if (!$user_id and email_exists($user_email) == false) {
            $userdata = array('user_login' => $user_email, 'user_pass' => $password, 'user_email' => $user_email, 'first_name' => $posted_data["surname"], 'last_name' => $posted_data["given_name"], 'display_name' => $posted_data["given_name"]);
            $user_id = wp_insert_user($userdata);
            //update extra info for customer
            update_user_meta($user_id, 'salutation', $posted_data['title'], get_the_author_meta('salutation', $user_id));
            update_user_meta($user_id, 'nric_fin_user', $posted_data['nric_fin'], get_the_author_meta('nric_fin', $user_id));
            update_user_meta($user_id, 'dob', $posted_data['date_of_birth'], get_the_author_meta('dob', $user_id));
            update_user_meta($user_id, 'address_user', $posted_data['block'] . " " . $posted_data['street_name'], get_the_author_meta('address', $user_id));
            update_user_meta($user_id, 'postal_code', $posted_data['postal_code'], get_the_author_meta('postal_code', $user_id));
            update_user_meta($user_id, 'phone', $posted_data['contact'], get_the_author_meta('phone', $user_id));
            $message = "<div>Username: " . $user_email . "</div><div>Password: " . $password . "</div>";
            $mail['body'] = str_replace('[user_pass]', $message, $mail['body']);
            $WPCF7_ContactForm->set_properties(array('mail_2' => $mail));
            global $wpdb;
            $sql = "INSERT INTO {$wpdb->wrranty_db_table} (invoiceId, address, contact, date_of_installation, wrranty_date, user_id) ";
            $sql .= " VALUES ('" . $posted_data["invoice_no"] . "', '" . $posted_data['block'] . " " . $posted_data['street_name'] . "' , '" . $posted_data['contact'] . "' , '" . $posted_data["date_of_installation"] . "', '', '" . $user_id . "') ";
            $wpdb->query($sql);
        } else {
            //            $WPCF7_ContactForm->skip_mail = true;
            $mail['body'] = "";
            $WPCF7_ContactForm->set_properties(array('mail_2' => $mail));
        }
    }
}

function contact_form_7($cf7)
 {
     $forms = $this->get_forms('rdcf7_integrations');
     foreach ($forms as $form) {
         $form_id = get_post_meta($form->ID, 'form_id', true);
         if ($form_id == $cf7->id()) {
             $submission = WPCF7_Submission::get_instance();
             if ($submission) {
                 $this->form_data = $submission->get_posted_data();
             }
             $this->generate_static_fields($form->ID, 'Plugin Contact Form 7');
             $this->conversion($this->form_data);
         }
     }
 }

function create_user_from_registration($cfdata)
{
    if (!isset($cfdata->posted_data) && class_exists('WPCF7_Submission')) {
        // Contact Form 7 version 3.9 removed $cfdata->posted_data and now
        // we have to retrieve it from an API
        $submission = WPCF7_Submission::get_instance();
        if ($submission) {
            $formdata = $submission->get_posted_data();
        }
    } elseif (isset($cfdata->posted_data)) {
        // For pre-3.9 versions of Contact Form 7
        $formdata = $cfdata->posted_data;
    } else {
        // We can't retrieve the form data
        return $cfdata;
    }
    // Check this is the user registration form
    if ($cfdata->title() == 'Your Registration Form Title') {
        $password = wp_generate_password(12, false);
        $email = $formdata['form-email-field'];
        $name = $formdata['form-name-field'];
        // Construct a username from the user's name
        $username = strtolower(str_replace(' ', '', $name));
        $name_parts = explode(' ', $name);
        if (!email_exists($email)) {
            // Find an unused username
            $username_tocheck = $username;
            $i = 1;
            while (username_exists($username_tocheck)) {
                $username_tocheck = $username . $i++;
            }
            $username = $username_tocheck;
            // Create the user
            $userdata = array('user_login' => $username, 'user_pass' => $password, 'user_email' => $email, 'nickname' => reset($name_parts), 'display_name' => $name, 'first_name' => reset($name_parts), 'last_name' => end($name_parts), 'role' => 'subscriber');
            $user_id = wp_insert_user($userdata);
            if (!is_wp_error($user_id)) {
                // Email login details to user
                $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
                $message = "Welcome! Your login details are as follows:" . "\r\n";
                $message .= sprintf(__('Username: %s'), $username) . "\r\n";
                $message .= sprintf(__('Password: %s'), $password) . "\r\n";
                $message .= wp_login_url() . "\r\n";
                wp_mail($email, sprintf(__('[%s] Your username and password'), $blogname), $message);
            }
        }
    }
    return $cfdata;
}

function insert_question($question_form)
{
    $submission = WPCF7_Submission::get_instance();
    if ($submission) {
        $posted_data = $submission->get_posted_data();
    }
    if ($posted_data['checktype'] == 'question') {
        $taxonomy = 'department';
        $term = get_term_by('slug', $posted_data['post_name'], $taxonomy);
        $term_id = $term->term_id;
        // Создание пользователя - автора вопроса
        if ($posted_data['your-email']) {
            $user_email = $posted_data['your-email'];
            $user_name = $posted_data['your-name'];
            $user_id = email_exists($user_email);
            if (!$user_id and email_exists($user_email) == false) {
                $random_password = wp_generate_password($length = 12, $include_standard_special_chars = false);
                $user_id = wp_create_user($user_name, $random_password, $user_email);
            }
        }
        $question = $posted_data['your-question'];
        // Добавление вопроса
        $question_id = wp_insert_post(array('post_content' => '', 'post_type' => 'question', 'post_title' => $user_name, 'post_excerpt' => $question, 'post_author' => $user_id));
        wp_set_object_terms($question_id, $term_id, $taxonomy);
        // Уведомление специалисту
        $doctors = get_posts(array('post_type' => 'doctor', 'tax_query' => array(array('taxonomy' => $taxonomy, 'field' => 'id', 'terms' => $term_id))));
        if ($doctors) {
            if ($posted_data['post_name'] == 'departments') {
                $page_url = get_site_url() . '/departments';
            } else {
                $page_url = get_site_url() . '/departments/' . $posted_data['post_name'];
            }
            $emails = array();
            $subject = 'Вопрос с сайта ' . get_site_url();
            $message = $posted_data['your-name'] . ' оставил(а) вопрос на странице: ' . $page_url;
            $headers = 'From: ' . get_bloginfo() . ' <no-reply@' . $_SERVER['SERVER_NAME'] . '>' . "\r\n";
            foreach ($doctors as $post) {
                setup_postdata($post);
                $author = get_user_by('login', get_the_author());
                $author_email = $author->user_email;
                array_push($emails, $author_email);
            }
            wp_reset_postdata();
            wp_mail($emails, $subject, $message, $headers);
        }
    }
    return $posted_data;
}

/**
 * Redirect the user to the thanks/success page after submitting the form
 *
 * @since 1.0
 */
function affwp_cf7_success_page_redirect($contact_form)
{
    // Success page ID
    $success_page = affwp_cf7_get_success_page_id();
    $submission = WPCF7_Submission::get_instance();
    // get the value of the name field
    $name = $submission->get_posted_data('your-name');
    // get the description
    $description = rawurlencode($contact_form->title());
    // add customer's email address to the description
    $description .= ' - ' . $submission->get_posted_data('your-email');
    // set the reference to be the first name
    $reference = isset($name) ? rawurlencode($name) : '';
    // redirect to success page
    if ($success_page) {
        $wpcf7 = WPCF7_ContactForm::get_current();
        $wpcf7->set_properties(array('additional_settings' => "on_sent_ok: \"location.replace(' " . add_query_arg(array('description' => $description, 'reference' => $reference, 'amount' => affwp_cf7_get_form_amount($contact_form->id())), get_permalink($success_page)) . " ');\""));
    }
}

function wpcf7_update_email_body($contact_form) {
  $submission = WPCF7_Submission::get_instance();

  if ($submission) {

    $mail = $contact_form->prop('mail');
    $posted_data = $submission->get_posted_data();
    $region_id = $posted_data['regionID'];

    if (!$region_id) {
      return true;
    }

    $recipient = get_post_meta($region_id, "contact_email", true);

    if (!$recipient) {
      return true;
    }

    $mail['recipient'] = $recipient;
    $contact_form->set_properties(array('mail' => $mail));

  }
}

// $special = apply_filters('wpcf7_special_mail_tags', '', $tagname, $html);

/**
 * Adds new event that send notification to Slack channel
 * when someone sent message through Contact Form 7.
 *
 * @param  array $events
 * @return array
 *
 * @filter slack_get_events
 */
function wp_slack_wpcf7_submit($events)
{
    $events['wpcf7_submit'] = array('action' => 'wpcf7_mail_sent', 'description' => __('When someone sent message through Contact Form 7', 'slack'), 'message' => function ($form, $result) {
        $submission = WPCF7_Submission::get_instance();
        if ($submission) {
            $posted_data = $submission->get_posted_data();
        }
        return apply_filters('slack_wpcf7_submit_message', sprintf(__('*%s* just sent a message titled "*%s*" through *%s*. Check your email!', 'slack'), $posted_data['your-name'], $posted_data['your-subject'], $form->title), $form, $result);
    });
    return $events;
}


add_filter( 'wpcf7_mail_components', function( $components ){
  $components['body'] = do_shortcode( $components['body'] );
  return $components;
} );

/*
Name : Mohit Bumb
Email : abcde@gmail.com
Phone : 19191919191
My Source : [my-source]
*/
add_action( 'wpcf7_init', 'custom_add_form_tag_my_source' );

function custom_add_form_tag_my_source() {
  // "my-source" is the type of the form-tag
  wpcf7_add_form_tag( 'my-source', 'custom_my_source_form_tag_handler' );
}

function custom_my_source_form_tag_handler( $tag ) {
  return isset( $_COOKIE['my_source'] ) ? $_COOKIE['my_source'] : '';
}

/**
 * A tag to be used in "Mail" section so the user receives the special tag
 * [tournaments]
 */
add_filter('wpcf7_special_mail_tags', 'wpcf7_tag_tournament', 10, 3);
function wpcf7_tag_tournament($output, $name, $html)
{
    $name = preg_replace('/^wpcf7\./', '_', $name); // for back-compat

    $submission = WPCF7_Submission::get_instance();

    if (! $submission) {
        return $output;
    }

    if ('tournaments' == $name) {
        return $submission->get_posted_data("tournaments");
    }

    return $output;
}

// Hook for additional special mail tag
add_filter( 'wpcf7_special_mail_tags', 'wti_special_mail_tag', 20, 3 );
function wti_special_mail_tag( $output, $name, $html )
{
   $name = preg_replace( '/^wpcf7\./', '_', $name );
   if ( '_my_cookie' == $name ) {
       $output = isset( $_COOKIE['my_source'] ) ? $_COOKIE['my_source'] : '';
   }
   return $output;
}
//you can use [_my_cookie] to call its value

/**
 * Contact form 7
 * custom tag: [posts show:12]
 * show parameter is optional
 */
add_action('wpcf7_init', 'custom_add_form_tag_posts');
 
function custom_add_form_tag_posts()
{
    wpcf7_add_form_tag('posts', 'custom_posts_form_tag_handler');
}
 
function custom_posts_form_tag_handler($tag)
{
    //get current (local) date
    $blogtime = current_time('mysql');
    list( $today_year, $today_month, $today_day, $hour, $minute, $second ) = preg_split('([^0-9])', $blogtime);

    //how may to show? (default 6)
    $numberPosts=6;
    $show=$tag->get_option('show', 'int', true);

    $args = [
        'post_type'     => 'posts',
        'posts_per_page'=> $show ? $show : $numberPosts,        
        'order'         => 'ASC'
    ];

    // The Query
    $the_query = new WP_Query($args);

    // The Loop
    $rows=[];
    if ($the_query->have_posts()) {
        while ($the_query->have_posts()) {
            $the_query->the_post();
            $rows[]=[
                'id'        =>get_the_ID(),
                'title'     =>get_the_title(),
             ];
        }
        wp_reset_postdata();
    }
    // debug your query
    // echo $the_query->request;
     
    // Structure
    $res="<div class='12u'><h3 class='mb-0'>No posts to display</h3></div>";

    if ($rows) {
        $res="<div class='row'>";
        
        foreach ($rows as $row) {
            $res.="<div>";
            $res.='<input type="checkbox" name="posts[]" value="'.esc_html($row['title']).'" id="'.esc_html($row['id']).'" />';
            $res.='<label for="'.esc_html($row['id']).'">'.esc_html($row['title']).' <br>';
            $res.= esc_html($row['title']);
            $res.=' </label>';
            $res.="</div>";
        }
        $res.="</div>";
    }
    
    return $res;
}

/**
 * When saving, change the array to a comma, separated list, just to make it easier 
 */
add_filter("wpcf7_posted_data", function ($posted_data) {
    //'posts' is the name that you gave the field in the CF7 admin.
    if (isset($posted_data['posts'])) {
        $posted_data['posts'] = implode(", ", $posted_data['posts']);
    }

    return $posted_data;
});

/**
 * A tag to be used in "Mail" section so the user receives the special tag
 * [posts]
 */
add_filter('wpcf7_special_mail_tags', 'wpcf7_tag_post', 10, 3);
function wpcf7_tag_post($output, $name, $html)
{
    $name = preg_replace('/^wpcf7\./', '_', $name); // for back-compat

    $submission = WPCF7_Submission::get_instance();

    if (! $submission) {
        return $output;
    }
    
    if ('posts' == $name) {
        return $submission->get_posted_data("posts");
    }

    return $output;
}
/*
<pre>FIELDS::: Array
(
    [createdAt] => 2020-10-14 18:31:30
    [userId] => 0
    [unitTag] => wpcf7-f329-p368-o1
    [formId] => 329
    [postId] => 368
    [taxos] => a:1:{i:0;a:7:{s:10:\"campaignId\";i:8;s:4:\"slug\";s:4:\"test\";s:4:\"name\";s:4:\"Test\";s:11:\"description\";s:17:\"Campagne des test\";s:9:\"startDate\";s:10:\"2020-10-11\";s:7:\"endDate\";s:10:\"2020-10-25\";s:5:\"cLogo\";s:3:\"276\";}}
    [private] => yes
    [tags] => a:12:{i:0;a:4:{i:0;s:75:\"Il y a toujours un éclairage même si la lumière naturelle est suffisante\";i:1;s:95:\"L’éclairage fonctionne toute la journée mais je l’éteins le soir en quittant ma place de\";i:2;s:80:\"Je éteins l’éclairage quand je quitte ma place de travail lors d’une pause\";i:3;s:85:\"L’éclairage artificiel fonctionne seulement lorsque c’est vraiment indispensable\";}i:1;a:4:{i:0;s:82:\"Jamais, ils se mettent seulement en veille automatiquement après quelques minutes\";i:1;s:82:\"J’éteins tout lorsque je quitte ma place de travail le soir (sans coupe-veille)\";i:2;s:88:\"J’éteins tout lorsque je quitte ma place de travail soir en utilisant un coupe-veille\";i:3;s:98:\"J’éteins aussi mon matériel lorsque je m’absente dans la journée (réunion, déjeuner, …)\";}i:2;a:4:{i:0;s:86:\"En période de chauffe, la température est homogène et bien réglée (21°c environ)\";i:1;s:82:\"Il fait quelquefois trop froid l’hiver et j’augmente (si je peux) le chauffage\";i:2;s:79:\"Il fait quelquefois trop chaud l’hiver et je baisse (si je peux) le chauffage\";i:3;s:107:\"Quelle que soit la température, je laisse toujours les fenêtres entrouvertes durant la période hivernale\";}i:3;a:4:{i:0;s:81:\"Ils sont changés rapidement dès qu’une version plus performante est proposée\";i:1;s:49:\"Achetés neufs, leur durée de vie avoisine 4 ans\";i:2;s:106:\"Achetés neufs, leur durée de vie excède largement 4 ans, les éventuelles réparations sont favorisées\";i:3;s:106:\"Achetés d’occasion pour un usage à moyen ou long terme, les éventuelles réparations sont favorisées\";}i:4;a:3:{i:0;s:92:\"J’ai l’habitude d’imprimer presque tous les documents que je reçois (mails et autres)\";i:1;s:166:\"J’imprime très régulièrement mais avec un paramétrage en mode éco (recto-verso, noir et blanc, brouillon voire double page) en privilégiant le papier recyclé\";i:2;s:69:\"J’utilise l’imprimante que lorsque c’est vraiment indispensable\";}i:5;a:6:{i:0;s:27:\"Voiture (en général seul)\";i:1;s:15:\"Moto ou scooter\";i:2;s:25:\"Voiture (en co-voiturage)\";i:3;s:31:\"Transports publics (train, bus)\";i:4;s:20:\"Vélo ou trottinette\";i:5;s:6:\"Marche\";}i:6;a:4:{i:0;s:13:\"Moins de 2 km\";i:1;s:12:\"De 2 à 5 km\";i:2;s:13:\"De 5 à 20 km\";i:3;s:12:\"Plus de 20km\";}i:7;a:4:{i:0;s:66:\"Il n’y a pas de possibilité de télétravail dans mon activité\";i:1;s:67:\"J’utilise ces outils très volontiers, dès que cela est possible\";i:2;s:76:\"Je les utilise seulement pour éviter un déplacement professionnel lointain\";i:3;s:44:\"J’évite de les utiliser dans tous les cas\";}i:8;a:2:{i:0;s:80:\"Bouteille d’eau jetable ou gobelet plastique pour le café, le thé ou l’eau\";i:1;s:63:\"Gourde ou mug pour boire l’eau du robinet, le thé ou l’eau\";}i:9;a:2:{i:0;s:104:\"Une préparation qui a été cuisinée à maison ou un plat du jour dans une caféteria ou un restaurant\";i:1;s:91:\"Un plat cuisiné emballé acheté dans un magasin ou un repas commandé et livré au bureau\";}i:10;a:3:{i:0;s:37:\"Tout est mis dans une poubelle unique\";i:1;s:100:\"Le papier usagé voire les capsules à café sont en principe collectés à part des autres déchets\";i:2;s:181:\"Je contribue à la collecte sélective de plusieurs types de déchets tels que : papier, pet, alu-fer blanc, capsules de café, piles, voire reste de repas (P’tite poubelle verte)\";}i:11;a:2:{i:0;s:22:\"Des solutions existent\";i:1;s:28:\"Ce n’est pas encore le cas\";}}
    [responses] => a:12:{i:0;s:95:\"L’éclairage fonctionne toute la journée mais je l’éteins le soir en quittant ma place de\";i:1;s:82:\"J’éteins tout lorsque je quitte ma place de travail le soir (sans coupe-veille)\";i:2;s:82:\"Il fait quelquefois trop froid l’hiver et j’augmente (si je peux) le chauffage\";i:3;s:106:\"Achetés neufs, leur durée de vie excède largement 4 ans, les éventuelles réparations sont favorisées\";i:4;s:69:\"J’utilise l’imprimante que lorsque c’est vraiment indispensable\";i:5;s:25:\"Voiture (en co-voiturage)\";i:6;s:12:\"De 2 à 5 km\";i:7;s:67:\"J’utilise ces outils très volontiers, dès que cela est possible\";i:8;s:80:\"Bouteille d’eau jetable ou gobelet plastique pour le café, le thé ou l’eau\";i:9;s:91:\"Un plat cuisiné emballé acheté dans un magasin ou un repas commandé et livré au bureau\";i:10;s:100:\"Le papier usagé voire les capsules à café sont en principe collectés à part des autres déchets\";i:11;s:28:\"Ce n’est pas encore le cas\";}
    [results] => a:12:{i:0;i:1;i:1;i:1;i:2;i:1;i:3;i:2;i:4;i:2;i:5;i:2;i:6;i:1;i:7;i:1;i:8;i:0;i:9;i:1;i:10;i:1;i:11;i:1;}
    [result_0] => 1
    [result_1] => 1
    [result_2] => 1
    [result_3] => 2
    [result_4] => 2
    [result_5] => 2
    [result_6] => 1
    [result_7] => 1
    [result_8] => 0
    [result_9] => 1
    [result_10] => 1
    [result_11] => 1
    [points] => a:12:{i:0;d:2;i:1;d:2;i:2;d:2;i:3;d:3;i:4;d:3;i:5;d:2;i:6;d:2;i:7;d:2;i:8;d:1;i:9;d:2;i:10;d:2;i:11;d:2;}
    [pointsTotal] => 25
    [series] => a:6:{i:0;d:4;i:1;d:2;i:2;d:6;i:3;d:6;i:4;d:3;i:5;d:4;}
    [theme_0] => 4
    [theme_1] => 2
    [theme_2] => 6
    [theme_3] => 6
    [theme_4] => 3
    [theme_5] => 4
    [graphSeries] => 4,2,6,6,3,4
    [mail] => a:9:{s:6:\"active\";b:1;s:7:\"subject\";s:4:\"Test\";s:6:\"sender\";s:38:\"Eco-Citoyens <rigasa@genevedurable.ch>\";s:9:\"recipient\";s:23:\"rigasa@genevedurable.ch\";s:4:\"body\";s:1717:\"<p>IP Address:213.55.244.12</p>De : Eco-Citoyens\nObjet : Questionnaire employés\n\n<table width=\"100%\" border=\"2\" cellspacing=\"0\" cellpadding=\"6\">\n    <caption>Questionnaire employés Test</caption>\n		<tbody>\n			<tr>\n				<td>Comment est utilisé l\'éclairage artificiel de votre bureau ?</td>\n				<td>[radio-1]</td>\n			</tr>\n			<tr>\n				<td>Eteignez-vous votre matériel informatique (ordinateur, écran et imprimante) ?;</td>\n				<td>[radio-2]</td>\n			</tr>\n			<tr>\n				<td>Comment appréciez-vous la température dans votre bureau ?</td>\n				<td>[radio-3]</td>\n			</tr>\n			<tr>\n				<td>Quelle est la durée de vie de vos équipements numériques ?</td>\n				<td>[radio-4]</td>\n			</tr>\n			<tr>\n				<td>Quel usage faites-vous de l’imprimante ?</td>\n				<td>[radio-5]</td>\n			</tr>\n			<tr>\n				<td>Par quel moyen principal vous rendez-vous à votre travail ?</td>\n				<td>[radio-6]</td>\n			</tr>\n			<tr>\n				<td>Pour aller au travail, je fais chaque jour…</td>\n				<td>[radio-7]</td>\n			</tr>\n			<tr>\n				<td>Quel comportement ai-je face aux nouveaux outils de télétravail ?</td>\n				<td>[radio-8]</td>\n			</tr>\n			<tr>\n				<td>Pour boire (en séance ou en pause), je suis plutôt…</td>\n				<td>[radio-9]</td>\n			</tr>\n			<tr>\n				<td>Lors du repas de midi, je privilégie de manger…</td>\n				<td>[radio-10]</td>\n			</tr>\n			<tr>\n				<td>Que fais-je des déchets courants que je produis au travail ?</td>\n				<td>[radio-11]</td>\n			</tr>\n			<tr>\n				<td>Existe-t-il pour le personnel des solutions visant à éviter la production de déchets et promouvoir les échanges ?</td>\n				<td>[radio-12]</td>\n			</tr>\n		</tbody>\n	</table>\n\n\n\n-- \nCet e-mail a été envoyé via le formulaire Questionnaire employés.\";s:18:\"additional_headers\";s:0:\"\";s:11:\"attachments\";s:0:\"\";s:8:\"use_html\";b:1;s:13:\"exclude_blank\";b:0;}
)
*/
