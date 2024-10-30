<?php 
/*
Plugin Name: Category Family Tree
Plugin URI: http://www.mtizamohuru.co.tz/category-family-tree/
Description: Show Category ancestors and sub categories on the sidebar of a single post or a page or an achive, highlisht the current subcategory the post is in
Author: Nasibu Njoka 
Version: 1.0
Author URI: http://www.mtizamohuru.co.tz/category-family-tree/
*/

class Category_Family_Tree extends WP_Widget {
        private $p,$q ;
	function Category_Family_Tree() { 
    	$widget_ops = array(
      		'classname' => 'cat-family-tree',
      		'description' => 'Show category Family Links on post & category page only');
		
		/* Widget control settings. */
		$control_ops = array(
			'width' => 250,
			'height' => 250,
			'id_base' => 'cat-family-tree-widget');
		
		/* Create the widget. */
		$this->WP_Widget('cat-family-tree-widget', 'Category Family Tree', $widget_ops, $control_ops );
	}
        
        function getParentCategory_()
        {
           
            $category = get_the_category();
            $current_category = $category[0];
            $parent_category = $current_category->category_parent;

            if ( $parent_category != 0 ) {  
            $this->p = $parent_category ;
            }
            
            $this->q = $current_category->term_id ;
 
        }
	
	function form ($instance) {
		// prints the form on the widgets page
		$defaults = array('title'=>'','count'=>'', 'empty'=>'');
    	$instance = wp_parse_args( (array) $instance, $defaults ); ?>
    	
  <p>
    <label for="<?php echo $this->get_field_id('title'); ?>">Title:</label>
    <input type="text" name="<?php echo $this->get_field_name('title') ?>" id="<?php echo $this->get_field_id('title') ?> " value="<?php echo $instance['title'] ?>" size="20">
  </p>
  <p>
   <input type="checkbox" id="<?php echo $this->get_field_id('empty'); ?>" name="<?php echo $this->get_field_name('empty'); ?>" <?php if ($instance['empty']) echo 'checked="checked"' ?> />
   <label for="<?php echo $this->get_field_id('empty'); ?>">Show Empty Cats ?</label>
  </p>
  <p>
   <input type="checkbox" id="<?php echo $this->get_field_id('count'); ?>" name="<?php echo $this->get_field_name('count'); ?>" <?php if ($instance['count']) echo 'checked="checked"' ?> />
   <label for="<?php echo $this->get_field_id('count'); ?>">Show Counts ?</label>
  </p>

	<?php }
	

	function update ($new_instance, $old_instance) { 
		$instance = $old_instance;
		
		$instance['title'] = $new_instance['title'];
		$instance['empty'] = $new_instance['empty'];
		$instance['count'] = $new_instance['count'];
		
		return $instance;
	}

	function widget ($args,$instance) {
		extract($args);
		
		$title = $instance['title'];
		$count = $instance['count'];
		$empty = $instance['empty'];
 		
 		global $wpdb;
		if(is_category() ) {
			$thiscat = get_term( get_query_var('cat') , 'category' ); 
		} elseif(is_single() ) {
			$aCats = get_the_category();
			$thiscat = $aCats[0];
  		}
  		$category_value = '';
  		if($thiscat) {
  			$args = array();
  			if($empty) {
  				$args['hide_empty'] = false;
  			}
  			$args["parent"] = $thiscat->term_id;
  			$subcategories = get_terms( 'category' , $args);  
  
			if(empty($subcategories) && $thiscat->parent != 0) {  
				$args["parent"] = $thiscat->parent;
    			$subcategories = get_terms( 'category' , $args );  
			}  
  
			$li='';  
			if(!empty($subcategories)){  
    			foreach($subcategories as $subcat):  
                            $this->getParentCategory_();
        			if($thiscat->term_id == $subcat->term_id){
                                $current = ' current-cat';} else {$current = '';}
        			if($subcat->term_id == $this->q):
                                  $li .= '<li class="cat-item cat-item-'.$subcat->term_id.$current.'">';
        			  $li .= '<strong><a href="'.get_category_link( $subcat->term_id ).'" title="'.$subcat->name.'">'.$subcat->name.'</a></strong>';  
        			  else :
                                  $li .= '<li class="cat-item cat-item-'.$subcat->term_id.$current.'">';
        			  $li .= '<a href="'.get_category_link( $subcat->term_id ).'" title="'.$subcat->name.'">'.$subcat->name.'</a>';  
                              endif;
                                if($count) :
        				$li .= ' ('.$subcat->count.')';
        			endif;
        			$li .= '</li>';  	
    			endforeach;  
    			$category_value = "<ul>$li</ul>";  
                        }  
			unset($subcategories,$subcat,$thiscat,$li); 
  		}
  		if(isset($category_value) && $category_value != '') {
  			echo $before_widget; 
  			if(isset($title) && $title != ''): 
  				echo $before_title.$title.$after_title;
                        endif; 
                        echo '<li style="list-style: inside cirle;"><a href="'.get_category_link( $this->p ).'" title="'.get_cat_name($this->p).'">'.get_cat_name($this->p).'</a></li>';  
        		echo '<ul style="margin-left: 1.5em;">'; 
                        echo $category_value; 
                        echo '</ul>' ;
  			echo $after_widget;
  		}
  	}
}

// initiate the widget
function nasznjoka_load_widgets() {
  register_widget('Category_Family_Tree');
}

// register the widget
add_action('widgets_init', 'nasznjoka_load_widgets');

?>