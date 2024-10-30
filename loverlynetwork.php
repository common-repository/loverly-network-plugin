<?php
/*
Plugin Name: Lover.ly Network Plugin
Plugin URI: http://lover.ly/
Description: The Lover.ly Network Plugin allows publishing partners to connect their blog to the Lover.ly platform.
Author: Lover.ly
Version: 1.3.1
Author URI: http://lover.ly/
*/

	if (!defined('WP_CONTENT_DIR')) {
		define( 'WP_CONTENT_DIR', ABSPATH.'wp-content');
	}
	if (!defined('WP_CONTENT_URL')) {
		define('WP_CONTENT_URL', get_option('site url').'/wp-content');
	}
	if (!defined('WP_PLUGIN_DIR')) {
		define('WP_PLUGIN_DIR', WP_CONTENT_DIR.'/plugins');
	}
	if (!defined('WP_PLUGIN_URL')) {
		define('WP_PLUGIN_URL', WP_CONTENT_URL.'/plugins');
	}
	$lnVersion = '2.0';
	$lnVendorList = array("-- Select Vendor Type --","Band","Bridal Salon","Bride’s Dress","Bride’s Jewelry","Bride’s Shoes","Bride’s Veil/Hair Accessories","Bridesmaid Accessories", "Bridesmaid Dresses", "Bridesmaid Shoes","Cake Baker","Catering","Ceremony Musicians","Ceremony Officiant","Ceremony Venue","Dessert Table","DJ","Favors","Flowers & Decor","Groom’s Attire","Groomsmen Attire","Hairstylist","Invitations & Stationery","Lighting","Linens","Makeup Artist","Photographer","Reception Venue","Rental Furniture","Portrait Station","Reception Venue","Rental Furniture","Rentals","Wedding Planner/Coordinator","Other");


	function lnInit() {
		global $lnVersion;
		if (!headers_sent() && !session_id()) session_start();
		if (!defined('WP_CONTENT_URL')) define('WP_CONTENT_URL', get_option( 'site url' ) . '/wp-content');
		if (!defined('WP_PLUGIN_URL')) define('WP_PLUGIN_URL', WP_CONTENT_URL . '/plugins');
		add_action('wp_head', 'lnHead', 30);
		add_action('admin_head', 'lnAdminHead');
		add_action('admin_menu', 'lnAddAdmin', 20);
	}
	add_action('init', 'lnInit');
	function lnHead(){
	}
	function lnAdminHead(){
	
	}
	function lnAddAdmin(){
		add_menu_page(__('Lover.ly Network', 'loverlynetwork'), __('Lover.ly Network', 'loverlynetwork'), 'Lover.ly Network', dirname(__FILE__), 'lnSettings');
		add_submenu_page(dirname(__FILE__), __('Settings', 'loverlynetwork'), __('Settings', 'loverlynetwork'), 'manage_options', 'lnSettings', 'lnSettings');
	}
	
	function lnPostFooter($content){
		global $post;
		$loverlynetwork_style = get_option('loverlynetwork_style');
		$loverlynetwork_format_type = get_option('loverlynetwork_format_type');
		$loverlynetwork_format_name = get_option('loverlynetwork_format_name');
		if( $vendors = get_post_meta($post->ID, '_loverlynetwork_vendors', false) ){
			$vendors = unserialize( $vendors[0] );
			$output = "";
			if( count($vendors) ){
				$i = 1;
				$output = "<div class='vendor_section'>";
				foreach($vendors as $vendor){
					if( empty($vendor['name']) ) continue;
					$vendor['name'] = stripslashes($vendor['name']);
					switch($loverlynetwork_style){
						case "inline":	$output .= '<span class="vendor_feature">';	break;
						case "block":	$output .= '<div class="vendor_feature">';	break;
						default:		$output .= '<div class="vendor_feature">';	break;
					}
					switch($loverlynetwork_format_type){	
						case 'bold':	$output .= '<span style="font-weight:bold;">';	break;
						case 'italic':	$output .= '<em>';	break;
					}
					$output .= stripslashes($vendor['type']).': ';
					switch($loverlynetwork_format_type){
						case 'bold':	$output .= '</span>';	break;
						case 'italic':	$output .= '</em>';	break;
						default:		$output .= '</a>';	break;
					}
					switch($loverlynetwork_format_name){
						case 'bold':	$output .= '<a href="'.$vendor['link'].'" style="font-weight:bold;" target="_blank">';	break;
						case 'italic':	$output .= '<a href="'.$vendor['link'].'" target="_blank"><em>';	break;
						default:		$output .= '<a href="'.$vendor['link'].'" target="_blank">';	break;
					}
					$output .= $vendor['name'];
					switch($loverlynetwork_format_name){
						case 'bold':	$output .= '</a>';	break;
						case 'italic':	$output .= '</em></a>';	break;
						default:		$output .= '</a>';	break;
					}
					switch($loverlynetwork_style ){
						case "inline":	
							if($i < count($vendors)){
								$output .= '</span> / ' ;	
								break;
							} else {
								$output .= '</span>' ;	
								break;
							}
						case "block":	$output .= '</div>';	break;
						default:		$output .= '</div>';	break;
					}
					
					$i++;		
				}
				$output .= "</div>";
				$content = $content.$output;
			}
		}
		return $content;
	}
	add_action('the_content', 'lnPostFooter');
	add_filter('the_content_feed', 'lnPostFooter');
	
