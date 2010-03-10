{include file="header1.tpl"}
	
<div class="blok1">

	<div class="entrytop">
		<div class="entry">
			<h2>Yazı Düzenle</h2>
		</div>
	</div>
	
	<div class="icerik">
		
		{if $tamam neq ""}
		<div class="tamam">{$tamam}</div> 
		{else}
		
			{if $hata neq ""}
			<div class="hata">{$hata}</div> 
			{/if}
	
			<form action="{$smarty.const.SAYFA_YAZAR_13}" class="form1" method="post" id="form1">
				
				<p>
					<b>* Kategori:</b><br />
					<select name="kategori_id" title="Yazı kategorisini seçiniz.">
					   {html_options values=$kategori_ids output=$kategori_names selected=$kategori_selected_id}
					</select>
				</p>
				
				<p>
					<b>* Başlık:</b><br />
					<input type="text" name="baslik" size="60" maxlength="255" title="Yazının başlığını yazınız. Ör: Yazı Başlığı" value="{$yazi->baslik}" />
				</p>
				
				<p>
					<b>* Özet:</b><br />
					<i>Yazının özetini yazınız. Yazınız uzun değilse sadece özet alanı yeterlidir. İçerik yazmayınız.</i>
					<textarea rows="10" cols="60" name="ozet">{$yazi->ozet}</textarea>
				</p>
				
				<p>
					<b>İçerik:</b><br />
					<i>Yazı içeriğini yazınız. Özet ana sayfada görünen yazıdır. İçerik ise özet ile birlikte yazının devamını okumak isteyene gösterilir.</i>
					<textarea rows="10" cols="60" name="icerik">{$yazi->icerik}</textarea>
				</p>
				
				<p>
					<b>Etiketler:</b><br />
					<input name="etiketler" size="60" title="Etiketleri aralarında virgül (,) kullanarak ayırınız|Ör: php, mysql, code igniter" value="{$etiketler}" />
				</p>
		
				<p>
					<input type="hidden" name="id" value="{$yazi->id}" />
					<input type="submit" value="Kaydet" />
				</p>

			</form>
			
		{/if}

	</div>

</div>

<script src="{$smarty.const.DIZIN_URL_2}/js/ckeditor/ckeditor.js" type="text/javascript"></script>

{literal}
<script type="text/javascript">

	CKEDITOR.replace('ozet', {customConfig: 'tr.gen.ci.js'});
	CKEDITOR.replace('icerik', {customConfig: 'tr.gen.ci.js'});

	$().ready(function(){
		$('#form1').formtooltip();	
	});
	
</script>
{/literal}

{include file="footer1.tpl"}	