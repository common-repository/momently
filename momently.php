<?php
/*
Plugin Name: Real Time Content Analytics
Plugin URI: https://momently.com/
Description: Real time content analytics dashboard for digital publishers and bloggers
Version: 1.5.3
Author: Momently, Analytics
Author URI: https://momently.com/
License: GPLv2 or later
Copyright 2015-2018 Momently Inc.
*/

function momently_menu() {
	add_options_page( 'momently plugin options', 'Momently', 'manage_options', 'momently-options', 'momently_options_page' );
	add_menu_page( 'Momently Dashboard', 'Momently', 'edit_posts', 'momently_console', 'momently_console', 'data:image/svg+xml;charset=utf8,%3Csvg version=\'1.1\' xmlns=\'http://www.w3.org/2000/svg\' x=\'0\' y=\'0\' viewBox=\'0 0 16 16\' enable-background=\'new 0 0 16 16\' width=\'16\' height=\'16\' xml:space=\'preserve\'%3E%3Cpolygon fill=\'%23FFFFFF\' points=\'11.8 14 11.8 6 8.1 11.1 4.2 6 4.2 14 2 14 2 2 4 2 8.1 7.4 12 2 14 2 14 14 \'/%3E%3C/svg%3E' );
	if ( get_option( 'momently_script' ) ) {
		add_submenu_page( 'momently_console', 'Momently Dashboard', 'Dashboard', 'edit_posts', 'momently_console', 'momently_console' );
		add_submenu_page( 'momently_console', 'Momently Setup', 'Setup', 'manage_options', 'momently_options', 'momently_options_page' );
	}
}

function momently_console() {
	if ( ! current_user_can( 'edit_posts' ) ) {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
	include( sprintf( '%s/templates/dashboard.php', dirname( __FILE__ ) ) );
}

function momently_options_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
	include( sprintf( '%s/templates/settings.php', dirname( __FILE__ ) ) );
}

function momently_plugin_activate() {
}

function momently_plugin_deactivate() {
}

function momently_register_settings() {
	register_setting( 'momently-options', 'momently_site_id', 'momently_site_id_update' );
	register_setting( 'momently-options', 'momently_track_admin' );
	register_setting( 'momently-options', 'momently_automatic_updates' );
	register_setting( 'momently-options', 'momently_custom_taxonomy_cat' );
	register_setting( 'momently-options', 'momently_lowercase_tags' );
	register_setting( 'momently-options', 'momently_top_level_cat' );
	register_setting( 'momently-options', 'momently_cats_as_tags' );

}

function momently_site_id_update( $momently_site_id ) {
	if ( $momently_site_id ) {
		$momently_site_script = str_replace( '.', '_', $momently_site_id ) . '_momently.js';
		update_option( 'momently_script', $momently_site_script );
	}

	return $momently_site_id;
}

function momently_is_user_logged_in() {
	$current_blog_id = get_current_blog_id();
	$current_user_id = get_current_user_id();

	return is_user_member_of_blog( $current_user_id, $current_blog_id );
}

function momently_automatic_updates_fn( $update, $item ) {

	// If this is multisite and is not on the main site, return early.
	if ( is_multisite() && ! is_main_site() ) {
		return $update;
	}
	// If we don't have everything we need, return early.
	$item = (array) $item;
	if ( ! isset( $item['new_version'] ) || ! isset( $item['slug'] ) ) {
		return $update;
	}
	// If the plugin isn't ours, return early.
	$is_momently = 'momently' === $item['slug'];
	if ( ! $is_momently ) {
		return $update;
	}
	$momently_automatic_updates = get_option( 'momently_automatic_updates', true );
	// If the opt in update allows major updates but there is no major version update, return early.
	if ( $momently_automatic_updates ) {
		return true;
	} else {
		return $update;
	}

}

