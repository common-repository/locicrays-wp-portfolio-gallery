<?php
/**
 * Plugin Name: Locicrays Wp Portfolio & Gallery
 * Version: 1.0
 * Description: LR Portfolio & Gallery is an amazing and powerful WordPress portfolio plugin designed to help you display your portfolio projects.
 * Author: Logicrays
 * Author URI: http://logicrays.com/
 */
 
define('LR_PORTFOLIO_URL', plugins_url('', __FILE__));
ini_set('allow_url_fopen',1);

add_action('admin_menu' , 'lr_portfolio_settings_page');

function lr_portfolio_settings_page() {
 add_submenu_page('edit.php?post_type=lrportfolio', __('LR Free Plugins', 'lr-portfolio'), __('LR Free Plugins', 'lr-portfolio'), 'manage_options', 'lr-free-plugins', 'lr_portfolio_free_plugins_page');
}

function lr_portfolio_free_plugins_page(){
	include_once 'lr-free-plugins.php';
}

include_once 'includes/lr-portfolio-shortcode.php';

add_action("admin_init", "lr_portfolio_fields");
function lr_portfolio_fields()
{
	add_settings_section("section", "All Settings", null, "portfolio-options");	
	add_settings_field("lr_portfolio_layout", "Portfolio Layout", "lr_portfolio_layout_element", "portfolio-options", "section");
	add_settings_field("lr_portfolio_style", "Portfolio Style", "lr_portfolio_style_element", "portfolio-options", "section");
	add_settings_field("lr_portfolio_preview", "Preview Style", "lr_portfolio_preview_element", "portfolio-options", "section");
	add_settings_field("lr_rmore_layout", "Read more link", "lr_portfolio_read_more_element", "portfolio-options", "section");
	add_settings_field("lr_portfolio_custom_css", "Custom css", "lr_portfolio_custom_css_element", "portfolio-options", "section");
	
	register_setting("section", "lr_portfolio_layout");
	register_setting("section", "lr_portfolio_style");
	register_setting("section", "lr_portfolio_preview");
	register_setting("section", "lr_rmore_layout");
	register_setting("section", "lr_portfolio_custom_css");
}
function lrportfolio_style() {
	wp_enqueue_style('bootstrap-min', LR_PORTFOLIO_URL.'/css/bootstrap.min.css');
	wp_enqueue_style('font-awesome-min', LR_PORTFOLIO_URL.'/css/font-awesome.min.css');
	wp_enqueue_style('portfolio-style', LR_PORTFOLIO_URL.'/css/pstyle.css');
	wp_enqueue_script('bootstrap-min', LR_PORTFOLIO_URL.'/js/bootstrap.min.js');
	wp_enqueue_script('isotope', 'http://cdnjs.cloudflare.com/ajax/libs/jquery.isotope/1.5.25/jquery.isotope.min.js');
	wp_enqueue_script('filter', LR_PORTFOLIO_URL.'/js/filter.js');
}
add_action( 'wp_head', 'lrportfolio_style' );

add_action( 'init', 'lr_portfolios' );
function lr_portfolios() {
    $labels = array(
        'name' => 'LR Portfolios',
        'singular_name' => 'LR Portfolio',
        'add_new' => 'Add New',
        'add_new_item' => 'Add New',
        'edit_item' => 'Edit Portfolio',
        'new_item' => 'New Portfolio',
        'view_item' => 'View Portfolio',
        'search_items' => 'Search Portfolios',
        'not_found' =>  'No Portfolios found',
        'not_found_in_trash' => 'No Portfolios in the trash',
		'featured_image' => __( 'Portfolio Image' ),
		'set_featured_image' => __( 'Set Portfolio Image' ),
		'remove_featured_image' => __( 'Remove Portfolio Image' ),
		'use_featured_image' => __( 'Use as Portfolio Image' )
    );
    register_post_type( 'lrportfolio', array(
        'labels' => $labels,
        'public' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'exclude_from_search' => true,
        'query_var' => true,
        'rewrite' => true,
        'capability_type' => 'post',
        'has_archive' => true,
        'hierarchical' => false,
        'menu_position' => 10,
        'supports' => array( 'title', 'thumbnail', 'editor' ),
		'register_meta_box_cb' => 'lr_portfolio_add_metaboxes'
  	) );
}
add_action( 'init', 'lrportfolio_category_tax' );

