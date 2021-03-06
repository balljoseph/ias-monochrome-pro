<?php
/**
 * Monochrome Pro.
 *
 * This file adds functions to the Monochrome Pro Theme.
 *
 * @package Monochrome
 * @author  StudioPress
 * @license GPL-2.0+
 * @link    https://my.studiopress.com/themes/monochrome/
 */

// Start the engine.
include_once( get_template_directory() . '/lib/init.php' );

// Setup Theme.
include_once( get_stylesheet_directory() . '/lib/theme-defaults.php' );

// Set Localization (do not remove).
add_action( 'after_setup_theme', 'monochrome_localization_setup' );
function monochrome_localization_setup(){

	load_child_theme_textdomain( 'monochrome-pro', get_stylesheet_directory() . '/languages' );

}

// Add the theme helper functions.
include_once( get_stylesheet_directory() . '/lib/helper-functions.php' );

// Add Image upload and Color select to WordPress Theme Customizer.
require_once( get_stylesheet_directory() . '/lib/customize.php' );

// Include Customizer CSS.
include_once( get_stylesheet_directory() . '/lib/output.php' );

// Add WooCommerce support.
// include_once( get_stylesheet_directory() . '/lib/woocommerce/woocommerce-setup.php' );

// Include the Customizer CSS for the WooCommerce plugin.
// include_once( get_stylesheet_directory() . '/lib/woocommerce/woocommerce-output.php' );

// Include notice to install Genesis Connect for WooCommerce.
// include_once( get_stylesheet_directory() . '/lib/woocommerce/woocommerce-notice.php' );

// Child theme (do not remove).
define( 'CHILD_THEME_NAME', 'Monochrome Pro' );
define( 'CHILD_THEME_URL', 'https://my.studiopress.com/themes/monochrome/' );
define( 'CHILD_THEME_VERSION', '1.0.0' );

// Enqueue scripts and styles.
add_action( 'wp_enqueue_scripts', 'monochrome_enqueue_scripts_styles' );
function monochrome_enqueue_scripts_styles() {
// Add Anton font instead of Open Sans
	wp_enqueue_style( 'monochrome-fonts', 'https://fonts.googleapis.com/css?family=Anton', array(), CHILD_THEME_VERSION );
	wp_enqueue_style( 'monochrome-ionicons', '//code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css', array(), CHILD_THEME_VERSION );

	wp_enqueue_script( 'monochrome-global-script', get_stylesheet_directory_uri() . '/js/global.js', array( 'jquery' ), '1.0.0', true );

	$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
	wp_enqueue_script( 'monochrome-responsive-menu', get_stylesheet_directory_uri() . '/js/responsive-menus' . $suffix . '.js', array( 'jquery' ), CHILD_THEME_VERSION, true );
	wp_localize_script( 'monochrome-responsive-menu', 'genesis_responsive_menu', monochrome_responsive_menu_settings() );

}


// Define our responsive menu settings.
function monochrome_responsive_menu_settings() {

	$settings = array(
		'mainMenu'         => __( 'Menu', 'monochrome-pro' ),
		'menuIconClass'    => 'ionicons-before ion-navicon',
		'subMenu'          => __( 'Submenu', 'monochrome-pro' ),
		'subMenuIconClass' => 'ionicons-before ion-chevron-down',
		'menuClasses'      => array(
			'combine' => array( ),
			'others'  => array(
				'.nav-primary',
			),
		),
	);

	return $settings;

}

// Add HTML5 markup structure.
add_theme_support( 'html5', array( 'caption', 'comment-form', 'comment-list', 'gallery', 'search-form' ) );

// Add Accessibility support.
add_theme_support( 'genesis-accessibility', array( '404-page', 'drop-down-menu', 'headings', 'rems', 'search-form', 'skip-links' ) );

// Add viewport meta tag for mobile browsers.
add_theme_support( 'genesis-responsive-viewport' );

// Add support for custom header.
add_theme_support( 'custom-header', array(
	'width'           => 320,
	'height'          => 120,
	'header-selector' => '.site-title a',
	'header-text'     => false,
	'flex-height'     => true,
	'flex-width'     => true,
) );

// Add support for after entry widget.
add_theme_support( 'genesis-after-entry-widget-area' );

// Remove after entry widget
remove_action( 'genesis_after_entry', 'genesis_after_entry_widget_area' );

// Add after entry widget to posts and pages
add_action( 'genesis_after_entry', 'custom_after_entry', 9 );
function custom_after_entry() {

   if ( ! is_singular( array( 'post', 'page','talk' )) )
        return;

        genesis_widget_area( 'after-entry', array(
            'before' => '<div class="after-entry widget-area">',
            'after'  => '</div>',
        ) );

}


