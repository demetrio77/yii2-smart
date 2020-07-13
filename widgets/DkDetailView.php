<?php

namespace demetrio77\smartadmin\widgets;

use yii\widgets\DetailView;

class DkDetailView extends DetailView {

    public $template = '<tr><th class="bg-primary-300 text-uppercase">{label}</th><td>{value}</td></tr>';
    public $title = '';

    public function init()
    {
    	if (mb_strlen($this->title)>60) {
    		$this->title = mb_substr($this->title, 0, 60).'...';
    	}
    	return parent::init();
    }

    public function run() {
       	JarvisWidget::begin(['title'=>$this->title]);
    	parent::run();
    	JarvisWidget::end();
    }
}
