jQuery( document ).ready( function( $ ) {
	$('#dolly').click( function( ) {
		$(this).fadeOut( 'slow', function() { $(this).html( hello_dolly2.lyrics[ Math.floor(Math.random() * hello_dolly2.lyrics.length ) ] ) } ).fadeIn( 'slow' );
	});
});