// Add image sizes.
add_image_size( 'front-blog', 960, 540, TRUE );
add_image_size( 'sidebar-thumbnail', 80, 80, TRUE );

// Remove header right widget area.
unregister_sidebar( 'header-right' );

// Remove secondary sidebar.
unregister_sidebar( 'sidebar-alt' );

// Remove site layouts.
genesis_unregister_layout( 'content-sidebar-sidebar' );
genesis_unregister_layout( 'sidebar-content-sidebar' );
genesis_unregister_layout( 'sidebar-sidebar-content' );

// Remove output of primary navigation right extras.
remove_filter( 'genesis_nav_items', 'genesis_nav_right', 10, 2 );
remove_filter( 'wp_nav_menu_items', 'genesis_nav_right', 10, 2 );

// Remove navigation meta box.
add_action( 'genesis_theme_settings_metaboxes', 'monochrome_remove_genesis_metaboxes' );
function monochrome_remove_genesis_metaboxes( $_genesis_theme_settings_pagehook ) {

	remove_meta_box( 'genesis-theme-settings-nav', $_genesis_theme_settings_pagehook, 'main' );

}

// Register navigation menus.
add_theme_support( 'genesis-menus', array( 'primary' => __( 'Header Menu', 'monochrome-pro' ) ) );

// Reposition primary navigation menu.
remove_action( 'genesis_after_header', 'genesis_do_nav' );
add_action( 'genesis_header', 'genesis_do_nav', 12 );

// Reposition secondary navigation menu.
remove_action( 'genesis_after_header', 'genesis_do_subnav' );
add_action( 'genesis_after', 'genesis_do_subnav', 12 );

// Add the search icon to the header if the option is set in the Customizer.
add_action( 'genesis_meta', 'monochrome_add_search_icon' );
function monochrome_add_search_icon() {

	$show_icon = get_theme_mod( 'monochrome_header_search', monochrome_customizer_get_default_search_setting() );

	// Exit early if option set to false.
	if ( ! $show_icon ) {
		return;
	}

	add_action( 'genesis_header', 'monochrome_do_header_search_form', 14 );
	add_filter( 'genesis_nav_items', 'monochrome_add_search_menu_item', 10, 2 );
	add_filter( 'wp_nav_menu_items', 'monochrome_add_search_menu_item', 10, 2 );

}

// Function to modify the menu item output of the Header Menu.
function monochrome_add_search_menu_item( $items, $args ) {

	$search_toggle = sprintf( '<li class="menu-item">%s</li>', monochrome_get_header_search_toggle() );

	if ( 'primary' === $args->theme_location ) {
		$items .= $search_toggle;
	}

	return $items;

}

// Reduce secondary navigation menu to one level depth.
add_filter( 'wp_nav_menu_args', 'monochrome_secondary_menu_args' );
function monochrome_secondary_menu_args( $args ) {

	if ( 'secondary' != $args['theme_location'] ) {
		return $args;
	}

	$args['depth'] = 1;

	return $args;

}

// Modify Gravatar size in author box.
add_filter( 'genesis_author_box_gravatar_size', 'monochrome_author_box_gravatar' );
function monochrome_author_box_gravatar( $size ) {

	return 90;

}

// Customize entry meta in entry header.
// remove_action( 'genesis_before_post_content', 'genesis_post_info' );
// remove_action( 'genesis_after_post_content', 'genesis_post_meta' );
// add_filter( 'genesis_post_info', 'monochrome_entry_meta_header' );
// function monochrome_entry_meta_header( $post_info ) {

// 	$post_info = '[post_date format="M j, Y"] &middot; [post_comments] [post_edit]';

// 	return $post_info;

// }

// Customize entry meta in entry footer.
// add_filter( 'genesis_post_meta', 'monochrome_entry_meta_footer' );
// function monochrome_entry_meta_footer( $post_meta ) {

// 	$post_meta = 'IASummit [post_categories before=""], subject [post_tags before=""]';

// 	return $post_meta;

// }

// Modify Gravatar size in entry comments.
add_filter( 'genesis_comment_list_args', 'monochrome_comments_gravatar' );
function monochrome_comments_gravatar( $args ) {

	$args['avatar_size'] = 48;

	return $args;

}

// Setup widget counts.
function monochrome_count_widgets( $id ) {

	$sidebars_widgets = wp_get_sidebars_widgets();

	if ( isset( $sidebars_widgets[ $id ] ) ) {
		return count( $sidebars_widgets[ $id ] );
	}

}

