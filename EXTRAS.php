<?php

add_filter( 'jpeg_quality', create_function( '', 'return 75;' ) );

//-------------------------------------------------
// Compter le nombre de vues
function setPostViews($postID) {
    $count_key = 'post_views_count';
    $count = get_post_meta($postID, $count_key, true);
    if($count==''){
        add_post_meta($postID, $count_key, '1');
    }else{
        $count++;
        update_post_meta($postID, $count_key, $count);
    }
}
// setPostViews($postID);
// Retourner le nombre de vues
function getPostViews($postID){
    $count_key = 'post_views_count';
    $count = get_post_meta($postID, $count_key, true);
    if($count==''){
        return "1";
    }
    return $count;
}
// getPostViews($postID);
//
//Enregistrer et afficher le nombre de vues
function set_get_PostViews2($postID) {
	setPostViews($postID);
	$counter_views = getPostViews($postID);
	if ( $counter_views < 2) {
		echo $counter_views.' vue';}
	else {
		echo $counter_views.' vues';
	};
}

// set_get_PostViews2($postID);
// AJAX

function set_get_PostViews($postID) {
	if(!empty($_POST['ID_count'])){
		$postID = ($_POST['ID_count']);
	}
	setPostViews($postID);
	$counter_views = getPostViews($postID);
	$return .= '<div id="set_views_count" data-id="'. $postID .'">';
	if ( $counter_views < 2) {
		$return .= $counter_views .' vue';
	} else {
		$return .= $counter_views .' vues';
	};
	$return .= '</div>';
	echo $return;

	if(!empty($_POST['ID_count'])){
		die();
	}
}
add_action( 'wp_ajax_my_set_get_PostViews', 'set_get_PostViews' );
add_action( 'wp_ajax_nopriv_my_set_get_PostViews', 'set_get_PostViews' );

?>
<script>
if ($('#set_views_count').length) {
    var ID_count = $('#set_views_count').attr('data-id');
    $.post( ajaxurl, {
        action: 'my_set_get_PostViews',
        'ID_count': ID_count
    },
    function(response) {
        $('#set_views_count').html(response);
    });
    return false;
};
</script>
<?php
// Et enfin, il vous suffit de placer ce code dans votre page (single.php ?) à l’endroit où vous souhaitez afficher le compteur.

// set_get_PostViews($postID);


// Afficher le nombre de vues des posts dans l’administration
add_filter('manage_posts_columns', 'posts_column_views');
add_action('manage_posts_custom_column', 'posts_custom_column_views',5,2);
function posts_column_views($defaults){
    $defaults['post_views'] = __('Views');
    return $defaults;
}
function posts_custom_column_views($column_name, $id){
	if($column_name === 'post_views'){
        echo getPostViews(get_the_ID());
    }
}

//-------------------------------------------------
//-------------------------------------------------
//-------------------------------------------------
/*
Si vous souhaitez totalement interdire l’accès au back-office de votre site pour les utilisateurs n’ayant pas un accès administrateur, il vous suffit de coller ce snippet dans votre thème.

Les utilisateurs seront automatiquement redirigés vers la home de votre site.
*/
$wpba_required_capability = 'edit_others_posts';
$wpba_redirect_to = '';

function no_more_dashboard() {
	global $wpba_required_capability, $wpba_redirect_to;
		if (
			stripos($_SERVER['REQUEST_URI'],'/wp-admin/') !== false
			&&
			stripos($_SERVER['REQUEST_URI'],'async-upload.php') == false
			&&
			stripos($_SERVER['REQUEST_URI'],'admin-ajax.php') == false
		) {
			if (!current_user_can($wpba_required_capability)) {
					if ($wpba_redirect_to == '') { $wpba_redirect_to = get_option('siteurl'); }
					wp_redirect($wpba_redirect_to,302);
			}
		}
}
add_action('admin_init', 'no_more_dashboard');

/*
Notez qu’ici on applique ce bouton que sur les pages de type poste. On pourrait modifier is_single() par is_singular( ‘mon_type_de_post’ ) pour un besoin plus spécifique encore.
*/
function insertbackbutton ($content){
	if ( is_single()) {
		$back = $_SERVER['HTTP_REFERER'];
		if( isset($back) && $back !='') {
			 $content .= '<a class="theme-button" href="'.$back.'" title="Retour"><i class="icon-caret-left"></i> Retour</a> ';
		}
	};
	return $content;
}
add_filter ('the_content', 'insertbackbutton');

