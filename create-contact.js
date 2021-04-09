/*jslint esnext:true */
/*global wpse_385769 */
// This script does not rely on jQuery..

function createContact( btn, status ) {
	const _btnText = btn.innerText;

	btn.disabled = true;
	btn.innerText = 'Creating Contact...';

	let _res = {}; // on successful request, this would be a Response object with data like status, statusText, url, etc.
	fetch( wpse_385769.apiRoot + 'wp/v2/contact', { // If you are using a custom rest_base, use it in place of 'contact'.
		method: 'POST',
		body: JSON.stringify( {
			title: `test via JS window.fetch() at ${ ( new Date() ).toLocaleString() } :)`,
			status: status || 'draft',
		} ),
		headers: { 'Content-Type': 'application/json' },
	} )
		.then( res => {
			_res = res;
			return res.json();
		} )
		.then( post => {
			const res = document.querySelector( '#ajax-res' );

			if ( post.id ) {
				const link = `<a href="${ post.link }" target="_blank" title="Status: ${ post.status }">${ post.id }</a>`;
				res.innerHTML = `Success! ID: ${ link }; Title: <i>${ post.title.rendered }</i>`;
			} else if ( post.code ) {
				res.innerHTML = `Failed! :( Message: <i>${ post.message } (${ post.code })</i>`;
			} else {
				res.innerHTML = 'Unknown error - check the console log. Try again later.';
			}
			console.log( post );

			btn.innerText = _btnText;
			btn.disabled = false;
		} )
		.catch( error => {
			const res = document.querySelector( '#ajax-res' );

			res.innerHTML = `ERROR ${ _res.status || '' }: <i>${ error }</i> - check the console`;
			console.log( _res );

			btn.innerText = _btnText;
			btn.disabled = false;
		} );
}