function momently_ajax_set_set_id() {
	// Run a security check first.
	check_ajax_referer( 'momently_security_nonce', 'nonce' );
	if ( ! current_user_can( 'manage_options' ) ) {
		echo json_encode( true );
	} else {
		$momently_site_id     = $_POST['siteId'];
		$momently_site_script = $_POST['siteScript'];
		if ( $momently_site_id && $momently_site_script ) {
			update_option( 'momently_site_id', $momently_site_id );
			update_option( 'momently_script', $momently_site_script );
			// Send back the response.
			wp_send_json_success();
		} else {
			echo json_encode( true );
		}

	}
	wp_die();
}

function momently_ajax_reset() {
	check_ajax_referer( 'momently_security_nonce', 'nonce' );
	if ( ! current_user_can( 'manage_options' ) ) {
		echo json_encode( true );
	} else {
		delete_option( 'momently_site_id' );
		delete_option( 'momently_script' );
		wp_send_json_success();
	}
	wp_die();
}

function momently_get_coauthor_names( $post_id ) {
	$coauthors = array();
	if ( class_exists( 'coauthors_plus' ) ) {
		global $post, $post_ID, $coauthors_plus;

		$post_id = (int) $post_id;
		if ( ! $post_id && $post_ID ) {
			$post_id = $post_ID;
		}

		if ( ! $post_id && $post ) {
			$post_id = $post->ID;
		}

		if ( $post_id ) {
			$coauthor_terms = get_the_terms( $post_id, $coauthors_plus->coauthor_taxonomy );

			if ( is_array( $coauthor_terms ) && ! empty( $coauthor_terms ) ) {
				foreach ( $coauthor_terms as $coauthor ) {
					$coauthor_slug = preg_replace( '#^cap\-#', '', $coauthor->slug );
					$post_author   = $coauthors_plus->get_coauthor_by( 'user_nicename', $coauthor_slug );
					// In case the user has been deleted while plugin was deactivated.
					if ( ! empty( $post_author ) ) {
						$coauthors[] = $post_author;
					}
				}
			} elseif ( ! $coauthors_plus->force_guest_authors ) {
				if ( $post && $post_id === $post->ID ) {
					$post_author = get_userdata( $post->post_author );
				}
				if ( ! empty( $post_author ) ) {
					$coauthors[] = $post_author;
				}
			}
		}
	}

	return $coauthors;
}

function momently_get_author_name( $author ) {
	$author_name = $author->display_name;
	if ( ! empty( $author_name ) ) {
		return momently_sanitize_value( $author_name );
	}

	$author_name = $author->user_firstname . ' ' . $author->user_lastname;
	if ( ' ' !== $author_name ) {
		return momently_sanitize_value( $author_name );
	}

	$author_name = $author->nickname;
	if ( ! empty( $author_name ) ) {
		return momently_sanitize_value( $author_name );
	}

	return momently_sanitize_value( $author->user_nicename );
}

function momently_sanitize_value( $val ) {
	if ( is_string( $val ) ) {
		$val = str_replace( "\r", '', $val );
		$val = str_replace( "\n", '', $val );
		$val = wp_strip_all_tags( $val );
		$val = trim( $val );

		return $val;
	} else {
		return $val;
	}
}

function momently_get_authors( $post ) {
	$authors = momently_get_coauthor_names( $post->ID );
	if ( empty( $authors ) ) {
		$authors = array( get_user_by( 'id', $post->post_author ) );
	}
	$authors = array_map( 'momently_get_author_name', $authors );
	$authors = apply_filters( 'momently_post_authors', $authors, $post );
	$authors = implode( ',', $authors );

	return $authors;
}

function momently_get_top_level_term( $term_id, $taxonomy_name ) {
	$parent = get_term_by( 'id', $term_id, $taxonomy_name );
	while ( 0 !== $parent->parent ) {
		$parent = get_term_by( 'id', $parent->parent, $taxonomy_name );
	}

	return $parent->name;
}