// Calculate widget count.
function monochrome_widget_area_class( $id ) {

	$count = monochrome_count_widgets( $id );

	$class = '';

	if ( $count == 1 ) {
		$class .= ' widget-full';
	} elseif ( $count % 3 == 1 ) {
		$class .= ' widget-thirds';
	} elseif ( $count % 4 == 1 ) {
		$class .= ' widget-fourths';
	} elseif ( $count % 2 == 0 ) {
		$class .= ' widget-halves uneven';
	} else {
		$class .= ' widget-halves';
	}

	return $class;

}


// Customize content limit read more link markup.
add_filter( 'get_the_content_limit', 'monochrome_content_limit_read_more_markup', 10, 3 );
function monochrome_content_limit_read_more_markup( $output, $content, $link ) {

	$output = sprintf( '<p>%s &#x02026;</p><p class="more-link-wrap">%s</p>', $content, str_replace( '&#x02026;', '', $link ) );

	return $output;
}

// Remove entry meta in entry footer.
// remove_action( 'genesis_entry_footer', 'genesis_entry_footer_markup_open', 5 );
// remove_action( 'genesis_entry_footer', 'genesis_post_meta' );
// remove_action( 'genesis_entry_footer', 'genesis_entry_footer_markup_close', 15 );

// Hook before footer CTA widget area.
add_action( 'genesis_before_footer', 'monochrome_before_footer_cta' );
function monochrome_before_footer_cta() {

	genesis_widget_area( 'before-footer-cta', array(
		'before' => '<div class="before-footer-cta"><div class="wrap">',
		'after'  => '</div></div>',
	) );

}

// Add Footer widgets
genesis_register_sidebar(array(
	'id' => 'footer-left',
    'name'=>'Footer Left',
    'description' => 'This is the first column of the footer section.',
    // 'before_title'=>'<h4 class="widgettitle">','after_title'=>'</h4>'
));
genesis_register_sidebar(array(
	'id' => 'footer-right',
    'name'=>'Footer Right',
    'description' => 'This is the second column of the footer section.',
    // 'before_title'=>'<h4 class="widgettitle">','after_title'=>'</h4>'
));


// Remove site footer.
remove_action( 'genesis_footer', 'genesis_footer_markup_open', 5 );
remove_action( 'genesis_footer', 'genesis_do_footer' );
remove_action( 'genesis_footer', 'genesis_footer_markup_close', 15 );

// Add site footer after page, customize.
add_action( 'genesis_after', 'genesis_footer_markup_open', 5 );
add_action( 'genesis_after', 'do_custom_footer' );
add_action( 'genesis_after', 'genesis_footer_markup_close', 15 );

function do_custom_footer() { ?>
	<div id="footer-widgetized">
	    <div class="wrap">
	        <div class="footer-col1">
	            <?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('Footer Left') ) : ?>
	                <h4><?php _e("Footer Left Widget", 'genesis'); ?></h4>
	                <p><?php _e("This is an example of a widgeted area. You can add content to this area by visiting your Widgets Panel and adding new widgets to this area.", 'genesis'); ?></p>
	            <?php endif; ?>
	        </div><!-- end .footer-col1 -->
	        <div class="footer-col2">
	            <?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('Footer Right') ) : ?>
	                <h4><?php _e("Footer Right Widget", 'genesis'); ?></h4>
	                <p><?php _e("This is an example of a widgeted area. You can add content to this area by visiting your Widgets Panel and adding new widgets to this area.", 'genesis'); ?></p>
	            <?php endif; ?>
	        </div><!-- end .footer-col2 -->

	    </div><!-- end .wrap -->
	</div><!-- end #footer-widgetized -->
	<?
}


// Remove "You are Here" from breadcrumbs
add_filter('genesis_breadcrumb_args', 'change_breadcrumbs_text');
function change_breadcrumbs_text( $args ) {
    $args['labels']['prefix'] = '';
    return $args;
}



// Register widget areas.

genesis_register_sidebar( array(
	'id'          => 'front-page-1',
	'name'        => __( 'Front Page 1', 'monochrome-pro' ),
	'description' => __( 'This is the front page 1 image section.', 'monochrome-pro' ),
) );
genesis_register_sidebar( array(
	'id'          => 'front-page-2',
	'name'        => __( 'Front Page 2', 'monochrome-pro' ),
	'description' => __( 'This is the front page 2 email sign-up section.', 'monochrome-pro' ),
) );
genesis_register_sidebar( array(
	'id'          => 'front-page-3',
	'name'        => __( 'Front Page 3', 'monochrome-pro' ),
	'description' => __( 'This is the front page 3 countdown image section.', 'monochrome-pro' ),
) );
genesis_register_sidebar( array(
	'id'          => 'front-page-4',
	'name'        => __( 'Front Page 4', 'monochrome-pro' ),
	'description' => __( 'This is the front page 4 section.', 'monochrome-pro' ),
) );

