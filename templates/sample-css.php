<?php 
/**
 * Template for sample plugin to inject CSS into header
 * @project Plugin_Boilerplate
 */
$x = is_rtl() ? 'left' : 'right';
?>		
<style type='text/css'>
	#dolly {
		float: <?php echo $x; ?>;
		padding-<?php echo $x; ?>: 15px;
		padding-top: 5px;		
		margin: 0;
		font-size: 11px;
		cursor: pointer;
	}
</style>