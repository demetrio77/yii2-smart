<?php

namespace demetrio77\smartadmin\widgets;

use yii\grid\GridView;

class DkGridView extends GridView 
{
    public $layout = '<div class="alert alert-info no-margin fade in">{pager}</div>{items}<div class="alert alert-info no-margin fade in">{pager}</div>';
    public $title;
    public $jarvisOptions;

    public function run() {
        $this->jarvisOptions['title'] = $this->renderSummary().$this->title;
       	JarvisWidget::begin($this->jarvisOptions);
    	parent::run();
    	JarvisWidget::end();
    }
}