// add_action( 'add_meta_boxes', 'lnMetaBox' );
	add_action( 'admin_init', 'lnMetaBox', 1 );
	add_action( 'save_post', 'lnMetaSave' );	
	
	function lnMetaBox(){
	    add_meta_box( 
	        'loverlynetwork_sectionid',
	        __( 'Lover.ly Network', 'loverlynetwork_textdomain' ),
	        'lninner_custom_box',
	        'post' 
	    );
	}
	function lninner_custom_box( $post) {
		global $lnVendorList;
		if( $vendors = get_post_meta($post->ID, '_loverlynetwork_vendors', false) ){
			$vendors = unserialize( $vendors[0] );
		}
		
		if (count($vendors) <= 9) {
			$total = 9;
		}
		else
		{
			$total = count($vendors) - 1;
		}
?>
		<table id="vendor_table" width=100% cellpadding="5" cellspacing="5">
<?php
		for($i = 0;$i <= ($total);$i++){
			if( empty($vendors[$i]['name']) ){
				$vendors[$i]['type'] = '';
			}
			$vendors[$i]['name'] = stripslashes($vendors[$i]['name']);
			$vendors[$i]['type'] = stripslashes($vendors[$i]['type']);
?>
			<tr>
				<td style="padding-bottom:10px;"><label for="lnvendors[<?php echo $i?>][type]">Vendor Type:</label></td>
				<td style="padding-bottom:10px;"><label for="lnvendors[<?php echo $i?>][name]">Name:</label></td>
				<td style="padding-bottom:10px;"><label for="lnvendors[<?php echo $i?>][link]">Link:</label></td>
			</tr>
			<tr>
				<td style="padding-bottom:10px;">
<?php
		$inList = true;
		if( !empty($vendors[$i]['type']) ){
			$inList = false;
			foreach($lnVendorList as $item){
				if($item == $vendors[$i]['type'] ) $inList = true;
			}
		}
		if ($inList){
?>
					<select class="vendortypes" name="lnvendors[<?php echo $i?>][type]" style="width:175px;">
<?php
					foreach($lnVendorList as $item){
						$sel = "";
						if($item == $vendors[$i]['type'] ) $sel = " SELECTED ";
						echo "<option {$sel}>".$item."</option>";
					}
?>
					</select>
<?php	}else{	?>
					<input type="text" class="vendortypesi" name="lnvendors[<?php echo $i?>][type]" value="<?php echo $vendors[$i]['type'];?>"  style="width:200px;" />
<?php	}	?>
				</td>
				<td style="padding-bottom:10px;">
					<input type="text" name="lnvendors[<?php echo $i?>][name]" value="<?php echo ($vendors[$i]['name']);?>"  style="width:200px;" />
				</td>
				<td style="padding-bottom:10px;">
					<input type="text" name="lnvendors[<?php echo $i?>][link]" value="<?php echo $vendors[$i]['link'];?>"  style="width:200px;" />
				</td>
			</tr>
			<?php
		}
?>		
		</table>
		<p id="add_more" style="padding-left:20px; width:120px; display:block; height:20px; "><u><b><a href="" onClick="addrows(); return false;">Add More Vendors</a></b></u></p>
		<script>
			
			function addrows()
			{
				var new_table = document.getElementById("vendor_table");
				var rowCount = new_table.rows.length;
				var count = rowCount/2;
				for(var i = count; i< count+5; i++){
					var rowCount = new_table.rows.length;
					var row = new_table.insertRow(rowCount);
					
					var cell1 = row.insertCell(0);
					cell1.innerHTML = "<label for='lnvendors["+i+"][type]'>Vendor Type:</label>";
				
					var cell2 = row.insertCell(1);
					cell2.innerHTML = "<label for='lnvendors["+i+"][name]'>Name:</label>";
				
					var cell3 = row.insertCell(2);
					cell3.innerHTML = "<label for='lnvendors["+i+"][link]'>Link:</label>";
				
					var nextrowCount = new_table.rows.length;
					var nextrow = new_table.insertRow(nextrowCount);
				
					var cell4 = nextrow.insertCell(0);
					cell4.innerHTML ="<select class='vendortypes' name='lnvendors["+i+"][type]' style='width:175px;''><?php
										foreach($lnVendorList as $item){
											$sel = "";
											if($item == $vendors["+i+"]['type'] ) $sel = ' SELECTED ';
											echo '<option {$sel}>'.$item.'</option>';
										}
					?></select>";
				
					var cell5 = nextrow.insertCell(1);
					cell5.innerHTML ="<input type='text' name='lnvendors["+i+"][name]' value='<?php echo ($vendors["+i+"]['name']);?>'  style='width:200px;' />"
				
					var cell6 = nextrow.insertCell(2);
					cell6.innerHTML ="<input type='text' name='lnvendors["+i+"][link]' value='<?php echo ($vendors["+i+"]['link']);?>'  style='width:200px;' />"				
				}
				jQuery("td").css("padding-bottom", "10px"); 
			}			
		
			jQuery(".vendortypes").live('change', function() {
				if( jQuery(this).val() == "Other" ){
					var name = jQuery(this).attr("name");
					var html = '<input type="text" class="vendortypesi" name="'+name+'" value=""  style="width:200px;" />';
					jQuery(this).parent().html( html );
				}
			});
			jQuery(".vendortypesi").live('blur', function() {
				if( jQuery(this).val() == "" ){
					var name = jQuery(this).attr("name");
					var html = '<select class="vendortypes" name="'+name+'" style="width:175px;">';
<?php
					foreach($lnVendorList as $item){
						$sel = "";
						if($item == $vendors[$i]['type'] ) $sel = " SELECTED ";
						echo "html += '<option {$sel}>".$item."</option>';";
					}
?>
					html += '</select>';
					jQuery(this).parent().html( html );
				}
			});
		</script>
<?php
	}
	function lnMetaSave(){
		global $post_id;
//		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
//		if ( !wp_verify_nonce( $_POST['loverlynetwork_noncename'], plugin_basename( __FILE__ ) ) )	return;
		if ( 'page' == $_POST['post_type'] ) {
			if ( !current_user_can( 'edit_page', $post_id ) )	return;
		}else{
			if ( !current_user_can( 'edit_post', $post_id ) )	return;
		}
		$loverlynetwork_vendors = array();
		if( count($_POST['lnvendors']) ){
			foreach($_POST['lnvendors'] as $k=>$v){
				if( empty($v['name']) ) continue;
				$v['name']=str_replace(array('&','"','\'','<','>',"\t",),array('&amp;','&quot;','&#039;','&lt;','&gt;','&nbsp;&nbsp;'),$v['name']);
				$loverlynetwork_vendors[$k] = $v;
			}
		}
		update_post_meta($post_id, '_loverlynetwork_vendors', str_replace('\\','\\\\',serialize($loverlynetwork_vendors)));
	}
	
	function lnSettings(){
//		Manage Settings
		if(isset($_POST['saveit'])){
			if (isset($_POST['loverlynetwork_style']))
				update_option('loverlynetwork_style', $_POST['loverlynetwork_style']);
			if (isset($_POST['loverlynetwork_format_type']))
				update_option('loverlynetwork_format_type', $_POST['loverlynetwork_format_type']);
			if (isset($_POST['loverlynetwork_format_name']))
				update_option('loverlynetwork_format_name', $_POST['loverlynetwork_format_name']);
		}
		$loverlynetwork_style = get_option('loverlynetwork_style');
		$loverlynetwork_format_type = get_option('loverlynetwork_format_type');
		$loverlynetwork_format_name = get_option('loverlynetwork_format_name');
?>
<style>
a.info{position:relative;z-index:24;}
a.info:hover {z-index:25;text-decoration:none;}
a.info span {display: none;}
a.info:hover span {display:block;position:absolute;top:1em;left:0;width:350px;border:1px solid #000;background-color:#ccc;colour:#000;padding:4px;}
</style>
<div class="wrap">
    <h2>Lover.ly Network Settings</h2>
    <form method="post" action="" id="ap_conf">
    <input type="hidden" name="saveit" value="1" />
    <table class="form-table">
    <tr valign="top">
        <th scope="row"><label for="loverlynetwork_style">Display using block format or inline?</label></th>
        <td>
            <select name="loverlynetwork_style">
                <option value="block" <?php if($loverlynetwork_style == "block") echo " selected "; ?>>Block</option>
                <option value="inline" <?php if($loverlynetwork_style == "inline") echo " selected "; ?>>Inline</option>
            </select>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row"><label for="loverlynetwork_style">How do you want to style your vendor type?</label></th>
        <td>
            <select name="loverlynetwork_format_type">
                <option value="" <?php if($loverlynetwork_format_type == "") echo " selected "; ?>>None</option>
                <option value="bold" <?php if($loverlynetwork_format_type == "bold") echo " selected "; ?>>Bold</option>
                <option value="italic" <?php if($loverlynetwork_format_type == "italic") echo " selected "; ?>>Italics</option>
            </select>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row"><label for="loverlynetwork_style">How do you want to style your links?</label></th>
        <td>
            <select name="loverlynetwork_format_name">
                <option value="" <?php if($loverlynetwork_format_name == "") echo " selected "; ?>>None</option>
                <option value="bold" <?php if($loverlynetwork_format_name == "bold") echo " selected "; ?>>Bold</option>
                <option value="italic" <?php if($loverlynetwork_format_name == "italic") echo " selected "; ?>>Italics</option>
            </select>
        </td>
    </tr>
    </table>
    <br />
    <input type="submit" value="Save Settings" class="button" />
    </form>
<?php
	}
?>