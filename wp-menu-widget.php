<?php
/*
Plugin Name: WP Menu widget
Plugin URI: https://github.com/darkdelphin/wp-menu-widget
Description: Widget that outputs selected menu which was created in Appearance → Menus
Version: 1.2.1
Author: Pavel Burov (Dark Delphin)
Author URI: http://pavelburov.com
*/

class WP_menu_output_widget extends WP_Widget {
    
    //function __construct()
    //{
	//$params = array(
	//	'name' => 'WP Menu widget',
	//   'description' => 'Widget that outputs selected menu which was created in Appearance → Menus' // plugin description that is showed in Widget section of admin panel
	//);
	
	//parent::__construct('WP_menu_output_widget', '', $params);

	

	// add_shortcode( 'wp_menu_output', array($this, 'wp_menu_output_shortcode') );
	// add_filter( 'wp_nav_menu_items', array($this, 'WP_menu_output_widget_custom_menu_item'), 10, 2 );
    //}

    // Constructor
	function WP_menu_output_widget() 
	{

		$params = array(
			'classname' => 'wp_menu_output_widget',
		    'description' => 'Widget that outputs selected menu which was created in Appearance → Menus' // plugin description that is showed in Widget section of admin panel
		);

		// id, name, other parameters
		$this->WP_Widget('wp_menu_output_widget', 'WP Menu widget', $params);

		add_shortcode( 'wp_menu_output', array($this, 'wp_menu_output_shortcode') );
	}

	function update( $new_instance, $old_instance ) {
		
		$instance = $old_instance;

		$instance['title'] = strip_tags($new_instance['title']);
		$instance['output_widget_specific_menu'] = !empty($new_instance['output_widget_specific_menu']) ? 1 : 0;
		$instance['outputmenu'] = strip_tags($new_instance['outputmenu']);
		$instance['loggedmenu'] = strip_tags($new_instance['loggedmenu']);
		$instance['output_subpages'] = !empty($new_instance['output_subpages']) ? 1 : 0;
		$instance['output_page_specific_menu'] = !empty($new_instance['output_page_specific_menu']) ? 1 : 0;

		return $instance;
	}
    
    function form($instance)
    {
		// extract($instance);
	    $instance = wp_parse_args( (array) $instance, array( 'title' => '') );

		$title = esc_attr( $instance['title'] );
		$output_widget_specific_menu = isset( $instance['output_widget_specific_menu'] ) ? (bool) $instance['output_widget_specific_menu'] : false;
		$outputmenu = esc_attr( $instance['outputmenu'] );
		$loggedmenu = esc_attr( $instance['loggedmenu'] );
		$output_subpages = isset( $instance['output_subpages'] ) ? (bool) $instance['output_subpages'] : false;
		$output_page_specific_menu = isset( $instance['output_page_specific_menu'] ) ? (bool) $instance['output_page_specific_menu'] : false;

		
	$menus = get_terms( 'nav_menu', array( 'hide_empty' => true ) );
	?>
	    <!--some html with input fields-->
	    <p>
	    	<label for="<?php echo $this->get_field_id('title'); ?>">Title: </label>
	    	<input type="text" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php if(isset($title)) echo esc_attr($title) ?>"/>
		</p>
	    <p>
	    	<input type="checkbox" id="<?php echo $this->get_field_id('output_widget_specific_menu'); ?>" name="<?php echo $this->get_field_name('output_widget_specific_menu'); ?>" <?php checked( $output_widget_specific_menu ); ?>/>
	    	<label for="<?php echo $this->get_field_id('output_widget_specific_menu'); ?>">Output widget specific menu</label>
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
	    	<input type="checkbox" id="<?php echo $this->get_field_id('output_subpages'); ?>" name="<?php echo $this->get_field_name('output_subpages'); ?>" <?php checked( $output_subpages ); ?>/>
	    	<label for="<?php echo $this->get_field_id('output_subpages'); ?>">Output subpages list</label>
		</p>
		<p>
	    	<input type="checkbox" id="<?php echo $this->get_field_id('output_page_specific_menu'); ?>" name="<?php echo $this->get_field_name('output_page_specific_menu'); ?>" <?php checked( $output_page_specific_menu ); ?>/>
	    	<label for="<?php echo $this->get_field_id('output_page_specific_menu'); ?>">Output page specific menu</label>
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
	@extract($instance);
	
	$title = $instance['title'];
	$output_widget_specific_menu = $instance['output_widget_specific_menu'];
	$outputmenu = $instance['outputmenu'];
	$loggedmenu = $instance['loggedmenu'];
	$output_subpages = $instance['output_subpages'];
	$output_page_specific_menu = $instance['output_page_specific_menu'];

	global $wp_query;
	
	echo $before_widget;
	    if($title) echo $before_title . $title . $after_title;


	    if($output_page_specific_menu)
		{
			$pageid = get_the_ID();

			$meta = get_post_meta($pageid);

			if(is_user_logged_in() && $meta['wpmows_select_logged_menu'][0]) wp_nav_menu( array( 'menu' => $meta['wpmows_select_logged_menu'][0] ) );
			elseif($meta['wpmows_select_guest_menu'][0]) wp_nav_menu( array( 'menu' => $meta['wpmows_select_guest_menu'][0] ) );
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
    	$pageid = get_the_ID();

		$meta = get_post_meta($pageid);

		if(is_user_logged_in() && $meta['wpmows_select_logged_menu'][0]) wp_nav_menu( array( 'menu' => $meta['wpmows_select_logged_menu'][0] ) );
		elseif($meta['wpmows_select_guest_menu'][0]) wp_nav_menu( array( 'menu' => $meta['wpmows_select_guest_menu'][0] ) );
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
        $logged = get_post_meta($post->ID, 'wpmows_select_logged_menu', true);
        $guest = get_post_meta($post->ID, 'wpmows_select_guest_menu', true);

		$menus = get_terms( 'nav_menu', array( 'hide_empty' => true ) );
        ?>
		
		<label for="wpmows_select_menu"><?php echo __('Select guest menu'); ?> </label>
        <select class="widefat" id="wpmows_select_guest_menu" name="wpmows_select_guest_menu">
		<?php
			echo '<option value="">No menu</option>';						

		foreach ($menus as $menu) 
		{
			if(isset($guest) && $guest == $menu->slug) $selected = ' selected="selected"';
			else $selected = '';
			echo '<option value="'.$menu->slug.'"'.$selected.'>'.$menu->name.'</option>';						
		}
		?>
		</select>

        <label for="wpmows_select_menu"><?php echo __('Select logged in menu'); ?> </label>
        <select class="widefat" id="wpmows_select_logged_menu" name="wpmows_select_logged_menu">
		<?php
			echo '<option value="">No menu</option>';						

		foreach ($menus as $menu) 
		{
			if(isset($logged) && $logged == $menu->slug) $selected = ' selected="selected"';
			else $selected = '';
			echo '<option value="'.$menu->slug.'"'.$selected.'>'.$menu->name.'</option>';						
		}
		?>
		</select>
		
        <?php
    }
    
    function update_select_menu($id)
    {
        if(isset($_POST['wpmows_select_logged_menu']))
        {
            update_post_meta($id, 'wpmows_select_logged_menu', strip_tags($_POST['wpmows_select_logged_menu']));
        }
        if(isset($_POST['wpmows_select_guest_menu']))
        {
            update_post_meta($id, 'wpmows_select_guest_menu', strip_tags($_POST['wpmows_select_guest_menu']));
        }
    }
     
}

$copy = new wp_menu_output_widget_support();


?>