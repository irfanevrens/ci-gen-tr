// ----------------------------------------------------------------------------
// markItUp!
// ----------------------------------------------------------------------------
// Copyright (C) 2008 Jay Salvat
// http://markitup.jaysalvat.com/
// ----------------------------------------------------------------------------
// BBCode tags example
// http://en.wikipedia.org/wiki/Bbcode
// ----------------------------------------------------------------------------
// Feel free to add more tags
// ----------------------------------------------------------------------------
mySettings = {
	previewParserPath:	'', // path to your BBCode parser
	markupSet: [
		{name:'Kalın', key:'B', openWith:'[b]', closeWith:'[/b]'},
		{name:'Eğik', key:'I', openWith:'[i]', closeWith:'[/i]'},
		{name:'Altı Çizgili', key:'U', openWith:'[u]', closeWith:'[/u]'},
		{separator:'---------------' },
		// {name:'Picture', key:'P', replaceWith:'[img][![Url]!][/img]'},
		{name:'Bağlantı', key:'L', openWith:'[url=[![Url]!]]', closeWith:'[/url]', placeHolder:'Your text to link here...'},
		//{separator:'---------------' },
		//{name:'Size', key:'S', openWith:'[size=[![Text size]!]]', closeWith:'[/size]',
		//dropMenu :[
		//	{name:'Big', openWith:'[size=200]', closeWith:'[/size]' },
		//	{name:'Normal', openWith:'[size=100]', closeWith:'[/size]' },
		//	{name:'Small', openWith:'[size=50]', closeWith:'[/size]' }
		//]},
		{separator:'---------------' },
		{name:'Sırasız Liste', openWith:'[list]\n', closeWith:'\n[/list]'},
		{name:'Sıralı Liste', openWith:'[list=[![Starting number]!]]\n', closeWith:'\n[/list]'}, 
		{name:'Madde', openWith:'[*] '},
		{separator:'---------------' },
		{name:'PHP Kodu', openWith:'[php_kodu]\n', closeWith:'\n[/php_kodu]'},
		{separator:'---------------' }
		//{name:'Quotes', openWith:'[quote]', closeWith:'[/quote]'},
		//{name:'Code', openWith:'[code]', closeWith:'[/code]'}, 
		//{separator:'---------------' },
		//{name:'Temizle', className:"clean", replaceWith:function(markitup) { return markitup.selection.replace(/\[(.*?)\]/g, "") } },
		// {name:'Preview', className:"preview", call:'preview' }
	]
}