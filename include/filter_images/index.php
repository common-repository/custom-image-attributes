<?php 
/**
 * Filter Image Tag
 */
function custom_img_attributes( $filtered_image, $context, $attachment_id ) {
    $attrib = get_post_meta($attachment_id, 'add_new_attribute', true);
 	$getlazyloadsetting = get_post_meta($attachment_id, 'remove_lazy_loading', true);

    $filtered_image = str_replace( '<img ', '<img ' . $attrib . ' ', $filtered_image );
	
	$getvaluefromsetting = get_option('custom_image_attributes_name');  
	$getfinalvaluefromsetting = $getvaluefromsetting['custom_image_attributes_value'];
	if (!empty($getfinalvaluefromsetting)) {
	$filtered_image = str_replace( '<img ', '<img ' . $getfinalvaluefromsetting . ' ', $filtered_image );
	}

	if (!empty($getlazyloadsetting) && $getlazyloadsetting == '1') {
		$remove = 'loading="lazy"';
    	$add = '';
    	$filtered_image = str_replace($remove, $add, $filtered_image);
	}    
 
    return $filtered_image;
}
add_filter( 'wp_content_img_tag', 'custom_img_attributes', 10, 3 );

function featured_image_priority($html) {
	$attachment_id = get_post_thumbnail_id();
	$attrib = get_post_meta($attachment_id, 'add_new_attribute', true);
	$getlazyloadsetting = get_post_meta($attachment_id, 'remove_lazy_loading', true);

	if (!empty($getlazyloadsetting) && $getlazyloadsetting == '1') {
		$remove = 'loading="lazy"';
    	$add = '';
    	$html = str_replace($remove, $add, $html);
	}else{
		$add = 'loading="lazy"';
		$html = str_replace( '<img ', '<img ' . $add . ' ', $html );
	}


    $html = str_replace( '<img ', '<img ' . $attrib . ' ', $html );

    $getvaluefromsetting = get_option('custom_image_attributes_name');  
	$getfinalvaluefromsetting = $getvaluefromsetting['custom_image_attributes_value'];
	if (!empty($getfinalvaluefromsetting)) {
	$html = str_replace( '<img ', '<img ' . $getfinalvaluefromsetting . ' ', $html);
	}
    
    return $html;
}
add_filter( 'post_thumbnail_html', 'featured_image_priority' );

// For Less version of WP
$getcurrentverison = get_bloginfo( 'version', 'display' );
if ($getcurrentverison <= '6.0') {
	add_filter('the_content','custom_img_attributes_in_content_images');
}
function custom_img_attributes_in_content_images( $content) {

	if ( ! preg_match_all( '/<(img|iframe)\s[^>]+>/', $content, $matches, PREG_SET_ORDER ) ) {
		return $content;
	}

	// List of the unique `img` tags found in $content.
	$images = array();

	// List of the unique `iframe` tags found in $content.
	$iframes = array();

	foreach ( $matches as $match ) {
		list( $tag, $tag_name ) = $match;

		switch ( $tag_name ) {
			case 'img':
				if ( preg_match( '/wp-image-([0-9]+)/i', $tag, $class_id ) ) {
					$attachment_id = absint( $class_id[1] );

					if ( $attachment_id ) {
						// If exactly the same image tag is used more than once, overwrite it.
						// All identical tags will be replaced later with 'str_replace()'.
						$images[ $tag ] = $attachment_id;
						break;
					}
				}
				$images[ $tag ] = 0;
				break;
			case 'iframe':
				$iframes[ $tag ] = 0;
				break;
		}
	}

	// Reduce the array to unique attachment IDs.
	$attachment_ids = array_unique( array_filter( array_values( $images ) ) );

	if ( count( $attachment_ids ) > 1 ) {
		/*
		 * Warm the object cache with post and meta information for all found
		 * images to avoid making individual database calls.
		 */
		_prime_post_caches( $attachment_ids, false, true );
	}

	// Iterate through the matches in order of occurrence as it is relevant for whether or not to lazy-load.
	foreach ( $matches as $match ) {
		// Filter an image match.
		if ( isset( $images[ $match[0] ] ) ) {
			$filtered_image = $match[0];
			$attachment_id  = $images[ $match[0] ];

			$attrib = get_post_meta($attachment_id, 'add_new_attribute', true);
 			$getlazyloadsetting = get_post_meta($attachment_id, 'remove_lazy_loading', true);

 			$filtered_image = str_replace( '<img ', '<img ' . $attrib . ' ', $filtered_image );

			$getvaluefromsetting = get_option('custom_image_attributes_name');  
			$getfinalvaluefromsetting = $getvaluefromsetting['custom_image_attributes_value'];
			if (!empty($getfinalvaluefromsetting)) {
			$filtered_image = str_replace( '<img ', '<img ' . $getfinalvaluefromsetting . ' ', $filtered_image );
			}

			if (!empty($getlazyloadsetting) && $getlazyloadsetting == '1') {
				$remove = 'loading="lazy"';
		    	$add = '';
		    	$filtered_image = str_replace($remove, $add, $filtered_image);
			}  

			if ( $filtered_image !== $match[0] ) {
				$content = str_replace( $match[0], $filtered_image, $content );
			}

			/*
			 * Unset image lookup to not run the same logic again unnecessarily if the same image tag is used more than
			 * once in the same blob of content.
			 */
			unset( $images[ $match[0] ] );
		}
	}

	return $content;
}
