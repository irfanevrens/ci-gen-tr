{include file="header1.tpl"}

<div class="blok1">

	<div class="entrytop">
		<div class="entry">
			<h2>Şifremi Unuttum</h2>
		</div>
	</div>
	
	<div class="icerik">
		
		{if $tamam neq ""}
		<div class="tamam">{$tamam}</div> 
		{else}
		
			{if $hata neq ""}
			<div class="hata">{$hata}</div> 
			{/if}
	
			<form action="{$smarty.const.SAYFA_EDITOR_2}" class="form1" method="post" id="form1">
				
				<p>
					<b>* Mail Adresiniz:</b><br />
					<input type="text" name="mail" size="40" maxlength="255" title="Mail adresinizi yazınız." value="{$kullanici->mail}"></input>
				</p>
	
				<p>
					<input type="submit" value="Gönder"></input>
				</p>
			
			</form>
			
		{/if}

	</div>

</div>

{literal}
<script type="text/javascript">
	$().ready(function(){$('#form1').formtooltip();});
</script>
{/literal}

{include file="footer1.tpl"}	