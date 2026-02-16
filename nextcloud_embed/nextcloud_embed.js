//


function embedded_in_nextcloud()
{
	return (window.top.OCA && window.top.OCA.Theming);
}

function nextcloud_embed_init(_event)
{
	document.getElementById("rcmloginsubmit").addEventListener("click", function() {
		// Accept login form submit with empty values
		window.location.href=window.location.pathname;	
	});	

	if (embedded_in_nextcloud())
	{
		const topBodyStyle=window.top.getComputedStyle(window.top.document.body)
		const bgColor=topBodyStyle.getPropertyValue('--normal-brand-color');
		const fgColor=topBodyStyle.getPropertyValue('--light-brand-color');
		const textColor=topBodyStyle.getPropertyValue('--color-background-plain-text');
		const ss=document.styleSheets[0];

		ss.insertRule(`#layout-menu { color: ${textColor} !important; background-color: ${bgColor} !important; }`);
		ss.insertRule(`#layout-menu div.popover-header { background-color: transparent !important;}`);
		ss.insertRule(`#taskmenu a.selected,#taskmenu a:hover { background-color: ${fgColor} !important; }`);
		ss.insertRule(`#taskmenu .action-buttons a,#taskmenu .action-buttons a span.inner { color: ${textColor} !important; } `);

		if (rcmail.env.removeEmbeddedItem)
			$(rcmail.env.removeEmbeddedItem).remove();
	}
}

// override rcube__webmail.session_error
// see app.js:session_error()
rcmail.session_error=function(redirect_url)
{
    this.env.server_error = 401;
    if (this.env.action == 'compose') {
      this.save_compose_form_local();
      this.compose_skip_unsavedcheck = true;
      this.env.session_lifetime = 0;
      if (this._keepalive)
        clearInterval(this._keepalive);
      if (this._refresh)
        clearInterval(this._refresh);
    }
    else if (redirect_url) {
		if (embedded_in_nextcloud())
			setTimeout(function() { window.top.location.href=window.top.location.href; }, 2000);
		else
			setTimeout(function() { rcmail.redirect(redirect_url, true); }, 2000);
    }
};


if (window.rcmail)
{
	rcmail.addEventListener('init', nextcloud_embed_init);
}
