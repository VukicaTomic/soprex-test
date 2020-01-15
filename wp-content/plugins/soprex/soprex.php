<?php
/**
 * Plugin Name: Soprex
 * Description: This plugin bolds all instances of the word "Soprex" and counts post views.
 */

// Function that adds <strong> tags around instances of "Soprex" found in post content

if ( ! function_exists( 'SoprexBoldWords' ) ) {
    function SoprexBoldWords( $content ) {
        return preg_replace('/\b(soprex|Soprex|SOPREX)\b/', '<strong>$1</strong>', $content );
    }
    add_filter( 'the_content', 'SoprexBoldWords' );
}


// Function that creates a table that stores post view count

if ( ! function_exists( 'SoprexCreateTable' ) ) {
    function SoprexCreateTable() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'soprex_post_views';
        $charset_collate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE $table_name (
          id int(11) NOT NULL AUTO_INCREMENT,
          post_id bigint(20) UNSIGNED,
          view_count int(11) DEFAULT 0,
          PRIMARY KEY  (id)
        ) $charset_collate;";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
    }
    register_activation_hook( __FILE__, 'SoprexCreateTable' );
}


// Function that inserts data into the table

if ( ! function_exists( 'SoprexInsertData' ) ) {
    function SoprexInsertData($id, $count) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'soprex_post_views';
        $table_row = $wpdb->get_row("SELECT * FROM $table_name WHERE post_ID = $id");

        if ($table_row !== null) {
            $wpdb->update(
                $table_name,
                array(
                    'view_count' => $count,
                ),
                array(
                    'post_id' => $id
                )
            );
        } else {
            $wpdb->insert(
                $table_name,
                array(
                    'post_id'    => $id,
                    'view_count' => $count,
                )
            );
        }
    }
}


// Function that keeps tracks of post views

if ( ! function_exists( 'SoprexPostViews' ) ) {
    function SoprexPostViews( $postID ) {
        $count_key = 'post_views_count';
        $count = get_post_meta($postID, $count_key, true);
        if ($count == '') {
            $count = 0;
            delete_post_meta($postID, $count_key);
            add_post_meta($postID, $count_key, '0');
        } else {
            $count++;
            update_post_meta($postID, $count_key, $count);
        }
        SoprexInsertData($postID, $count);
    }
}


// Function that adds Soprex counter on single post page

if ( ! function_exists( 'SoprexAddCounter' ) ) {
    function SoprexAddCounter( $content ) {
        if (is_single()) {
            SoprexPostViews(get_the_ID());
        }
        return $content;
    }
    add_filter( 'the_content', 'SoprexAddCounter' );
}