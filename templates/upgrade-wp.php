<?php
/**
 * Nootice to be displayed if WordPress version does not meet minimum version requirements
 * @package Plugin_Boilerplate
 */
?>
<div class="notice"><?php _e( sprintf( '%s requires WordPress version %s or greater. Please disable the plugin or upgrade your WordPress install.', $this->name, $this->min_wp ) ); ?></div>