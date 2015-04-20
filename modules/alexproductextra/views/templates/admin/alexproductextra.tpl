<div class="panel product-tab" id="product-compozitie">	
	<h3>{l s='Alex Product Extra' mod='alexproductextra'}</h3>		
	{foreach from=$languages item=language}
		{$idlang = $language.id_lang}
		{if $languages|count > 1}
			<div class="translatable-field lang-{$idlang}" {if $idlang != $default_language}style="display:none"{/if}>
		{/if}
		<div class="form-group">		
			<label class="control-label col-lg-3" for="editor_note">
				<span class="label-tooltip">
					{l s='Editor\'s Note' mod='alexproductextra'}
				</span>
			</label>
			<div class="col-lg-8">
				<textarea class="autoload_rte" name="editor_note_{$idlang}" id="editor_note_{$idlang}">{$alex_data.$idlang.editor_note}</textarea>
			</div>
			{if $languages|count > 1}
				<div class="col-lg-1">
					<button type="button" class="btn btn-default dropdown-toggle" tabindex="-1" data-toggle="dropdown">
						{$language.iso_code}
						<span class="caret"></span>
					</button>
					<ul class="dropdown-menu">
						{foreach from=$languages item=lang}
						<li><a href="javascript:hideOtherLanguage({$lang.id_lang});" tabindex="-1">{$lang.name}</a></li>
						{/foreach}
					</ul>
				</div>
			{/if}
		</div>
		<div class="form-group">		
			<label class="control-label col-lg-3" for="shippin_and_return">
				<span class="label-tooltip">
					{l s='Shipping and Returns' mod='alexproductextra'}
				</span>
			</label>
			<div class="col-lg-8">
				<textarea class="autoload_rte" name="shippin_and_return_{$idlang}" id="shippin_and_return_{$idlang}">{$alex_data.$idlang.shippin_and_return}</textarea>
			</div>
			{if $languages|count > 1}
				<div class="col-lg-1">
					<button type="button" class="btn btn-default dropdown-toggle" tabindex="-1" data-toggle="dropdown">
						{$language.iso_code}
						<span class="caret"></span>
					</button>
					<ul class="dropdown-menu">
						{foreach from=$languages item=lang}
						<li><a href="javascript:hideOtherLanguage({$lang.id_lang});" tabindex="-1">{$lang.name}</a></li>
						{/foreach}
					</ul>
				</div>
			{/if}
		</div>
		{if $languages|count > 1}
			</div>
		{/if}
		<script>
			$(document).ready(function(){				
				$('#editor_note_{$idlang}, #shippin_and_return_{$idlang}').change(function(e){
					var val = $(this).val();
					var file = val.split(/[\\/]/);
					$('#editor_note_{$idlang}-name, #shippin_and_return_{$idlang}-name').val(file[file.length-1]);
				});
			});
		</script>
	{/foreach}	
	<div class="panel-footer">
		<a class="btn btn-default" href="index.php?controller=AdminProducts&amp;token={$token}"><i class="process-icon-cancel"></i>{l s='Cancel'  mod='alexproductextra'}</a>
		<button class="btn btn-default pull-right" name="submitAddproduct" type="submit"><i class="process-icon-save"></i> {l s='Save'  mod='alexproductextra'}</button>
		<button class="btn btn-default pull-right" name="submitAddproductAndStay" type="submit"><i class="process-icon-save"></i> {l s='Save and stay'  mod='alexproductextra'}</button>
	</div>
</div>
{literal}
<script language="javascript" type="text/javascript">
	$(function() {
		tinySetup();
	});
</script>
{/literal}