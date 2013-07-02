<?php
/*
Plugin Name: WP Menu widget
Plugin URI: 
Description: Widget that outputs selected menu which was created in Appearance → Menus
Version: 1.0
Author: Pavel Burov (Dark Delphin)
Author URI: http://pavelburov.com
*/

class WP_menu_output_widget extends WP_Widget {
    
    function __construct()
    {
	$params = array(
		'name' => 'WP Menu widget',
	    'description' => 'Widget that outputs selected menu which was created in Appearance → Menus' // plugin description that is showed in Widget section of admin panel
	);
	
	parent::__construct('WP_menu_output_widget', '', $params);

	add_shortcode( 'wp_menu_output', array($this, 'wp_menu_output_shortcode') );
	// add_filter( 'wp_nav_menu_items', array($this, 'WP_menu_output_widget_custom_menu_item'), 10, 2 );
    }
    
    function form($instance)
    {
	extract($instance);

		
	$menus = get_terms( 'nav_menu', array( 'hide_empty' => true ) );
	?>
	    <!--some html with input fields-->
	    <p>
	    	<label for="<?php echo $this->get_field_id('title'); ?>">Title: </label>
	    	<input type="text" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php if(isset($title)) echo esc_attr($title) ?>"/>
		</p>
	    <p>
	    	<input type="checkbox" id="<? echo $this->get_field_id('output_widget_specific_menu'); ?>" name="<? echo $this->get_field_name('output_widget_specific_menu'); ?>" value="1" <?php checked( '1', $output_widget_specific_menu ); ?>/>
	    	<label for="<? echo $this->get_field_id('output_widget_specific_menu'); ?>">Output widget specific menu</label>
		</p>
	    <p>
	    	<?php echo __('Show following menu to <b>guest</b> user'); ?>
	    </p>
	    <p>
			<select class="widefat" id="<?php echo $this->get_field_id('outputmenu'); ?>" name="<?php echo $this->get_field_name('outputmenu'); ?>">
				<option value=""><?php echo __('-None-'); ?></option>
				<?php
				foreach ($menus as $menu) 
				{
					if(isset($outputmenu) && $outputmenu == $menu->term_id) $selected = ' selected="selected"';
					else $selected = '';
					echo '<option value="'.$menu->term_id.'"'.$selected.'>'.$menu->name.'</option>';						
				}
				?>
			</select>
		</p>
		<p>
	    	<?php echo __('Show following menu to <b>logged in</b> user'); ?>
	    </p>
		<p>
			<select class="widefat" id="<?php echo $this->get_field_id('loggedmenu'); ?>" name="<?php echo $this->get_field_name('loggedmenu'); ?>">
				<option value=""><?php echo __('-None-'); ?></option>
			<?php
			foreach ($menus as $menu) 
			{
				if(isset($loggedmenu) && $loggedmenu == $menu->term_id) $selected = ' selected="selected"';
				else $selected = '';
				echo '<option value="'.$menu->term_id.'"'.$selected.'>'.$menu->name.'</option>';						
			}
			?>
			</select>
		</p>
		<p>
	    	<input type="checkbox" id="<? echo $this->get_field_id('output_subpages'); ?>" name="<? echo $this->get_field_name('output_subpages'); ?>" value="1" <?php checked( '1', $output_subpages ); ?>/>
	    	<label for="<? echo $this->get_field_id('output_subpages'); ?>">Output subpages list</label>
		</p>
		<p>
	    	<input type="checkbox" id="<? echo $this->get_field_id('output_page_specific_menu'); ?>" name="<? echo $this->get_field_name('output_page_specific_menu'); ?>" value="1" <?php checked( '1', $output_page_specific_menu ); ?>/>
	    	<label for="<? echo $this->get_field_id('output_page_specific_menu'); ?>">Output page specific menu</label>
		</p>
	<?php
    }


	function WP_menu_output_widget_custom_menu_item ( $items, $args ) {
	    // if (is_single() && $args->theme_location == 'primary') {
	        $items .= '<li>Show whatever</li>';
	        $items .= $this->WP_menu_output_widget_get_subpages();
	    // }
	    return $items;
	}

	function WP_menu_output_widget_get_subpages()
	{
		global $wp_query;
		$post = $wp_query->get_queried_object();
		$pageid = get_the_ID();

			$args = array(
					'child_of'     => $pageid,
					'orderby' => 'menu_order',
					'title_li'     => '',
					'echo'	=> 0
				);

			if(!get_pages( $args ) && $post->post_parent) $pageid = $post->post_parent;

			$args = array(
					'child_of'     => $pageid,
					'orderby' => 'menu_order',
					'title_li'     => '',
					'echo'	=> 0
				);
			
			$links = wp_list_pages( $args );

			if(get_pages( $args ))
			{
				$links = wp_list_pages( $args );
			}
			else
			{
				$links = '';
			}
		
		return $links;
	}

    
    function widget($args, $instance)
    {
	extract($args);
	extract($instance);

	global $wp_query;
	
	echo $before_widget;
	    if($title) echo $before_title . $title . $after_title;


	    if($output_page_specific_menu)
 			{
 				$pageid = get_the_ID();

	    		$meta = get_post_meta($pageid);
            	
            	if($meta['wpmows_select_menu'][0])
        		{
        			wp_nav_menu( array( 'menu' => $meta['wpmows_select_menu'][0] ) );	
        		}
 			}
 			
	    if($output_subpages)
	    {
	    	if ( is_page() )//&& $post->post_parent ) 
			{
				?>
				<ul class="menu">
					<?php
	    				echo $this->WP_menu_output_widget_get_subpages();
	    			?>
	    		</ul>
	    		<?php	
			}
	    }

	    if($output_widget_specific_menu)
	    {
	    	if(is_user_logged_in())
		    {
		    	if($loggedmenu != '') wp_nav_menu( array( 'menu' => $loggedmenu ) );
		    }
		    else
		    {
		    	if($loggedmenu != '') wp_nav_menu( array( 'menu' => $outputmenu ) );
		    }
	    }

            
	    
	echo $after_widget;
    }

