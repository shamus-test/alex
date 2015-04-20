$(document).ready(function() {
	$("#product-tab-content-ModuleAlexproductextra").on('loaded', function(){
		$(function() {
			tinySetup();
		});
	});		
	$(function() {
		tinySetup();
	});
});
$(document).ready(function(){
	tabs_manager.onLoad('ModuleAlexproductextra', function(){
		tinySetup({
			editor_selector :"autoload_rte",
			setup : function(ed) {
				ed.on('init', function(ed)
				{
					if (typeof ProductMultishop.load_tinymce[ed.id] != 'undefined')
					{
						if (typeof ProductMultishop.load_tinymce[ed.id])
							ed.hide();
						else
							ed.show();
					}
				});

				ed.on('keydown', function(ed, e) {
					tinyMCE.triggerSave();
					textarea = $('#'+tinymce.activeEditor.id);
					var max = textarea.parent('div').find('span.counter').data('max');
					if (max != 'none')
					{
						count = tinyMCE.activeEditor.getBody().textContent.length;
						rest = max - count;
						if (rest < 0)
							textarea.parent('div').find('span.counter').html('<span style="color:red;">Maxim '+ max +' caractere : '+rest+'</span>');
						else
							textarea.parent('div').find('span.counter').html(' ');
					}
				});
			}
		});
	});
});