function bweb_search_filter_exclude_frontpage( $query ) {
  if ( $query->is_search && $query->is_main_query() && !is_admin() ) {
    $query->set( 
		'post__not_in', 
		array( 
			get_option('page_on_front'), 
			get_option('page_for_posts') 
		) 
	); 
  }
}
add_action( 'pre_get_posts', 'bweb_search_filter_exclude_frontpage' );

function bweb_disallow_file_modifications(){
	$currentSite = get_site_url();
	if( ( strpos( $currentSite, '.local' ) !== false ) 
		&& ( strpos( $currentSite, 'preprod.' ) !== false )
		&& !defined( 'DISALLOW_FILE_MODS' ) ){
		define( 'DISALLOW_FILE_MODS', true );
	}
}
add_filter( 'init', 'bweb_disallow_file_modifications' );

add_action( 'content_save_pre', 'bweb_remove_shortcodes', 9, 2 );
function bweb_remove_shortcodes( $content  ) {
	
	$tags_to_remove = array(
		'shortcode_to_remove_1',
		'shortcode_to_remove_2',
	);

	foreach( $tags_to_remove as $tag ){
		$content = preg_replace('#\[[^\[]*' . $tag . '[^\]]*\]#', '', $content );
	}

	return $content;

}

function bweb_replace_shortcode($string) {
    $replace = array( '[' => '&#91;', ']' => '&#93;' );
    $newstring = str_replace( array_keys( $replace ), array_values( $replace ), $string[2] );
    return $string[1] . $newstring . $string[3];
}
function bweb_prevent_shortcode( $content ) {
	$pattern = '#(<pre.*?>|<code.*?>)(.*?)(<\/pre>|<\/code>)#imsu';
	return preg_replace_callback( $pattern, 'bweb_replace_shortcode', $content );
}
add_filter( 'the_content', 'bweb_prevent_shortcode', 9);

?>
<style>
.video-container {
	position: relative;
	padding-bottom: 56.25%; /* 16:9 */
	padding-top: 25px;
	margin-bottom: 20px;
	height: 0;
}
.video-container iframe {
	position: absolute;
	top: 0;
	left: 0;
	width: 100%;
	height: 100%;
}
</style>

<script>
jQuery(document).ready( function($) {
	// Open all external link in a new tab
	$('a').each(function() {
	   var a = new RegExp('/' + window.location.host + '/');
	   if (!a.test(this.href)) {
		   // This is an external link
			$(this).attr("target","_blank");
			// Remove after your tests 
			console.log($(this));
	   }
	});
});
</script>
<?php

function bweb_admin_favicon() {
    $favicon_url = get_stylesheet_directory_uri() . 'img/icon_bweb.png';
    echo '<link rel="shortcut icon" href="' . $favicon_url . '" />';
}
add_action( 'admin_head', 'bweb_admin_favicon' );


function bweb_plugin_admin_favicon() {
    $screen = get_current_screen();
	// /wp-admin/admin.php?page=plugin-ID
    if ( $screen->id != 'toplevel_page_' . 'plugin-ID' )
        return;

  	$favicon_url = plugin_dir_url(__FILE__) . 'img/icon_bweb.png';
	echo '<link rel="shortcut icon" href="' . $favicon_url . '" />';
}
add_action( 'admin_head', 'bweb_plugin_admin_favicon' );


function bweb_plugin_meta_links( $links, $file ) {
	if ( $file === 'your-plugin-folder/your-main-plugin-file.php' ) {
		$links[] = '<a href="https://www.b-website.com/category/plugins" target="_blank" title="' . __( 'More bweb Plugins', 'texdomain' ) . '">' . __( 'More bweb Plugins', 'texdomain' ) . '</a>';
		$links[] = '<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=7Z6YVM63739Y8" target="_blank" title="' . __( 'Donate to this plugin &#187;' ) . '"><strong>' . __( 'Donate to this plugin &#187;' ) . '</strong></a>';
	}
	return $links;
}
add_filter( 'plugin_row_meta', 'bweb_plugin_meta_links', 10, 2 );

function bweb_remove_post_formats() {
	add_theme_support( 'post-formats', array( 'link', 'audio', 'status' ) );
}
add_action( 'after_setup_theme', 'bweb_remove_post_formats', 11 );

