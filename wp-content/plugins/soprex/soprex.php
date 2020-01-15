<?php
/**
 * Plugin Name: Soprex
 * Description: This plugin bolds all instances of the word "Soprex" and counts post views.
 */

// Function that adds <strong> tags around instances of "Soprex" found in post content

if ( ! function_exists( 'bold_soprex_words' ) ) {
    function bold_soprex_words( $content ) {
        return preg_replace('/\b(soprex|Soprex|SOPREX)\b/', '<strong>$1</strong>', $content );
    }
}
add_filter( 'the_content', 'bold_soprex_words' );