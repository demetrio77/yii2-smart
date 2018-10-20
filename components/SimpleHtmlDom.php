<?php

namespace demetrio77\smartadmin\components;

use yii\base\BaseObject;

class SimpleHtmlDom extends BaseObject
{
	public static function instance()
	{
		require_once __DIR__ . '/SimpleHtmlDom/simple_html_dom.php';
		return new \simple_html_dom();
	}
}