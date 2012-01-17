jQuery( document ).ready( function( $ ) {
	$('#dolly').click( function( ) {
		$(this).fadeOut( 'slow', function() { $(this).html( hd2.lyrics[ Math.floor(Math.random() * hd2.lyrics.length ) ] ) } ).fadeIn( 'slow' );
	});
});