<?php

namespace demetrio77\smartadmin\widgets;

use yii\grid\GridView;

class DkGridView extends GridView 
{
    public $layout = '<div class="alert alert-info no-margin fade in">{pager}</div>{items}<div class="alert alert-info no-margin fade in">{pager}</div>';
    public $title;

    public function run() {
       	JarvisWidget::begin(['title'=>$this->renderSummary().$this->title, 'nopadding'=>true]);
    	parent::run();
    	JarvisWidget::end();
    }
}