function momently_get_bottom_level_term( $terms ) {
	$term_ids = wp_list_pluck( $terms, 'term_id' );
	$parents  = array_filter( wp_list_pluck( $terms, 'parent' ) );

	// Get array of IDs of terms which are not parents.
	$child_term_ids = array_diff( $term_ids, $parents );
	// Get corresponding term objects, which are mapped to array index keys.
	$child_terms = array_intersect_key( $terms, $child_term_ids );
	// remove array index keys.
	$terms_not_parents_cleaned = array();
	foreach ( $child_terms as $index => $value ) {
		array_push( $terms_not_parents_cleaned, $value );
	}

	// if you assign multiple child terms in a custom taxonomy, will only return the first.
	return $terms_not_parents_cleaned[0]->name;
}

function momently_get_category( $post ) {
	$taxonomy_option = get_option( 'momently_custom_taxonomy_cat', 'category' );
	$terms           = get_the_terms( $post->ID, $taxonomy_option );
	if ( ! empty( $terms->errors ) ) {
		$category = '';
	} else {
		$top_level_cat = get_option( 'momently_top_level_cat', 1 );

		if ( ! empty( $terms ) ) {
			if ( $top_level_cat ) {
				$first_term = reset( $terms );
				$category   = momently_get_top_level_term( $first_term->term_id, $first_term->taxonomy );
			} else {
				$category = momently_get_bottom_level_term( $terms );
			}
		} else {
			$category = 'Uncategorised';
		}
		$category = apply_filters( 'momently_post_category', $category, $post );
		$category = momently_sanitize_value( $category );
	}

	return $category;
}

function momently_get_custom_taxonomy_values( $post ) {
	// filter out default WordPress taxonomies.
	$all_taxonomies = array_diff( get_taxonomies(), array(
		'post_tag',
		'nav_menu',
		'author',
		'link_category',
		'post_format'
	) );
	$all_values     = array();

	if ( is_array( $all_taxonomies ) ) {
		foreach ( $all_taxonomies as $taxonomy ) {
			$custom_taxonomy_objects = get_the_terms( $post->ID, $taxonomy );
			if ( is_array( $custom_taxonomy_objects ) ) {
				foreach ( $custom_taxonomy_objects as $custom_taxonomy_object ) {
					array_push( $all_values, $custom_taxonomy_object->name );
				}
			}
		}
	}

	return $all_values;
}

function momently_get_categories( $post_id ) {
	$tags       = array();
	$categories = get_the_category( $post_id );
	foreach ( $categories as $category ) {
		$hierarchy = get_category_parents( $category, false, '/' );
		$hierarchy = rtrim( $hierarchy, '/' );
		array_push( $tags, $hierarchy );
	}
	$tags = explode( '/', end( $tags ) );

	return $tags;
}

function momently_get_tags( $post ) {
	$tags    = array();
	$wp_tags = wp_get_post_tags( $post->ID );
	foreach ( $wp_tags as $wp_tag ) {
		array_push( $tags, $wp_tag->name );
	}

	if ( get_option( 'momently_cats_as_tags', 0 ) ) {
		$tags = array_merge( $tags, momently_get_categories( $post->ID ) );
		// Add custom taxonomy values.
		$tags = array_merge( $tags, momently_get_custom_taxonomy_values( $post ) );
		$tags = array_diff( $tags, array( 'Uncategorized', 'Uncategorised' ) );
	}

	if ( function_exists( 'mb_strtolower' ) ) {
		$lowercase_callback = 'mb_strtolower';
	} else {
		$lowercase_callback = 'strtolower';
	}

	if ( get_option( 'momently_lowercase_tags', 1 ) ) {
		$tags = array_map( $lowercase_callback, $tags );
	}
	$tags = apply_filters( 'momently_post_tags', $tags, $post );
	$tags = array_map( 'momently_sanitize_value', $tags );
	$tags = array_values( array_unique( $tags ) );

	return $tags;
}