function bweb_dam_the_flood( $dam_it, $time_last, $time_new ) {
	if ( ($time_new - $time_last) < 300 ) // intervalle de temps en secondes
		return true;
	return false;
}
add_filter('comment_flood_filter', 'bweb_dam_the_flood', 10, 3);

function bweb_author_bio_nl2br( $value, $user_id ){
	if ($user_id == 1)
	return 'Bio : ' . nl2br( $value );

	return $value;
}
add_filter( 'get_the_author_description', 'bweb_author_bio_nl2br', 9, 2 ); 

function bweb_comment_editor_quicktags() {
	$quicktags_settings = array( 'buttons' => 'strong,em,link,block,del,ins,img,ul,ol,li,code,close' );
	wp_editor( '', 'comment', array( 'media_buttons' => false, 'tinymce' => false, 'quicktags' => $quicktags_settings ) );
}
add_filter( 'comment_form_field_comment', 'bweb_comment_editor_quicktags' );

function login_message_bweb( $message ) {
    $action = $_REQUEST['action'];
    if( $action == 'lostpassword' ) {
        $message = '<p class="message">Entrez votre adresse email ci-dessous. Un email vous sera envoyé sur votre messagerie pour réinitialiser votre mot de passe.Pensez à vérifier vos spams !</p>';
        return $message;
    }
    return;
}
add_filter( 'login_message', 'login_message_bweb' );

function bweb_comment_editor() {
  global $post;
 
  ob_start();
 
  wp_editor( '', 'comment', array(
    'textarea_rows' => 15,
    'teeny' => true,
    'quicktags' => false,
    'media_buttons' => false
  ) );
 
  $editor = ob_get_contents();
 
  ob_end_clean();
 
  //make sure comment media is attached to parent post
  $editor = str_replace( 'post_id=0', 'post_id='.get_the_ID(), $editor );
 
  return $editor;
}
 
// wp_editor doesn't work when clicking reply. Here is the fix.
function bweb_scripts() {
    wp_enqueue_script('jquery');
}
add_action( 'wp_enqueue_scripts', 'bweb_scripts' );

function bweb_comment_reply_link($link) {
    return str_replace( 'onclick=', 'data-onclick=', $link );
}
add_filter( 'comment_reply_link', 'bweb_comment_reply_link' );

function bweb_wp_head() {
	echo "
		<script type='text/javascript'>
		  jQuery(function($){
			$('.comment-reply-link').click(function(e){
			  e.preventDefault();
			  var args = $(this).data('onclick');
			  args = args.replace(/.*\(|\)/gi, '').replace(/\"|\s+/g, '');
			  args = args.split(',');
			  tinymce.EditorManager.execCommand('mceRemoveEditor', true, 'comment');
			  addComment.moveForm.apply( addComment, args );
			  tinymce.EditorManager.execCommand('mceAddEditor', true, 'comment');
			  $('#wp-link .howto, #search-panel').remove();
			});
		  });
		</script>
	";
}
add_filter( 'comment_form_field_comment', 'bweb_comment_editor' );
add_action( 'wp_head', 'bweb_wp_head' );

function bweb_plugin_add_action_links ( $links ) {
	$links[] = '<a href="' . admin_url( 'options-general.php?page=ID-plugin' ) . '">' . __( 'Settings', 'plugin-textdomain' ) . '</a>';
	return $links;
}
add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'bweb_plugin_add_action_links' );

function excerpt_more_example( $text ) {
   global $post;
   return ' <a href="'. get_permalink($post->ID) . '"><i class="read-more"></i>' . __('Read more', TEXTDOMAIN) . '</a>';
}
add_filter( 'excerpt_more', 'excerpt_more_example' );

function bweb_posts_ads($content){
	if ( !is_singular( 'post' ) )
	return $content;
	
	ob_start();
	$newContent.= LaFonctionDuSHORTCODE( array ( "parametre1" => "xxxx", "parametre2" => "xxx"));
	$newContent.= $content;
	$newContent.= ob_get_clean();

	return $newContent;
}
remove_filter( 'the_content', 'wpautop' );
add_filter('the_content', 'bweb_posts_ads', 10);
add_filter( 'the_content', 'wpautop',9 );

?>
<style>
	.entry > p a[target="_blank"][href^="http://"]:not([href*="b-website.com"]):after, 
