<?php

namespace demetrio77\smartadmin\actions;

use Yii;
use yii\base\Action;

class OrderDownAction extends Action
{
	public $redirectRoute = '{back}';
	public $modelClass;
	
	public function run($id)
	{
		$model = Yii::createObject($this->modelClass);
		$model->findOne($id)->moveDown();
		if ($this->redirectRoute=='{back}') {
			return $this->controller->redirect(Yii::$app->request->referrer);
		}
		else {
			$this->controller->redirect($this->redirectRoute);
		}
	}
}