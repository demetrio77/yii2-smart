<?php

namespace demetrio77\smartadmin\helpers\typograph;

class Typograph 
{
	public static function process($text)
	{
		require_once 'EMT.php';
		
		return \EMTypograph::fast_apply($text, [	
			'Text.paragraphs' => 'off',
			'Text.breakline'=>'off',
			'OptAlign.oa_oquote' => 'off',
			'OptAlign.oa_obracket_coma' => 'off',
			'Quote.quotation'=>'off',
			'Text.auto_links'=>'off',
			'Nobr.spaces_nobr_in_surname_abbr'=>'off',
			'Etc.century_period' => 'off'
		]);
	}
	
	public static function remove($text)
	{
		return html_entity_decode($text);
	}
}
