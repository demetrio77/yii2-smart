<?php

namespace demetrio77\smartadmin\widgets;

use yii\grid\GridView;

class DkGridView extends GridView
{
    public $layout = '{items}<div class="row"><div class="col-sm-12 col-md-5"><div class="dataTables_info" role="status" aria-live="polite">{summary}</div></div><div class="col-sm-12 col-md-7"><div class="dataTables_paginate paging_simple_numbers">{pager}</div></div></div>';
    public $title;
    public $jarvisOptions;

    public function run() {
        $this->jarvisOptions['title'] = $this->title;
        $this->headerRowOptions['class'] = 'bg-warning-200';
        $this->filterRowOptions['class'] = 'filters bg-warning-50';
       	JarvisWidget::begin($this->jarvisOptions);
    	parent::run();
    	JarvisWidget::end();
    }
}
