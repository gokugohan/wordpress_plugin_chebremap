<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://chebre.net
 * @since      1.0.0
 *
 * @package    Chebre_Map
 * @subpackage Chebre_Map/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<h1>Settings</h1>
<br>
<form method="post" name="map-setting" action="options.php">

    <?php
    //Grab all options
    $options = get_option($this->plugin_name);

    $sidebar_image = $options['sidebar-image-url'];
    $sidebar_avatar = $options['sidebar-avatar-url'];
    $sidebar_title = $options['sidebar-title'];

    ?>

    <?php
    settings_fields($this->plugin_name);
    do_settings_sections($this->plugin_name);
    ?>


    <table class="table table-chebre-map-setting">
        <tbody>
        <tr>
            <th><label><?php esc_attr_e('Map Title', $this->plugin_name); ?></label></th>
            <td>
                <input type="text" id="<?php echo $this->plugin_name; ?>-sidebar-title"
                       name="<?php echo $this->plugin_name; ?>[sidebar-title]"
                       value="<?php if (!empty($sidebar_title)) echo $sidebar_title; ?>"/>

            </td>
        </tr>
        <tr>
            <th><label><?php esc_attr_e('Map Sidebar avatar', $this->plugin_name); ?></label></th>
            <td>
                <?php
                if (empty($sidebar_avatar)) {
                    $sidebar_avatar = plugin_dir_url(__FILE__) . 'img/songoku.png';
                }
                ?>
                <input type="hidden" id="<?php echo $this->plugin_name; ?>-default-avatar" value="<?=plugin_dir_url(__FILE__) . 'img/songoku.png'?>">
                <img src="<?= $sidebar_avatar?>" class="result-avatar">
                <input type="hidden" id="<?php echo $this->plugin_name; ?>-sidebar-avatar-url"
                       name="<?php echo $this->plugin_name; ?>[sidebar-avatar-url]"
                       value="<?php if (!empty($sidebar_image)) echo $sidebar_image; ?>"/>

                <br>
                <a href="#!" class="<?php echo $this->plugin_name; ?>-sidebar-avatar-upload-button">Upload avatar</a> | <a href="#!" class="<?php echo $this->plugin_name; ?>-sidebar-avatar-remove-button">Remove avatar</a>
            </td>
        </tr>
        <tr>
            <th><label><?php esc_attr_e('Map Sidebar bakground', $this->plugin_name); ?></label></th>
            <td>
                <?php
                if (empty($sidebar_image)) {
                    $sidebar_image = plugin_dir_url(__FILE__) . 'img/header.jpg';
                }
                ?>
                <input type="hidden" id="<?php echo $this->plugin_name; ?>-default-img" value="<?=plugin_dir_url(__FILE__) . 'img/header.jpg'?>">
                <img src="<?= $sidebar_image?>" class="result-img">
                <input type="hidden" id="<?php echo $this->plugin_name; ?>-sidebar-image-url"
                       name="<?php echo $this->plugin_name; ?>[sidebar-image-url]"
                       value="<?php if (!empty($sidebar_image)) echo $sidebar_image; ?>"/>

                <br>
                <a href="#!" class="<?php echo $this->plugin_name; ?>-sidebar-image-upload-button">Upload image</a> | <a href="#!" class="<?php echo $this->plugin_name; ?>-sidebar-image-remove-button">Remove image</a>
            </td>
        </tr>
        </tbody>
    </table>

    <?php submit_button('Save all changes', 'primary', 'submit', TRUE); ?>
