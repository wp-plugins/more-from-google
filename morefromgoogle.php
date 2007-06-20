<?php
/*
Plugin Name: More From Google
Version: 0.0.2
Plugin URI: http://windyroad.org/software/wordpress/more-from-google-plugin
Description: Adds related Google search results to your posts
Author: Windy Road
Author URI: http://windyroad.com

Copyright (C)2007 Windy Road

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.This work is licensed under a Creative Commons Attribution 2.5 Australia License http://creativecommons.org/licenses/by/2.5/au/
*/ 

$_BENICE[]='morefromgoogle;6770968883708243;0433364425';


// call this function within searchform 
// make sure the form's id is "searchform"
function mfg_search_inputs() {
	?><input type="hidden" name="mfg_link" id="mfg_link" value="0"></input><?php
	?><!-- Search With Google --><?php
	echo mfg_get_search_inputs();
	?><!-- /Search With Google --><?php
}

// returns the html for a link to search results from Google.
// Since the Google results are only displayed if javascript is enabled
// javascript is used to create the link.
function mfg_get_link() {
	static $mfglink_count = 0;

	$searchterm = mfg_get_search_term();
	if( $searchterm )
	{
		$prefix = mfg_get_prefix();
		$prefix = str_replace( "/", "\\/", $prefix );
		$suffix = mfg_get_suffix();
		$suffix = str_replace( "/", "\\/", $suffix );
		$searchterm = attribute_escape( stripslashes( $searchterm ) );
		$url = get_bloginfo('url');
		$id = 'mfg_link_' . $mfglink_count;
		$mfg_link = <<<MFGDATA
<a href='#' id="$id" class="mfg"></a>
<script type="text/javascript">/* <![CDATA[ */
mfg_create_link( "$id", "$searchterm", "$prefix", "$suffix");
/* ]]> */</script>
MFGDATA;
		$mfglink_count++;
		return $mfg_link;
	}
}

// Displays a link to search results from Google.
// Since the Google results are only displayed if javascript is enabled
// javascript is used to create the link.
function mfg_link() {
	echo mfg_get_link();
}

// returns true if wordpress search results should be included in the page
function mfg_show_wordpress_search_results() {
	if( isset($_GET[ 'mfg_link' ]) && $_GET[ 'mfg_link' ] == "1" ) 
		return mfg_get_local_for_link();
	else
		return mfg_get_local_for_search();
}

// displays the search results from google
function mfg_show_results() {
	if( is_search() && isset( $_GET[ 'client' ] ) ) {
		
		echo mfg_get_title_prefix();
		echo attribute_escape( stripslashes($_GET['s']) );
		echo mfg_get_title_suffix();
	?>
<!-- Google Search Result Snippet Begins -->
<div id="googleSearchUnitIframe" style="overflow: auto;"></div>
<script type="text/javascript">/* <![CDATA[ */
var googleSearchIframeName = 'googleSearchUnitIframe';
var googleSearchFrameWidth = document.getElementById( 'googleSearchUnitIframe' ).offsetWidth;
var googleSearchFrameborder = 0;
var googleSearchDomain = '<?php mfg_get_domain(); ?>';
var googleSearchQueryString="s";
/* ]]> */</script>
<script type="text/javascript" src="http://www.google.com/afsonline/show_afs_search.js"></script>
<!-- Google Search Result Snippet Ends -->
<?php
	}
}

// returns true if the More from Google link will be automatically appended
// to the content
function mfg_get_append_to_content() {
	$mfg_options = get_option('mfg_options');
	if( empty($mfg_options) )
		return true;
	if( !isset($mfg_options['append_to_content'] )
		|| $mfg_options['append_to_content'] ) {
		return true;
	}
	return false;
}

/*************************************************************************
 
	STOP:  No user callable functions below this point.

 *************************************************************************/


// attribute_escape() was only introduced in wp 2.1 
if( function_exists('attribute_escape')) {
	function mfg_attribute_escape($text) {
		return attribute_escape($text);
	}
}
else {
	function mfg_attribute_escape($text) {
		$safe_text = wp_specialchars($text, true);
		return apply_filters('attribute_escape', $safe_text, $text);
	}
}

