
function mfg_trigger_search( term ) {
	var search = document.getElementById('s');
	if( search ) {
		search.value = term;
		var local = document.getElementById('mfg_link');
		if( local )
			local.value = "1";
		var form = document.getElementById('searchform');
		if( form )
			form.submit();
	}
}

function mfg_mouse_down( event, term ) {
	try {
		if( event.button == 2 ) { // right click
			return true;
		}
	}
	catch (e) {
		// no button in IE for onclick
	}
	mfg_trigger_search( term );
	return false;
}

function mfg_create_link( id, term, link_prefix, link_suffix ) {
	var mfg_link = document.getElementById( id );
	if( mfg_link ) {
		mfg_link.onclick = function( event ) { return mfg_mouse_down( event, term ); };
		mfg_link.onmouseup = function( event ) { return mfg_mouse_down( event, term ); };
		mfg_link.innerHTML = link_prefix + term + link_suffix;
	}
}