.entry > p a[target="_blank"][href^="https://"]:not([href*="b-website.com"]):after,
.entry > ul > li > a[target="_blank"][href^="http://"]:not([href*="b-website.com"]):after, 
.entry > ul > li > a[target="_blank"][href^="https://"]:not([href*="b-website.com"]):after {
	content: "";
	display: inline-block;
	padding-left: 5px;
	margin-left: 5px;
	width: 7px;
	height: 11px;
	background-image: url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAwAAAALCAYAAABLcGxfAAAACXBIWXMAAAsTAAALEwEAmpwYAAAAIGNIUk0AAHolAACAgwAA+f8AAIDpAAB1MAAA6mAAADqYAAAXb5JfxUYAAACySURBVHjafNAxagJhEAXgL8s2gRzA1spaAjmAoCDYBDzD1kIOkdrKJrXkAIIHsBNtBUEiCMKCJJBqy6T5F34W/30wxTDvzbw3D0VRaEEXX1FfZZjiG7tGrXDGLBI85nhHH5fEld+4yVC1kF/xgS0WkLf4H+ATB0xwwzFLkF9ChivGgQzze4Ie1vjBCB0M4wxNlNiHzaew4Lke5omvjFLBsshGW/gyFrxhg79EPWFZC/4HAJkNJ+ROVdV2AAAAAElFTkSuQmCC');
}
</style>
<?php
add_filter( 'wp_redirect', 'baw_hack_delete_post_redirect' );
function baw_hack_delete_post_redirect( $url ) {
	$ref = wp_get_referer();
	if ( strpos( $ref, admin_url() ) === false 
		&& isset( $_GET['action'], $_GET['post'] ) 
		&& ( ( 'delete' == $_GET['action'] && check_admin_referer('delete-post_' . $_GET['post'] ) )
			|| ( 'trash' == $_GET['action'] && check_admin_referer('trash-post_' . $_GET['post'] ) )
			)
	) {
		return home_url();
	}
	return $url;
}

//____________________________________________
/*
WordPress intègre nativement différents états de posts, il s’agit des 8 posts status suivants :

Published
Future
Draft
Pending
Private
Trash
Auto-Draft
Inherit
Si vous avez besoin d’en ajouter, c’est tout à fait possible et parfoit très utile dans la cadre d’un Worflow de validation poussé.

L’exemple qui suit m’a servi à créer un nouvel état de post afin de le rendre inaccessible, mais de pouvoir le republier par la suite. Il ne s’agit pas d’un brouillon, ni d’un post en attente de relecture, mais d’un post qui n’est plus valable car dépassé.

Voici comment procéder :
*/
//--------------------------------------------
//enregistrement du CPS
function custom_post_status_bweb(){
	register_post_status( 'outdated', array(
		'label'                     => 'outdated',
		'public'                    => false,
		'exclude_from_search'       => true,
		'show_in_admin_all_list'    => true,
		'show_in_admin_status_list' => true,
		'label_count'               => _n_noop( 'Dépassé <span class="count">(%s)</span>', 'Dépassé <span class="count">(%s)</span>' ),
	) );
}
add_action( 'init', 'custom_post_status_bweb' );

//affichage du CPS dans le menu déroulant (seul sur la page d'édition du post (edition full) --> en cours de maj sur le coeur)
function append_post_status_list_bweb(){
     global $post;
     $complete = '';
     $label = '';
     if($post->post_type == 'post'){
          if($post->post_status == 'outdated'){
               $complete = ' selected=\"selected\"';
               $label = '<span id=\"post-status-display\"> Dépassé</span>';
          }
          echo '
			  <script>
			  jQuery(document).ready(function($){
				   $("select#post_status").append("<option value=\"outdated\" '.$complete.'>Dépassé</option>");
				   $(".misc-pub-section label").append("'.$label.'");
			  });
			  </script>
          ';
     }
}
add_action('admin_footer-post.php', 'append_post_status_list_bweb');