function momently_add_head() {
	global $post;
	$script      = get_option( 'momently_script' );
	$track_admin = get_option( 'momently_track_admin', 1 );
	if ( $script && ( ( $track_admin || ! momently_is_user_logged_in() ) && ( ( ( is_single() || is_page() ) && $post->post_status == 'publish' ) || ( ! is_single() && ! is_page() ) ) ) ) {
		?>
        <script async="async" name="momently-script"
                src="//s3-us-west-2.amazonaws.com/momently-static/loader/<?php echo $script; ?>"></script>
		<?php
		if ( is_single() ) {

			// Use the author's display name
			$authors   = momently_get_authors( $post );
			$title     = apply_filters( 'momently_config_title', momently_sanitize_value( get_the_title() ) );
			$published = get_post_time( 'U', true ) * 1000;
			$category  = momently_get_category( $post );
			$tags      = momently_get_tags( $post );

			$value = array(
				'id'        => (string) get_the_ID(),
				'author'   => esc_js( $authors ),
				'title'     => esc_js( $title ),
				'published' => $published,
				'tags'      => $tags,
				'category'  => $category
			);
			printf( "<meta id='momently_data' name='momently' content='%s'>", esc_attr( json_encode( $value ) ) );
		}
	}
}

// Add settings link on plugin page
function momently_plugin_settings_link( $links ) {
	$settings_link = '<a href="options-general.php?page=momently-options">Settings</a>';
	array_unshift( $links, $settings_link );

	return $links;
}

// Add momently link on plugin page
function momently_plugin_momently_link( $links ) {
	$settings_link = '<a href="admin.php?page=momently_console">Momently</a>';
	array_unshift( $links, $settings_link );

	return $links;
}

//First enqueue your javascript in WordPress
function momently_enqueue_scripts( $hook ) {
	$plugin = plugin_basename( __FILE__ );
	if ( $hook == 'toplevel_page_momently_console' ) {
		//Enqueue your Javascript (this assumes your javascript file is located in your plugin in an "includes/js" directory)
		wp_enqueue_script( 'momently_dashboard_js', plugins_url( 'js/dashboard.js', $plugin ), array( 'jquery' ) );
		//Here we create a javascript object variable called "momently_js_vars". We can access any variable in the array using momently_js_vars.name_of_sub_variable
		wp_localize_script( 'momently_dashboard_js', 'momently_js_vars',
			array(
				//To use this variable in javascript use "youruniquejs_vars.ajaxurl"
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'momently_security_nonce' ),
				'baseUrl' => admin_url( 'admin.php' ) . '?page=momently_console'
			)
		);
	} else if ( $hook == 'momently_page_momently_options' || $hook === 'settings_page_momently-options' ) {
		wp_enqueue_script( 'momently_settings_js', plugins_url( 'js/settings.js', $plugin ), array( 'jquery' ) );
		wp_localize_script( 'momently_settings_js', 'momently_js_vars',
			array(
				//To use this variable in javascript use "youruniquejs_vars.ajaxurl"
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'momently_security_nonce' ),
				'baseUrl' => admin_url( 'admin.php' ) . '?page=momently_console',
				'hook'    => $hook
			)
		);
	}
}

$plugin = plugin_basename( __FILE__ );
register_activation_hook( __FILE__, 'momently_plugin_activate' );
register_deactivation_hook( __FILE__, 'momently_plugin_deactivate' );
add_filter( "plugin_action_links_$plugin", 'momently_plugin_settings_link' );
add_filter( "plugin_action_links_$plugin", 'momently_plugin_momently_link' );
add_filter( 'auto_update_plugin', 'momently_automatic_updates_fn', 10, 2 );
add_action( 'admin_menu', 'momently_menu' );
add_action( 'wp_ajax_momently_ajax_set_set_id', 'momently_ajax_set_set_id' );
add_action( 'wp_ajax_momently_ajax_reset', 'momently_ajax_reset' );
add_action( 'admin_enqueue_scripts', 'momently_enqueue_scripts' );
// If admin register settings on page that have been saved
// if not, add script to wp_head
if ( is_admin() ) {
	add_action( 'admin_init', 'momently_register_settings' );
} else {
	add_action( 'wp_head', 'momently_add_head' );
}
?>