function lrportfolio_category_tax() {
	register_taxonomy(
		'lrportfolio_category',
		'lrportfolio',
		array(
			'label' => __( 'Portfolio category' ),
			'rewrite' => array( 'slug' => 'lrportfolio_category' ),
			'hierarchical' => true,
		)
	);
}

function lr_portfolio_add_metaboxes(){
 add_meta_box('portfolio_details','Portfolio Details','lr_portfolio_details_callback','lrportfolio','normal','high');
}
add_action('add_meta_boxes', 'lr_portfolio_add_metaboxes');

function lr_portfolio_details_callback( $post ) {
    wp_nonce_field( 'portfolio_field_metabox_nonce', 'portfolio_field_nonce'); ?>
	<?php
		$company_name = get_post_meta( $post->ID, 'company_name', true );
		$project_URL = get_post_meta( $post->ID, 'project_URL', true );
	?>
    <p>
  	<label for="company_name">
    <?php _e('Company Name', 'lr-portfolio' ); ?>
    </label>
    <br/>
    <input type="text" class="widefat" name="company_name" value="<?php echo esc_attr( $company_name ); ?>" />
	</p>
    <p>
      <label for="project_URL">
        <?php _e('Project URL', 'lr-portfolio' ); ?>
      </label>
      <br/>
      <input type="text" class="widefat" name="project_URL" value="<?php echo esc_attr( $project_URL ); ?>" />
    </p>
<?php }
function lr_portfolio_icon_save_meta( $post_id ) {
  if( !isset( $_POST['portfolio_field_nonce'] ) || !wp_verify_nonce( $_POST['portfolio_field_nonce'],'portfolio_field_metabox_nonce') ) 
    return;
  if ( !current_user_can( 'edit_post', $post_id ))
    return;
  if ( isset($_POST['company_name']) ) {        
    update_post_meta($post_id, 'company_name', sanitize_text_field( $_POST['company_name']));      
  }
  if ( isset($_POST['project_URL']) ) {        
    update_post_meta($post_id, 'project_URL', sanitize_text_field( $_POST['project_URL']));      
  }
}
add_action('save_post', 'lr_portfolio_icon_save_meta');

add_image_size('featured_preview', 55, 55, true);

function lr_get_featured_image($post_id) {
    $post_thumbnail_id = get_post_thumbnail_id($post_id);
    if ($post_thumbnail_id) {
        $post_thumbnail_img = wp_get_attachment_image_src($post_thumbnail_id, 'featured_preview');
        return $post_thumbnail_img[0];
    }
}

add_filter( 'manage_lrportfolio_posts_columns', 'set_custom_edit_lrportfolio_columns' );
function set_custom_edit_lrportfolio_columns($columns) {
	unset($columns['date']);
    $columns['lrportfolio_category'] = __( 'Portfolio Category', 'lr-portfolio' );
	$columns['featured_image'] = __( 'Portfolio Image', 'lr-portfolio' );
	$columns['date'] = 'Date';
    return $columns;
}

add_action( 'manage_lrportfolio_posts_custom_column' , 'lr_custom_lrportfolio_column', 10, 2 );
function lr_custom_lrportfolio_column( $column, $post_id ) {
    switch ( $column ) {
        case 'lrportfolio_category' :
            $terms = get_the_term_list( $post_id , 'lrportfolio_category' , '' , ',' , '' );
            if ( is_string( $terms ) )
                echo $terms;
            else
                _e( 'Unable to get category(s)', 'lr-portfolio' );
            break;
		case 'featured_image' :
            $post_featured_image = lr_get_featured_image($post_id);
            if ($post_featured_image)
                echo '<img src="' . $post_featured_image . '" width="55" height="55"/>';
            else
                echo 'No image';
            break;	
	 }
}