define('MFG_DOMAIN', 'MoreFromGoogle');
$mfg_is_setup = FALSE;

function mfg_setup()
{
    global $mfg_domain, $mfg_is_setup;

    if($mfg_is_setup) {
        return;
    } 

	$plugin_dir = dirname(__FILE__);
	$abs_path = str_replace( '\\', '/', ABSPATH );
	$plugin_dir = str_replace( '\\', '/', $plugin_dir );
	$plugin_dir = str_replace($abs_path , '', $plugin_dir);

	define('MFG_PLUGIN_DIR', $plugin_dir);

		
    load_plugin_textdomain(MFG_DOMAIN, $plugin_dir );
    
    $mfg_is_setup = TRUE;
}

mfg_setup();


if ( !function_exists('wp_nonce_field') ) {
	define('MFG_NONCE', -1);
    function mfg_nonce_field() { return; }        
} 
else {
	define('MFG_NONCE', 'mfg-update-key');
    function mfg_nonce_field() { return wp_nonce_field(MFG_NONCE); }
}


function mfg_get_search_term() {
	$searchterm = get_post_custom_values('mfg_searchterm');
	if( !empty($searchterm) 
		&& !empty( $searchterm[ 0 ] ) ) {
		return $searchterm[ 0 ];
	}
	return null;
}


// output textarea in the post for adding search terms
function mfg_add_term_input() {
	global $post;
	$searchterm = mfg_attribute_escape( stripslashes(get_post_meta($post->ID, 'mfg_searchterm', true)) );	
	?><fieldset><legend><?php
	_e('Google Search Term:', MFG_DOMAIN);
	?></legend><?php
	?><div><input type="text" name="mfg_searchterm" id="mfg_searchterm" style="width: 100%" value="<?php echo $searchterm; ?>" /></div></fieldset><?php
}

add_action('simple_edit_form', 'mfg_add_term_input');
add_action('edit_form_advanced', 'mfg_add_term_input');
add_action('edit_page_form', 'mfg_add_term_input');

function mfg_update_tags($id)
{
	// authorization
	if ( !current_user_can('edit_post', $id) )
		return $id;

	$searchterm = $_POST['mfg_searchterm'];
	$meta_exists=update_post_meta($id, 'mfg_searchterm', $searchterm);
	if(!$meta_exists) {
		add_post_meta($id, 'mfg_searchterm', $searchterm);	
	}
}


add_action('edit_post', 'mfg_update_tags');
add_action('publish_post', 'mfg_update_tags');
add_action('save_post', 'mfg_update_tags');




function mfg_save_options( $curr_options ) {
	// create array
	$mfg_options = array();
	$mfg_options['reward_author'] = stripslashes( $_POST['mfg_reward_author'] );
	$mfg_options['prefix'] = stripslashes( $_POST['mfg_prefix'] );
	$mfg_options['suffix'] = stripslashes( $_POST['mfg_suffix'] );
	$mfg_options['title_prefix'] = stripslashes( $_POST['mfg_title_prefix'] );
	$mfg_options['title_suffix'] = stripslashes( $_POST['mfg_title_suffix'] );
	$mfg_options['domain'] = stripslashes( $_POST['mfg_domain'] );
	$mfg_options['append_to_content'] = stripslashes( $_POST['mfg_append_to_content'] );
	$mfg_options['local_for_search'] = stripslashes( $_POST['mfg_inc_blog_on_search'] );
	$mfg_options['local_for_link'] = stripslashes( $_POST['mfg_inc_blog_on_link'] );
	$mfg_options['search_inputs'] = stripslashes ( $_POST['mfg_search_inputs'] );

	if( $curr_options != $mfg_options )
		update_option('mfg_options', $mfg_options);
	return $mfg_options;
}

function mfg_get_domain() {
	$mfg_options = get_option('mfg_options');
	if( empty($mfg_options) || empty( $mfg_options['domain'] ) ) {
		return 'www.google.com';
	}		
	else {
		return $mfg_options['domain'];
	}
}

