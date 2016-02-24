<?php

namespace demetrio77\smartadmin\components;

use yii\base\Object;

class SimpleHtmlDom extends Object
{
	public static function instance()
	{
		require_once __DIR__ . '/SimpleHtmlDom/simple_html_dom.php';
		return new \simple_html_dom();
	}
}