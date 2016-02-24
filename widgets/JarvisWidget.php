<?php

namespace demetrio77\smartadmin\widgets;

use yii\base\Widget;

class JarvisWidget extends Widget
{
	private static $cnt = 0; 
	public $title;
	public $nopadding = false;
	public $togglebutton = true;
	public $colorbutton=true;
	public $editbutton=false;
	public $deletebutton=false;
	public $fullscreenbutton=true;
	public $collapsed=false;
	public $sortable=false;
	public $id = "";
	public $currentCnt = 0;
	
	public function init()
	{
		$this->currentCnt = static::$cnt;
		static::$cnt++;
		parent::init();
		if (mb_strlen($this->title)>65 && mb_strpos($this->title, ' ', 55)) {
			$this->title = mb_substr($this->title, 0, mb_strpos($this->title, ' ', 55)).'...'; 
		}
		ob_start();
	}
	
	public function run()
	{
		$content = ob_get_clean();
		$options = '';
		if (!$this->togglebutton) {
			$options .= 'data-widget-togglebutton="false" ';
		}
			if (!$this->colorbutton) {
			$options .= 'data-widget-colorbutton="false" ';
		}
		if (!$this->editbutton) {
			$options .= 'data-widget-editbutton="false" ';
		}
		if (!$this->deletebutton) {
			$options .= 'data-widget-deletebutton="false" ';
		}
		if (!$this->fullscreenbutton) {
			$options .= 'data-widget-fullscreenbutton="false" ';
		}
		if ($this->collapsed) {
			$options .= 'data-widget-collapsed="true" ';
		}
		if (!$this->sortable) {
			$options .= 'data-widget-sortable="false" ';
		}
		
		return 
		'<div class="jarviswidget jarviswidget-color-darken" id="wid-id-'.($this->id?$this->id:$this->currentCnt).'"'.$options.'>
			<header><h2 class="font-md">'.$this->title.'</h2></header>
			<div>
				<div class="jarviswidget-editbox"></div>						
    			<div class="widget-body'.($this->nopadding?' no-padding':'').'">'.$content.'</div>
    		</div>
    	</div>';
	}
}