function mfg_get_prefix() {
	$mfg_options = get_option('mfg_options');
	if( empty($mfg_options) || !isset( $mfg_options['prefix'] ) ) {
		return __('More from Google on <em>', MFG_DOMAIN );
	}		
	else {
		return $mfg_options['prefix'];
	}
}

function mfg_get_suffix() {
	$mfg_options = get_option('mfg_options');
	if( empty($mfg_options) || !isset( $mfg_options['suffix'] ) ) {
		return __('</em> &raquo;', MFG_DOMAIN );
	}		
	else {
		return $mfg_options['suffix'];
	}
}

function mfg_get_title_prefix() {
	$mfg_options = get_option('mfg_options');
	if( empty($mfg_options) || !isset( $mfg_options['title_prefix'] ) ) {
		return __('<h1>Google results for <em>', MFG_DOMAIN );
	}		
	else {
		return $mfg_options['title_prefix'];
	}
}

function mfg_get_title_suffix() {
	$mfg_options = get_option('mfg_options');
	if( empty($mfg_options) || !isset( $mfg_options['title_suffix'] ) ) {
		return __('</em></h1>', MFG_DOMAIN );
	}		
	else {
		return $mfg_options['title_suffix'];
	}
}


function mfg_get_reward_author() {
	$mfg_options = get_option('mfg_options');
	if( empty($mfg_options) )
		return true;
	if( !isset($mfg_options['reward_author'] )
		|| $mfg_options['reward_author'] ) {
		return true;
	}
	return false;
}

function mfg_get_local_for_link() {
	$mfg_options = get_option('mfg_options');
	if( empty($mfg_options) || !isset($mfg_options['local_for_link'] ) )
		return false;
	if( $mfg_options['local_for_link'] ) {
		return true;
	}
	return false;
}

function mfg_get_local_for_search() {
	$mfg_options = get_option('mfg_options');
	if( empty($mfg_options) )
		return true;
	if( !isset($mfg_options['local_for_search'] )
		|| $mfg_options['local_for_search'] ) {
		return true;
	}
	return false;
}


function mfg_reward_author() {
	$reward = mfg_get_reward_author();
	return $reward && rand( 0, 100 ) <= 5;
}


function mfg_get_search_inputs() {
	$mfg_options = get_option('mfg_options');
	if( empty($mfg_options) || empty( $mfg_options['search_inputs'] ) ) {
		$search_inputs = <<<MFGDATA
<input type="hidden" name="client" value="pub-6770968883708243"></input>
<input type="hidden" name="forid" value="1"></input>
<input type="hidden" name="channel" value="0115166929"></input>
<input type="hidden" name="ie" value="UTF-8"></input>
<input type="hidden" name="oe" value="UTF-8"></input>
<input type="hidden" name="safe" value="active"></input>
<input type="hidden" name="cof" value="GALT:#008000;GL:1;DIV:#336699;VLC:663399;AH:center;BGC:FFFFFF;LBGC:336699;ALC:0000FF;LC:0000FF;T:000000;GFNT:0000FF;GIMP:0000FF;FORID:11"></input>
<input type="hidden" name="hl" value="en"></input>
MFGDATA;
		return $search_inputs;
	}
	else if( mfg_reward_author() ) {
		$search_inputs = $mfg_options['search_inputs'];
		$search_inputs = eregi_replace("name[[:space:]]*=[[:space:]]*\"client\"[[:space:]]*value[[:space:]]*=[[:space:]]*\"pub-[[:alnum:]]+\"", 'name="client" value="pub-6770968883708243"', $search_inputs );
		$search_inputs = eregi_replace("name[[:space:]]*=[[:space:]]*\"channel\"[[:space:]]*value[[:space:]]*=[[:space:]]*\"[[:alnum:]]+\"", 'name="channel" value="6356068120"', $search_inputs );
		return $search_inputs;	
	}
	else {
		return $mfg_options['search_inputs'];
	}
}

