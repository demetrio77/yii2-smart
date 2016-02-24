<?php

namespace demetrio77\smartadmin\widgets;

use yii\widgets\DetailView;

class DkDetailView extends DetailView {
    
    public $template = '<tr><th class="col-xs-6 col-sm-4 col-md-3 col-lg-3">{label}</th><td>{value}</td></tr>';
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