<?php
add_shortcode( 'LRPORTFOLIOS', 'lr_portfolio_shortcode' );

function lr_portfolio_shortcode($atts) {
ob_start();
?>
<?php
$post_type = 'lrportfolio';
// Get all the taxonomies for this post type
$taxonomies = get_object_taxonomies( (object) array( 'post_type' => $post_type ) );
?>
<div class="lr_portfolio">
<div id="filters" class="button-group text-center">
<button class="btn btn-primary" data-filter="*">Show all</button>
<?php
foreach( $taxonomies as $taxonomy ) : 
// Gets every "category" (term) in this taxonomy to get the respective posts
$terms = get_terms( $taxonomy );
foreach( $terms as $term ) : 
?>
<button class="btn btn-primary" data-filter=".<?php echo $term->slug; ?>"><?php echo $term->name; ?></button>
<?php
endforeach;
endforeach;
?>
<div class="container-fluid no-gutter">
<div id="lrportfolio" class="row">
<?php
$args = array('post_type' => 'lrportfolio', 'posts_per_page' => -1, 'order' => 'DESC' );
$loop = new WP_Query( $args );
while ( $loop->have_posts() ) : $loop->the_post();

$terms = get_the_terms( $loop->ID, 'lrportfolio_category' );
if ( !empty( $terms ) ){
    // get the first term
    $term = array_shift( $terms );

$company_name = get_post_meta( get_the_id(), 'company_name', true );
$project_URL = get_post_meta( get_the_id(), 'project_URL', true );

$featured_img_url = get_the_post_thumbnail_url(get_the_ID(),'full');
?>
<div id="<?php echo get_the_ID(); ?>" class="item <?php echo $term->slug; ?> col-sm-4 single-portfolio">
<div class="item-wrap"> 
<img class="img-responsive" src="<?php echo esc_url($featured_img_url); ?>"> 
<div class="middle">
<div class="text">
<a href="jayscript:void(0);" data-toggle="modal" data-target="#myModal_<?php echo get_the_ID(); ?>">Read more</a>
</div>
</div>
</div>
</div>

<div class="modal fade" id="myModal_<?php echo get_the_ID(); ?>" role="dialog">
  <div class="modal-dialog"> 
    <!-- Modal content-->
    <div class="modal-content">
	  <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"><?php echo get_the_title(get_the_ID()); ?></h4>
      </div>
      <div class="modal-body">
      	<img class="img-responsive" src="<?php echo esc_url($featured_img_url); ?>" /> 
        <p><strong>Company name: </strong><?php echo $company_name; ?></p>
        <p><strong>Project Url: </strong><a href="<?php echo $project_URL; ?>"><?php echo $project_URL; ?></a></p>
        <?php echo get_the_content(get_the_ID()); ?>        
      </div>
    </div>
  </div>
</div>
<?php 
}
endwhile; 
wp_reset_query();
?>      
</div>
</div>
</div>
</div>
<?php 
return ob_get_clean();
}