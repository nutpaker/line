plugin.loadLang();

if(plugin.canChangeOptions())
	{
		plugin.loadMainCSS();
		plugin.addAndShowSettings = theWebUI.addAndShowSettings;
		theWebUI.addAndShowSettings = function( arg )
		{
			if(plugin.enabled)
				{
					$('#line_token').val( theWebUI.line.line_token );
					$$('line_enabled').checked = ( theWebUI.line.line_enabled != 0 );
					$$('line_addition').checked = ( theWebUI.line.line_addition != 0 );
					$$('line_finish').checked = ( theWebUI.line.line_finish != 0 );
					$$('line_deletion').checked = ( theWebUI.line.line_deletion != 0 );
					$('#line_enabled').trigger('change');
				}
			plugin.addAndShowSettings.call(theWebUI,arg);
		}
	
		theWebUI.lineWasChanged = function()
			{
				return(	
					($$('line_enabled').checked != ( theWebUI.line.line_enabled != 0 )) ||
					($$('line_addition').checked != ( theWebUI.line.line_addition != 0 )) ||
					($$('line_finish').checked != ( theWebUI.line.line_finish != 0 )) ||
					($$('line_deletion').checked != ( theWebUI.line.line_deletion != 0 )) ||
					($('#line_token').val() != theWebUI.line.line_token)
				);


			}

		plugin.setSettings = theWebUI.setSettings;
		theWebUI.setSettings = function()
		{
			plugin.setSettings.call(this);
			if( plugin.enabled && this.lineWasChanged() )
				this.request( "?action=setline" );
		}
	
		rTorrentStub.prototype.setline = function()
		{
			this.content = "cmd=set&line_addition=" + ( $$('line_addition').checked ? '1' : '0' ) +
			"&line_deletion=" + ( $$('line_deletion').checked  ? '1' : '0' ) +
			"&line_finish=" + ( $$('line_finish').checked  ? '1' : '0' ) +
			"&line_enabled=" + ( $$('line_enabled').checked  ? '1' : '0' ) +
			"&line_token=" + $('#line_token').val();

			this.contentType = "application/x-www-form-urlencoded";
			this.mountPoint = "plugins/line/action.php";
			this.dataType = "script";
		}
	}

plugin.onLangLoaded = function()
{
	if(this.canChangeOptions())
	{
		plugin.attachPageToOptions( $("<div>").attr("id","st_line").html(
			"<fieldset>"+
				"<legend><a href='https://notify-bot.line.me/my/' target='_blank'>"+theUILang.lineNotification+"</a></legend>"+
				"<div class='checkbox'>" +
					"<input type='checkbox' id='line_enabled' onchange=\"linked(this, 0, ['line_token','line_addition','line_deletion','line_finish']);\"/>"+
					"<label for='line_enabled'>"+ theUILang.Enabled +"</label>"+
				"</div>" +
				"<label for='line_token' id='lbl_line_token' class='disabled'>"+ theUILang.lineToken +"</label>"+
				"<input type='text' id='line_token' class='TextboxLarge' disabled='true' />"+
				"<div class='checkbox'>" +
					"<input type='checkbox' id='line_addition' disabled='true' />"+
					"<label for='line_addition' id='lbl_line_addition' class='disabled'>"+ theUILang.lineAddition +"</label>"+
				"</div>" +
				"<div class='checkbox'>" +
					"<input type='checkbox' class='disabled' id='line_deletion' disabled='true' />"+
					"<label for='line_deletion' id='lbl_line_deletion' class='disabled'>"+ theUILang.lineDeletion +"</label>"+
				"</div>" +
				"<div class='checkbox'>" +
					"<input type='checkbox' id='line_finish' disabled='true' />"+
					"<label for='line_finish' id='lbl_line_finish' class='disabled'>"+ theUILang.lineFinish +"</label>"+
				"</div>" +
			"</fieldset>"
			)[0], theUILang.line );
	};
}

plugin.onRemove = function()
{
	plugin.removePageFromOptions("st_line");
}

plugin.langLoaded = function()
{
	if(plugin.enabled)
		plugin.onLangLoaded();
}
