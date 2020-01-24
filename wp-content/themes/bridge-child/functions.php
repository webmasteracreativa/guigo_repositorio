<?php

// enqueue the child theme stylesheet

Function qode_child_theme_enqueue_scripts() {
	wp_register_style( 'childstyle', get_stylesheet_directory_uri() . '/style.css'  );
	wp_enqueue_style( 'childstyle' );
}
add_action( 'wp_enqueue_scripts', 'qode_child_theme_enqueue_scripts', 11);

#-----------------------------------------------------------------#
# Elimina referencias a la versión de WordPress
#-----------------------------------------------------------------#

add_filter('the_generator', create_function('', 'return "";'));

#-----------------------------------------------------------------#
# Duplicar Contenido
#-----------------------------------------------------------------#

function content_clone(){
	global $wpdb;
	if (! ( isset( $_GET['post']) || isset( $_POST['post'])  || ( isset($_REQUEST['action']) && 'content_clone' == $_REQUEST['action'] ) ) ) {
		wp_die('No se ha enviado ningún contenido para duplicar');
	}

	$post_id = (isset($_GET['post']) ? $_GET['post'] : $_POST['post']);
	$post = get_post( $post_id );
	$current_user = wp_get_current_user();
	$new_post_author = $current_user->ID;

	if (isset( $post ) && $post != null) {

		$args = array(
			'comment_status' => $post->comment_status,
			'ping_status'    => $post->ping_status,
			'post_author'    => $new_post_author,
			'post_content'   => $post->post_content,
			'post_excerpt'   => $post->post_excerpt,
			'post_name'      => $post->post_name,
			'post_parent'    => $post->post_parent,
			'post_password'  => $post->post_password,
			'post_status'    => 'draft',
			'post_title'     => $post->post_title,
			'post_type'      => $post->post_type,
			'to_ping'        => $post->to_ping,
			'menu_order'     => $post->menu_order
		);

		/*
		 * Crear el nuevo Post/Página via wp_insert_post()
		 */
		$new_post_id = wp_insert_post( $args );

		/*
		 * Para taxonomias de Post/Página a duplicar
		 */
		$taxonomies = get_object_taxonomies($post->post_type); // retorna array de taxonomias
		foreach ($taxonomies as $taxonomy) {
			$post_terms = wp_get_object_terms($post_id, $taxonomy, array('fields' => 'slugs'));
			wp_set_object_terms($new_post_id, $post_terms, $taxonomy, false);
		}
		/*
		 * SQL
		 */
		$post_meta_infos = $wpdb->get_results("SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id=$post_id");
		if (count($post_meta_infos)!=0) {
			$sql_query = "INSERT INTO $wpdb->postmeta (post_id, meta_key, meta_value) ";
			foreach ($post_meta_infos as $meta_info) {
				$meta_key = $meta_info->meta_key;
				$meta_value = addslashes($meta_info->meta_value);
				$sql_query_sel[]= "SELECT $new_post_id, '$meta_key', '$meta_value'";
			}
			$sql_query.= implode(" UNION ALL ", $sql_query_sel);
			$wpdb->query($sql_query);
		}
		/*
		 * Redirect para el editor de Post/Páginas
		 */
		wp_redirect( admin_url( 'post.php?action=edit&post=' . $new_post_id ) );
		exit;
	} else {
		wp_die('No es posible encontrar el Post/Página: ' . $post_id);
	}
}
add_action( 'admin_action_content_clone', 'content_clone' );

/*
 * Adicion del boton "Duplicar" en listas
 */
function content_clone_link( $actions, $post ) {
	if (current_user_can('edit_posts')) {
		$actions['duplicate'] = '<a href="admin.php?action=content_clone&amp;post=' . $post->ID . '" title="Clone this!" rel="permalink">Duplicar</a>';
	}
	return $actions;
}

add_filter( 'post_row_actions', 'content_clone_link', 10, 2 ); // Para post
add_filter( 'page_row_actions', 'content_clone_link', 10, 2 ); //Para páginas

#-----------------------------------------------------------------#
# Upload sin carácteres
#-----------------------------------------------------------------#

function sanitize_filename_on_upload($filename) {
   $ext = end(explode('.',$filename));
   // Reemplazar todos los caracteres extranos.
   $sanitized = preg_replace('/[^a-zA-Z0-9-_.]/','', substr($filename, 0, -(strlen($ext)+1)));
   // Replace dots inside filename
   $sanitized = str_replace('.','-', $sanitized);
   return strtolower($sanitized.'.'.$ext);
}

add_filter('sanitize_file_name', 'sanitize_filename_on_upload', 10);

#-----------------------------------------------------------------#
# Disable Gutenberg for post types
#-----------------------------------------------------------------#
add_filter('use_block_editor_for_post_type', '__return_false', 10);

#-----------------------------------------------------------------#
# Login
#-----------------------------------------------------------------#

function login_custom_stylesheet() { ?>
 <link rel="stylesheet" id="custom_wp_admin_css" href="<?php echo get_bloginfo( 'stylesheet_directory' ) . '/login.css'; ?>" type="text/css" media="all" />
<?php }
add_action( 'login_enqueue_scripts', 'login_custom_stylesheet' );

#-----------------------------------------------------------------#
# Tamaños de Imagenes
#-----------------------------------------------------------------#

add_image_size( 'portfolio-square', '0', '0', false );
add_image_size( 'portfolio-portrait', '0', '0', true );
add_image_size( 'portfolio-landscape', '0', '0', true );
add_image_size( 'menu-featured-post', '0', '0', true );
add_image_size( 'qode-carousel_slider', '0', '0', true );
add_image_size( 'portfolio_slider', '0', '0', true );
add_image_size( 'portfolio_masonry_regular', '0', '0', true );
add_image_size( 'portfolio_masonry_wide', '0', '0', true );
add_image_size( 'portfolio_masonry_tall', '0', '0', true );
add_image_size( 'portfolio_masonry_large', '0', '0', true );
add_image_size( 'portfolio_masonry_with_space', '0', '0', true );
add_image_size( 'latest_post_boxes', '0', '0', false );