    function wp_menu_output_shortcode( $atts )
    {
    	extract( shortcode_atts( array(
    	  'loggedmenu' => 'default',
    	  'outputmenu' => 'default',
	      'output_widget_specific_menu' => false,
	      'output_subpages' => false,
	      'output_page_specific_menu' => false
     	), $atts ) );
     	
     	return wp_menu_output( $loggedmenu, $outputmenu, $output_widget_specific_menu, $output_subpages, $output_page_specific_menu);
    }
}

add_action('widgets_init', 'WP_menu_output_widget_register_function');

function WP_menu_output_widget_register_function()
{
    register_widget('WP_menu_output_widget');
}


class wp_menu_output_widget_support {
     
    public $options;
     
    function __construct()
    {
	add_action('plugins_loaded', array($this, 'wpmows_translate'));

	// Plugin functions here
	add_action('add_meta_boxes', array($this,'add_select_menu_metabox'));
    add_action('save_post', array($this, 'update_select_menu'));

    }

    function wpmows_translate()
    {
    // Create 'languages' subdir in plugin dir and put translation files there. File name should be wp_menu_output_widget_support-xx_XX.po ex. wp_menu_output_widget_support-en_US.po 
    load_plugin_textdomain( 'wp_menu_output_widget_support', '', dirname( plugin_basename( __FILE__ ) ) . '/languages' );	
    }
    
    // Input functions *************************************************************************************************************************

    function add_select_menu_metabox()
    {
        // css id, title, cb func, page, priority, cb func args
        add_meta_box('wpmows_select_menu', __('Select menu'), array($this, 'select_menu_func'), 'page');
    }
    // Put this hook into __construct block
    // add_action('add_meta_boxes', array($this,'add_image_link_metabox'));
    
    function select_menu_func($post)
    {
        $val = get_post_meta($post->ID, 'wpmows_select_menu', true);

		$menus = get_terms( 'nav_menu', array( 'hide_empty' => true ) );
        ?>

        <label for="wpmows_select_menu"><?php echo __('Select menu'); ?> </label>
        <select class="widefat" id="wpmows_select_menu" name="wpmows_select_menu">
		<?php
			echo '<option value="">No menu</option>';						

		foreach ($menus as $menu) 
		{
			if(isset($val) && $val == $menu->slug) $selected = ' selected="selected"';
			else $selected = '';
			echo '<option value="'.$menu->slug.'"'.$selected.'>'.$menu->name.'</option>';						
		}
		?>
		</select>

        <?php
    }
    
    function update_select_menu($id)
    {
        if(isset($_POST['wpmows_select_menu']))
        {
            update_post_meta($id, 'wpmows_select_menu', strip_tags($_POST['wpmows_select_menu']));
        }
    }
     
}

    
// add_action('wp_enqueue_scripts', 'wpmows_styles');
// function wpmows_styles()
//     {
// 	wp_register_style('wp_menu_output_widget_support', plugins_url('/css/wp_menu_output_widget_support.css', __FILE__) );
// 	wp_enqueue_style('wp_menu_output_widget_support');
//     }
// add_action('wp_enqueue_scripts', 'wpmows_scripts');
// function wpmows_scripts()
//     {
// 	wp_enqueue_script('wp_menu_output_widget_support', plugins_url('/js/wp_menu_output_widget_support.js', __FILE__), array('jquery'), '1.0', true );
//     }
// function wpaf_register_admin_scripts() 
//     {
//     wp_enqueue_script('wp_menu_output_widget_support', plugins_url('/js/wp_menu_output_widget_support.js', __FILE__), array('jquery'), '1.0', true );
//     }
// add_action( 'admin_enqueue_scripts', 'wpmows_register_admin_scripts' );
$copy = new wp_menu_output_widget_support();


if(!function_exists('wp_menu_output'))
{
	function wp_menu_output( $loggedmenu, $outputmenu, $output_widget_specific_menu = false, $output_subpages = false, $output_page_specific_menu = false)
	{
		$submenu = new WP_menu_output_widget();
		$args = array(
			'loggedmenu' => $loggedmenu,
			'outputmenu' => $outputmenu
			);
		if($output_widget_specific_menu == true) $args['output_widget_specific_menu'] = true;

		if($output_subpages == true) $args['output_subpages'] = true;
		
		if($output_page_specific_menu == true) $args['output_page_specific_menu'] = true;
		
		echo $submenu->widget($args, $instance);
	}
}

?>