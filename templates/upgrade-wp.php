<?php
/**
 * Notice to be displayed if WordPress version does not meet minimum version requirements
 * @package Plugin_Boilerplate
 */
?>
<div class="notice"><?php sprintf( __( '%s requires WordPress version %s or greater. Please disable the plugin or upgrade your WordPress install.'), $this->parent->name, $this->parent->min_wp ); ?></div>