<?php

/**
 * Template functions for Posts Table Pro.
 *
 * @package   Barn2\posts-table-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
use Barn2\Plugin\Posts_Table_Pro\Table_Factory;

// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'ptp_get_posts_table' ) ) {

	/**
	 * Retrieves a post table for the specified args.
	 * The arg names are the same as those used in the shortcode and Table_Args.
	 *
	 * @see The shortcode documentation or See Table_Args::$default_args for the list of supported args.
	 * @param array $args The table args.
	 * @return string The data table as a HTML string.
	 */
	function ptp_get_posts_table( $args = [] ) {
		// Create and return the table as HTML
		$table = Table_Factory::create( $args );
		return $table->get_table( 'html' );
	}

}

// Tambahkan hook untuk menangani permintaan AJAX
add_action('wp_ajax_live_search', 'live_search_handler');
add_action('wp_ajax_nopriv_live_search', 'live_search_handler');

function live_search_handler() {
    // Tangkap kata kunci dari permintaan AJAX
    $keyword = sanitize_text_field($_POST['keyword']);

    // Buat query untuk mengambil posts yang sesuai dengan kata kunci
    $args = array(
        's' => $keyword,
        'post_type' => 'post', // Sesuaikan dengan jenis post Anda
    );
    $posts = new WP_Query($args);

    // Tampilkan hasil dalam format tabel posts
    if ($posts->have_posts()) {
        while ($posts->have_posts()) {
            $posts->the_post();
            // Tampilkan baris tabel posts di sini
            // Misalnya: echo '<tr><td>' . get_the_title() . '</td></tr>';
        }
    } else {
        // Tampilkan pesan jika tidak ada hasil
        echo '<tr><td colspan="5">No posts found.</td></tr>';
    }

    // Jangan lupa reset post data
    wp_reset_postdata();

    // Hentikan eksekusi lebih lanjut setelah menangani permintaan AJAX
    die();
}

function enqueue_custom_admin_scripts() {
    // Enqueue jQuery
    wp_enqueue_script('jquery');

    // Enqueue custom JavaScript from \assets\js\
    wp_enqueue_script('custom-admin-script', get_template_directory_uri() . '/assets/js/custom-admin-post.js', array('jquery'), '1.0', true);

    // Kirim variabel AJAX URL ke skrip JavaScript
    wp_localize_script('custom-admin-script', 'ajax_object', array('ajaxurl' => admin_url('admin-ajax.php')));
}

add_action('admin_enqueue_scripts', 'enqueue_custom_admin_scripts');