//affiche l'état à côté du titre dans la liste 'all' lorsque le statut est public
function display_state_post_status_bweb( $states ) {
     global $post;
     $arg = get_query_var( 'post_status' );
     if($arg != 'outdated'){
          if($post->post_status == 'outdated'){
               return array('Dépassé');
          }
     }
    return $states;
}
add_filter( 'display_post_states', 'display_state_post_status_bweb' );
//--------------------------------------------
function removeHooksHead_fn()
{
	remove_action( 'wp_head', 'rsd_link' );
	remove_action( 'wp_head', 'wlwmanifest_link' );
	remove_action( 'wp_head', 'wp_generator' );
	remove_action( 'wp_head', 'start_post_rel_link' );
	remove_action( 'wp_head', 'index_rel_link' );
	remove_action( 'wp_head', 'adjacent_posts_rel_link' );
	remove_action( 'wp_head', 'wp_shortlink_wp_head' );
}

function fjarrett_remove_wp_version_strings( $src ) {
     global $wp_version;
     parse_str(parse_url($src, PHP_URL_QUERY), $query);
     if ( !empty($query['ver']) && $query['ver'] === $wp_version ) {
          $src = remove_query_arg('ver', $src);
     }
     return $src;
}
add_filter( 'script_loader_src', 'fjarrett_remove_wp_version_strings' );
add_filter( 'style_loader_src', 'fjarrett_remove_wp_version_strings' );

function remove_wp_version(){
	return '';
}
add_filter('the_generator', 'remove_wp_version');

function bweb_session_start() {
   if ( !session_id() ) {
      session_start();
   }
}
add_action( 'init', 'bweb_session_start' );

function bweb_change_post_status( $post_id, $status ){
     $current_post = get_post( $post_id, 'ARRAY_A' );
     $current_post[ 'post_status' ] = $status;
     wp_update_post( $current_post );
}

function mon_favicon(){
  echo '<link rel="shortcut icon" href="'. get_stylesheet_directory_uri() .'/images/favicon.png" />';
}
add_action( 'wp_head', 'mon_favicon');

function bm_human_time_diff_enhanced( $duration = 60 ) {

	$post_time = get_the_time('U');
	$human_time = '';

	$time_now = date('U');

	// use human time if less that $duration days ago (60 days by default)
	// 60 seconds * 60 minutes * 24 hours * $duration days
	if ( $post_time > $time_now - ( 60 * 60 * 24 * $duration ) ) {
		$human_time = sprintf( __( '%s ago', 'binarymoon'), human_time_diff( $post_time, current_time( 'timestamp' ) ) );
	} else {
		$human_time = get_the_date();
	}

	return $human_time;

}

//usage
//echo bm_human_time_diff_enhanced();

function themeblvd_time_ago() {
     
    global $post;
     
    $date = get_post_time('G', true, $post);
     
    /**
     * Where you see 'themeblvd' below, you'd
     * want to replace those with whatever term
     * you're using in your theme to provide
     * support for localization.
     */
     
    // Array of time period chunks
    $chunks = array(
        array( 60 * 60 * 24 * 365 , __( 'year', 'themeblvd' ), __( 'years', 'themeblvd' ) ),
        array( 60 * 60 * 24 * 30 , __( 'month', 'themeblvd' ), __( 'months', 'themeblvd' ) ),
        array( 60 * 60 * 24 * 7, __( 'week', 'themeblvd' ), __( 'weeks', 'themeblvd' ) ),
        array( 60 * 60 * 24 , __( 'day', 'themeblvd' ), __( 'days', 'themeblvd' ) ),
        array( 60 * 60 , __( 'hour', 'themeblvd' ), __( 'hours', 'themeblvd' ) ),
        array( 60 , __( 'minute', 'themeblvd' ), __( 'minutes', 'themeblvd' ) ),
        array( 1, __( 'second', 'themeblvd' ), __( 'seconds', 'themeblvd' ) )
    );
 
    if ( !is_numeric( $date ) ) {
        $time_chunks = explode( ':', str_replace( ' ', ':', $date ) );
        $date_chunks = explode( '-', str_replace( ' ', '-', $date ) );
        $date = gmmktime( (int)$time_chunks[1], (int)$time_chunks[2], (int)$time_chunks[3], (int)$date_chunks[1], (int)$date_chunks[2], (int)$date_chunks[0] );
    }
     
    $current_time = current_time( 'mysql', $gmt = 0 );
    $newer_date = strtotime( $current_time );
 
    // Difference in seconds
    $since = $newer_date - $date;
 
    // Something went wrong with date calculation and we ended up with a negative date.
    if ( 0 > $since )
        return __( 'sometime', 'themeblvd' );
 
    /**
     * We only want to output one chunks of time here, eg:
     * x years
     * xx months
     * so there's only one bit of calculation below:
     */
 
    //Step one: the first chunk
    for ( $i = 0, $j = count($chunks); $i < $j; $i++) {
        $seconds = $chunks[$i][0];
 
        // Finding the biggest chunk (if the chunk fits, break)
        if ( ( $count = floor($since / $seconds) ) != 0 )
            break;
    }
 
    // Set output var
    $output = ( 1 == $count ) ? '1 '. $chunks[$i][1] : $count . ' ' . $chunks[$i][2];
     
 
    if ( !(int)trim($output) ){
        $output = '0 ' . __( 'seconds', 'themeblvd' );
    }
     
    $output .= __(' ago', 'themeblvd');
     
    return $output;
}
 
