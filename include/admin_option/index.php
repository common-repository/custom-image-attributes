<?php 
/*
* Plugin Admin Option
*/
class CustomImageAttributes_SettingsPage
{
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;

    /**
     * Start up
     */
    public function __construct()
    {
        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'page_init' ) );
        add_filter('admin_footer_text', array( $this, 'plugin_admin_footer_text'));
    }

    /**
     * Add options page
     */
    public function add_plugin_page()
    {
        // This page will be under "Settings"
        add_submenu_page(
            'options-general.php',
            'Settings Admin', 
            'Custom Image Attributes', 
            'manage_options', 
            'custom-image-attributes-setting', 
            array( $this, 'create_admin_page' )
        );
    }

    /**
     * Options page callback
     */
    public function create_admin_page()
    {
        // Set class property
        $this->options = get_option( 'custom_image_attributes_name' );
        ?>
        <div class="wrap">
            <h1>Setting</h1>
            <form method="post" action="options.php">
            <?php
                // This prints out all hidden setting fields
                settings_fields( 'custom_image_attributes_group' );
                do_settings_sections( 'custom-image-attributes-setting' );
                submit_button();
            ?>
            </form>
        </div>
        <?php
    }

    /**
     * Register and add settings
     */
    public function page_init()
    {        
        register_setting(
            'custom_image_attributes_group', // Option group
            'custom_image_attributes_name', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );

        add_settings_section(
            'setting_section_id', // ID
            'Custom Image Attributes', // Title
            array( $this, 'print_section_info' ), // Callback
            'custom-image-attributes-setting' // Page
        );

        add_settings_field(
            'custom_image_attributes_value', // ID
            'Custom Image Attributes', // Title 
            array( $this, 'custom_image_attributes_final_callback' ), // Callback
            'custom-image-attributes-setting', // Page
            'setting_section_id' // Section           
        );          
                        
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize( $input )
    {
        $new_input = array();
        if( isset( $input['custom_image_attributes_value'] ) )
            $new_input['custom_image_attributes_value'] = $input['custom_image_attributes_value'];
        return $new_input;
    }

    /** 
     * Print the Section text
     */
    public function print_section_info()
    {
        print 'Add an attribute to an image by viewing the image via your media folder. Look for the field to add your attribute, enter it, then click save. To add multiple attributes, simple leave a space, e.g. fetchpriority=”high” data-save=”lightbox” <br/> <br/> Want to add an attribute to all images? Enter your attribute below then click save. The attribute will be saved to all images. To clear all attributes, leave blank then save.';
    }

    public function custom_image_attributes_final_callback()
    {
        printf(
            '<input type="text" id="custom_image_attributes_value" name="custom_image_attributes_name[custom_image_attributes_value]" value="%s" />',
            isset( $this->options['custom_image_attributes_value'] ) ? esc_attr( $this->options['custom_image_attributes_value']) : ''
        );
    }


    public function plugin_admin_footer_text( $footer_text ) {
        if (isset($_GET['page']) && $_GET['page'] === 'custom-image-attributes-setting') {
            $footer_text = 'Custom Image Attributes v1.0. Developed by <a href="https://liveseo.co.uk" target="_blank" rel="noopener">Live SEO</a> | <a href="https://liveseo.co.uk" target="_blank" rel="noopener">Get Support</a> | <a href="https://liveseo.co.uk" target="_blank" rel="noopener">Buy us a coffee</a> or API credits so we can make more great tools.';
        }
        return $footer_text;
    }    

}


