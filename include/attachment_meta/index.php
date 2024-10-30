<?php
function add_image_attachment_fields_to_edit( $form_fields, $post ) {
    
    unset( $form_fields['post_content'] ); 
    
    $form_fields['post_title']['helps'] = 'Use a descriptive title for the image. This will make it easy to find the image in the future and will improve SEO.';
        
    $form_fields['post_excerpt']['helps'] = 'Describe the significants of the image pertaining to the site.';
    $caption_field = $form_fields['post_excerpt'];
    unset($form_fields['post_excerpt']);
    

    $image_url_field = $form_fields['image_url'];
    unset($form_fields['image_url']);
    

    $form_fields['post_excerpt'] = $caption_field;
    
    $removelazyloading =  get_post_meta($post->ID, 'remove_lazy_loading', true);
    if ($removelazyloading == '1') {
        $removelazyloadingcheck =  'checked="checked"';
    }else{
        $removelazyloadingcheck =  '';
    }

    $form_fields["remove_lazy_loading"] = array(
    'label' => 'Remove Lazy Loading',
    'input' => 'html',
    'html' => '<label for="attachments-'.$post->ID.'-foo"> '.
        '<input type="checkbox" id="attachments-'.$post->ID.'-foo" name="attachments['.$post->ID.'][remove_lazy_loading]" value="1" '.$removelazyloadingcheck.' /> Yes</label>  ',
    'value' => $removelazyloading,
    );
    
    $form_fields["add_new_attribute"] = array(
        "label" => __("New Attribute"),
        "input" => "text", 
        "value" => get_post_meta($post->ID, "add_new_attribute", true) ,
    );
    
    $form_fields['image_url'] = $image_url_field;
    
    return $form_fields;
}
add_filter("attachment_fields_to_edit", "add_image_attachment_fields_to_edit", null, 2);

function add_image_attachment_fields_to_save( $post, $attachment ) {
    $remove_lazy_loading = ($attachment['remove_lazy_loading'] == '1') ? '1' : ' ';
    update_post_meta( $post['ID'], 'remove_lazy_loading', esc_attr($remove_lazy_loading) );
        
    if ( isset( $attachment['add_new_attribute'] ) )
        update_post_meta( $post['ID'], 'add_new_attribute', $attachment['add_new_attribute'] );

    return $post;
}
add_filter("attachment_fields_to_save", "add_image_attachment_fields_to_save", null , 2);