// Filter our themeblvd_time_ago() function into WP's the_time() function
add_filter('the_time', 'themeblvd_time_ago');

//Avec cette méthode, vous pouvez donc construire une nouvelle requête via query_posts avec ses propres paramètres sans casser la requête principale.
$tmp_query = $wp_query;
query_posts('posts_per_page=-1&post_type=post&order=DESC');
   while( have_posts() ) : 
		the_post();
      echo '<a href="'. get_the_permalink() .'">'. get_the_title() .'</a><br />';
   endwhile;
$wp_query = $tmp_query;


function front_ajaxurl() {
     echo '<script>// <![CDATA[
          var ajaxurl = "'.admin_url('admin-ajax.php').'";
     // ]]></script>';
}
add_action('wp_head','front_ajaxurl', 9999);

add_filter('show_admin_bar', '__return_false');

function gravatar_perso ($avatar_defaults) {
     $myavatar = get_bloginfo('template_directory') . '/images/avatar.png';
     $avatar_defaults[$myavatar] = "Le nom de mon avatar";
     return $avatar_defaults;
}
add_filter( 'avatar_defaults', 'gravatar_perso' );

// https://www.b-website.com/un-superbe-outil-pour-presenter-le-rendu-dun-site-responsif


function bweb_breadcrumb(){
	wp_reset_query();
	echo '<ul class="breadcrumbs">';
		if (is_tag()) {echo"<li>Tag : ";single_tag_title();}
		elseif (is_day()) {echo"<li>Archive de "; the_time('F jS, Y'); echo'</li>';}
		elseif (is_month()) {echo"<li>Archive de "; the_time('F, Y'); echo'</li>';}
		elseif (is_year()) {echo"<li>Archive de "; the_time('Y'); echo'</li>';}
		elseif (is_author()) {echo"<li>Archive"; echo'</li>';}
		elseif (isset($_GET['paged']) && !empty($_GET['paged'])) {echo "<li>Archives du Blog "; echo'</li>';}
		elseif (is_search()) {echo"<li>Résultat de recherche"; echo'</li>';}
		elseif (!is_home() && !is_front_page() ) {
			echo '<li><a href="';
			echo get_option('home');
			echo '">';
			echo 'Accueil';
			echo '</a></li><li class="separator"> / </li>';
			if (is_category() || is_single()) {
				echo '<li>';
				the_category(' </li><li class="separator"> / </li><li> ');
				if (is_single()) {
					echo '</li><li class="separator"> / </li><li>';
					the_title();
					echo '</li>';
				}
			} elseif (is_page()) {
				if($post->post_parent){
					$anc = get_post_ancestors( $post->ID );

					foreach ( $anc as $ancestor ) {
						$output = '<li><a href="'.get_permalink($ancestor).'" title="'.get_the_title($ancestor).'">'.get_the_title($ancestor).'</a></li> <li class="separator">/</li>';
					}
					echo $output;
					echo $title;
				} else {
					echo the_title();
				}
			}
		}
    echo '</ul>';
}

// Miniatures standard
if(false === get_option("thumbnail_crop")) {
	add_option("thumbnail_crop", "1"); 
} else {
	update_option("thumbnail_crop", "1");
}

// Miniatures moyenne
if(false === get_option("medium_crop")) {
	add_option("medium_crop", "1"); 
} else {
	update_option("medium_crop", "1");
}

// // Grande miniatures
if(false === get_option("large_crop")) {
	add_option("large_crop", "1"); 
} else {
	update_option("large_crop", "1");
}