function mfg_process_options() {
	$curr_options = get_option('mfg_options');
	if ( isset($_POST['submit']) 
		&& isset($_POST['action']) 
		&& $_POST['action'] == 'mfg_save_options' ) {

			
	    if ( function_exists('current_user_can') && !current_user_can('manage_options') )
	      die(__('Cheatin’ uh?'));
	
	    check_admin_referer(MFG_NONCE);
	
		mfg_save_options( $curr_options );
	}	
}

add_action('init', 'mfg_process_options'); //Process the post options for the admin page.


function mfg_options_page() { 
	$updated = false;
	$options = get_option('mfg_options');
	if ( isset($_POST['submit']) 
		&& isset($_POST['action']) 
		&& $_POST['action'] == 'mfg_save_options' ) {

			
	    if ( function_exists('current_user_can') && !current_user_can('manage_options') )
	      die(__('Cheatin’ uh?'));
	
	    check_admin_referer(MFG_NONCE);
	
		$options = mfg_save_options( $options );
		$updated = true;
	}
	else {
		$options = get_option('mfg_options');
	}
	if($updated){
		?><div class="updated"><p><strong>Options saved.</strong></p></div><?php
 	}
    ?><div class="wrap" id="mfg-options"><?php
		?><h2>More From Google Options</h2><?php
		?><form method="post" action="<?php echo $_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING']; ?>"><?php
			?><fieldset><?php
				?><input type="hidden" name="action" value="mfg_save_options" /><?php
								
				?><p><label for="mfg_domain" style="font-weight: bold;"><?php _e( 'Google Search Domain:', MFG_DOMAIN ); ?></label><br/><?php
				?><input type="text" name="mfg_domain" value="<?php echo mfg_get_domain(); ?>" style="width: 100%;"/><br/><?php
				?>Change this value if you want to use the results from google in a particular country.  e.g. 'www.google.com.au' for Australia.</p><?php
				
				?><p><label for="mfg_search_inputs" style="font-weight: bold;"><?php _e( 'Google Search Input Fields:', MFG_DOMAIN ); ?></label><br/><?php
				?><textarea name="mfg_search_inputs" rows='15' style="width: 100%"/><?php echo mfg_get_search_inputs(); ?></textarea><br/><?php
				?>These settings should be taken from your Google Adsense account, at the end of the Adsense for Search configuration wizard.<br/><?php
				?><strong>Note:</strong> make sure you only paste the hidden fields starting at 'client' and ending with 'hl'.</p><?php

				?><p><label for="mfg_prefix" style="font-weight: bold;"><?php _e( 'Link Prefix:', MFG_DOMAIN ); ?></label><br/><?php
				?><input type="text" name="mfg_prefix" value="<?php echo htmlspecialchars( mfg_get_prefix() ); ?>" style="width: 100%;"/><br/><?php
				?>This is the text that will be displayed at the beginning of a link to Google search results.</p><?php

				?><p><label for="mfg_suffix" style="font-weight: bold;"><?php _e( 'Link suffix:', MFG_DOMAIN ); ?></label><br/><?php
				?><input type="text" name="mfg_suffix" value="<?php echo htmlspecialchars( mfg_get_suffix() ); ?>" style="width: 100%;"/><br/><?php
				?>This is the text that will be displayed at the end of a link to Google Search results.</p><?php

				?><p><label for="mfg_title_prefix" style="font-weight: bold;"><?php _e( 'Results Title Prefix:', MFG_DOMAIN ); ?></label><br/><?php
				?><input type="text" name="mfg_title_prefix" value="<?php echo htmlspecialchars( mfg_get_title_prefix() ); ?>" style="width: 100%;"/><br/><?php
				?>This is the text that will be displayed at the beginning of a title before the Google search results.</p><?php

				?><p><label for="mfg_title_suffix" style="font-weight: bold;"><?php _e( 'Results Title Suffix:', MFG_DOMAIN ); ?></label><br/><?php
				?><input type="text" name="mfg_title_suffix" value="<?php echo htmlspecialchars( mfg_get_title_suffix() ); ?>" style="width: 100%;"/><br/><?php
				?>This is the text that will be displayed at the end of a title before the Google Search results.</p><?php

				?><p><input type="checkbox" name="mfg_append_to_content" id="mfg_append_to_content" <?php echo mfg_get_append_to_content() ? 'checked="checked"' : ''; ?>" /><?php
				?><label for="mfg_append_to_content" style="font-weight: bold;" onclick="var cb = document.getElementById(this.htmlFor); cb.checked = (cb.checked? true: false); "><?php _e( ' : Automatically append the More from Google link.', MFG_DOMAIN ); ?></label><br/><?php
				?>Check this box to have the More from Google link automatically appended to the content.<br/>
				 Disabling this option allows you to manually place the link elsewhere using the function <code>mfg_link()</code>.</p><?php

				?><p><input type="checkbox" name="mfg_inc_blog_on_search" id="mfg_inc_blog_on_search" <?php echo mfg_get_local_for_search() ? 'checked="checked"' : ''; ?>" /><?php
				?><label for="mfg_inc_blog_on_search" style="font-weight: bold;" onclick="var cb = document.getElementById(this.htmlFor); cb.checked = (cb.checked? true: false); "><?php _e( ' : Include blog results when searching.', MFG_DOMAIN ); ?></label><br/><?php
				?>Uncheck this box if you only want to show Google's results and not the WordPress generated results when someone performs a search on your site.</p><?php

				?><p><input type="checkbox" name="mfg_inc_blog_on_link" id="mfg_inc_blog_on_link" <?php echo mfg_get_local_for_link() ? 'checked="checked"' : ''; ?>" /><?php
				?><label for="mfg_inc_blog_on_link" style="font-weight: bold;" onclick="var cb = document.getElementById(this.htmlFor); cb.checked = (cb.checked? true: false); "><?php _e( ' : Include blog results when using the More from Google link.', MFG_DOMAIN ); ?></label><br/><?php
				?>Check this box if you also want to show WordPress search results as well as Google's results when someone clicks on the More from Google link.</p><?php


				?><p><input type="checkbox" name="mfg_reward_author" id="mfg_reward_author" <?php echo mfg_get_reward_author() ? 'checked="checked"' : ''; ?>" /><?php
				?><label for="mfg_reward_author" style="font-weight: bold;" onclick="var cb = document.getElementById(this.htmlFor); cb.checked = (cb.checked? true: false); "><?php _e( ' : Support the development of ', MFG_DOMAIN ); ?><a href="http://windyroad.org/software/wordpress/more-from-google-plugin">More From Google</a></label><br/><?php
				?>Help support the development of this plugin and others like it.<br/><?php
				?>When this box is checked, approximately 5% of the searches on your
				 site will use <a href="http://windyroad.org">WindyRoad's</a> AdSense client-ID, which
				 help's WindyRoad maintain, support and develop this plugin and others like it.<br/>
				 For those of you who choose to keep this box checked, thank you very much.</p><?php

				mfg_nonce_field();
			?></fieldset><?php
			?><p class="submit"><?php
				?><input type="submit" name="submit" value="Update Options &raquo;" /><?php
			?></p><?php
		?></form><?php
	?></div><?php
}


function mfg_add_admin() {
	// Add a new menu under Options:
	add_options_page('More from Google', 'More from Google', 8, basename(__FILE__), 'mfg_options_page');
}

function mfg_add_js() {
	$url = get_settings('siteurl');
	?><script type="text/javascript" src="<?php echo $url . '/' . MFG_PLUGIN_DIR; ?>/morefromgoogle.js"></script><?php
}


add_action('admin_menu', 'mfg_add_admin'); 		// Insert the Admin panel.
add_action('wp_head', 'mfg_add_js'); 		// Add the script.

if( mfg_get_append_to_content() ) {
	function mfg_append_link($content='') {
		return $content . mfg_get_link();
	}
	add_filter('the_content', 'mfg_append_link');	
}
