
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

function mfg_create_link( id, term, link_prefix, link_suffix ) {
	var mfg_link = document.getElementById( id );
	if( mfg_link ) {
		mfg_link.onclick = function() { mfg_trigger_search( term ); return false; };
		mfg_link.innerHTML = link_prefix + term + link_suffix;
	}
}