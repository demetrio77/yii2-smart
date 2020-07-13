<?php

namespace demetrio77\smartadmin\widgets;

use yii\base\Widget;
use yii\web\View;

class JarvisWidget extends Widget
{
	private static $cnt = 0;
	public $title;
	public $nopadding = false;
	public $togglebutton = true;
	public $colorbutton = true;
	public $editbutton = false;
	public $deletebutton = false;
	public $fullscreenbutton = true;
	public $collapsed = false;
	public $sortable = false;
	public $id = "";
	public $currentCnt = 0;
	public $options = [];

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
		$panelId = 'wid-id-'.($this->id?$this->id:$this->currentCnt);
		return
		'<div class="panel '.($this->options['class'] ?? '').'" role="widget" id="'.$panelId.'">
			<div class="panel-hdr">
			    <h2>'.$this->title.'</h2>
			    <div class="panel-toolbar">
                    <button class="btn btn-panel" data-action="panel-collapse" data-toggle="tooltip" data-offset="0,10" data-original-title="Collapse"></button>
                    <button class="btn btn-panel" data-action="panel-fullscreen" data-toggle="tooltip" data-offset="0,10" data-original-title="Fullscreen"></button>
                    <button class="btn btn-panel" data-action="panel-close" data-toggle="tooltip" data-offset="0,10" data-original-title="Close"></button>
                </div>
            </div>
			<div class="panel-container show">
				<div class="panel-content border-faded border-top-0 border-right-0 border-left-0">'.$content.'</div>
    		</div>
    	</div>';
	}
}