genesis_register_sidebar( array(
	'id'          => 'front-page-5',
	'name'        => __( 'Front Page 5', 'monochrome-pro' ),
	'description' => __( 'This is the front page 5 image section.', 'monochrome-pro' ),
) );

genesis_register_sidebar( array(
	'id'          => 'front-page-6',
	'name'        => __( 'Front Page 6', 'monochrome-pro' ),
	'description' => __( 'This is the front page 6 sponsor section.', 'monochrome-pro' ),
) );

genesis_register_sidebar( array(
	'id'          => 'front-page-7',
	'name'        => __( 'Front Page 7', 'monochrome-pro' ),
	'description' => __( 'This is the front page 7 last-call CTA section.', 'monochrome-pro' ),
) );

genesis_register_sidebar( array(
	'id'          => 'before-footer-cta',
	'name'        => __( 'Before-Footer CTA', 'monochrome-pro' ),
	'description' => __( 'This is the call-to-ation area placed before the footer.', 'monochrome-pro' ),
) );


/****************
*****************
Copy the following functions into any new functions.php if you change the theme!
*****************
*****************

/**
* The Events Calendar - Bypass Genesis genesis_do_post_content in Event Views
 * This snippet overrides the Genesis Content Archive settings for Event Views
 * Event Template set to: Admin > Events > Settings > Display Tab > Events template > Default Page Template
 * The Events Calendar @4.0.4
 * Genesis @2.2.6
*/
add_action( 'get_header', 'tribe_genesis_bypass_genesis_do_post_content' );
function tribe_genesis_bypass_genesis_do_post_content() {
 
    if ( class_exists( 'Tribe__Events__Main' ) && class_exists( 'Tribe__Events__Pro__Main' ) ) {
        if ( tribe_is_month() || tribe_is_upcoming() || tribe_is_past() || tribe_is_day() || tribe_is_map() || tribe_is_photo() || tribe_is_week() || ( tribe_is_recurring_event() && ! is_singular( 'tribe_events' ) ) ) {
            remove_action( 'genesis_entry_content', 'genesis_do_post_image', 8 );
            remove_action( 'genesis_entry_content', 'genesis_do_post_content' );
            add_action( 'genesis_entry_content', 'the_content', 15 );
        }
    } elseif ( class_exists( 'Tribe__Events__Main' ) && ! class_exists( 'Tribe__Events__Pro__Main' ) ) {
        if ( tribe_is_month() || tribe_is_upcoming() || tribe_is_past() || tribe_is_day() ) {
            remove_action( 'genesis_entry_content', 'genesis_do_post_image', 8 );
            remove_action( 'genesis_entry_content', 'genesis_do_post_content' );
            add_action( 'genesis_entry_content', 'the_content', 15 );
        }
    }
 
}

/**
 * Sets the default date for Tribe Event Calendar
 *
 * Expects to be called during tribe_events_pre_get_posts. Note that this
 * function modifies $_REQUEST - this is needed for consistency because
 * various parts of TEC inspect that array directly to determine the current
 * date.
 * 
 * @param WP_Query $query
 */
function tribe_force_event_date( WP_Query $query ) {
    // Don't touch single posts or queries other than the main query
    if ( ! $query->is_main_query() || is_single() ) {
        return;
    }
    // If a date has already been set by some other means, bail out
    if ( strlen( $query->get( 'eventDate' ) ) || ! empty( $_REQUEST['tribe-bar-date'] ) ) {
        return;
    }
    // Change this to whatever date you prefer
    $default_date = '2018-03-21';
    // Use the preferred default date
    $query->set( 'eventDate', $default_date );
    $query->set( 'start_date', $default_date );
    // $_REQUEST['tribe-bar-date'] = $default_date;
}
add_action( 'tribe_events_pre_get_posts', 'tribe_force_event_date' ); 

add_filter( 'tribe-events-bar-filters',  'remove_date_from_bar', 1000, 1 );
 
function remove_date_from_bar( $filters ) {
  if ( isset( $filters['tribe-bar-date-filter'] ) ) {
        unset( $filters['tribe-bar-date-filter'] );
    }
 
    return $filters;
}


/* Output filter for my_date in Pods posts
   Use this against a date field in your Pods Fields like so:
   {@post_date,my_date}

   The Function below should be in your functions.php
*/

function my_date($input_date) {
     return date("F j, Y", strtotime($input_date));         
}

// Remove link in TEC event categories
add_filter( 'tribe_get_event_categories', 'display_but_not_link_event_cats' );
function display_but_not_link_event_cats( $html ) {
	$new_html = preg_replace( '/<a href=\"(.*?)\">(.*?)<\/a>/', "\\2", $html ); // from http://www.stumiller.me/code-snippet-strip-links-from-string-in-php/

	return $new_html;
}