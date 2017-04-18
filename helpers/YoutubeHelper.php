<?php

namespace demetrio77\smartadmin\helpers;

use yii\base\Object;

/**
 * Изымаем видео и превью Ютуба.
 *
 * @property string $imageSrc
 * @property string $embed 
 * @property string $autoplay 
 * 
 * @author Dmitry Karpov
 */
class YoutubeHelper extends Object
{
	public $url = '';
	public $validUrl = false;
	public $id = '';
	
	public function init()
	{
		parent::init();
		$this->initialize();
	}
	
	private function initialize()
	{
		if ($this->url=='') return false;
		
		preg_match_all('/v=([_a-zA-Z0-9\-]+)/', $this->url, $matches);
		if (isset($matches[1][0])) {
			$this->id = $matches[1][0];
			$this->validUrl = true;
		}
		
		return false;
	}
	
	public function getImageSrc()
	{
		if (!$this->validUrl) return '';
		return '//img.youtube.com/vi/'.$this->id.'/0.jpg';
	}
	
	public function getEmbed()
	{
		if (!$this->validUrl) return '';
		return "//www.youtube.com/embed/".$this->id;
	}
	
	public function getAutoplay()
	{
		if (!$this->validUrl) return '';
		return $this->embed."?autoplay=1";
	}
	
	public function getIframe($width = 560, $height = 315, $autoplay = false)
	{
		return '<iframe width="'.$width.'" height="'.$height.'" src="'.($autoplay ? $this->autoplay : $this->embed ).'" frameborder="0" allowfullscreen></iframe>';
	}
}