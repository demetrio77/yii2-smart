<?php

namespace demetrio77\smartadmin\behaviors;

use Yii;
use yii\base\Behavior;
use yii\db\ActiveRecord;
use yii\helpers\Inflector;
use demetrio77\smartadmin\helpers\TransliteratorHelper;

class SlugBehavior extends Behavior
{
    const DEFAULT_SLUG_TEMPLATE = '{slug}';
    public $dataAttribute;
    public $slugAttribute = 'slug';
    public $forceTranslit = true;
    public $slugFieldTemplate = '{slug}';
    public $forceUniqueSlug = true;
    
    public function events()
    {
    	return [
    	   ActiveRecord::EVENT_BEFORE_VALIDATE => 'getSlug'
    	];
    }
    
    public static function slug($value)
    {
        $value = strip_tags($value);
        $value = html_entity_decode($value);
        $cyrillic = [
            'ё' => 'jo','ж' => 'zh','й' => 'j','х' => 'h','ц' => 'ts','ч' => 'ch','ш' => 'sh','щ' => 'sch',
            'ъ' => 'j','ы' => 'y','ь' => '','э' => 'e','ю' => 'ju','я' => 'ja',
            'Ё' => 'Jo','Ж' => 'Zh','Й' => 'J','Х' => 'H','Ц' => 'Ts','Ч' => 'Ch','Ш' => 'Sh','Щ' => 'Sch',
            'Ъ' => 'J','Ы' => 'Y','Ь' => '','Э' => 'E','Ю' => 'Ju','Я' => 'Ja',
        ];
        $value = str_replace(array_keys($cyrillic), array_values($cyrillic), $value);
    	return Inflector::slug( TransliteratorHelper::process( $value ), '-', true );
    }
    
    public function getSlug( $event )
    {
    	$model = $this->owner;
        $model->{$this->slugAttribute} = $this->generateSlug( $this->dataAttribute ? $model->{$this->dataAttribute} :'' );
    }
    
    private function generateSlug ($value) 
    {
        $slug = $this->processSlug( $value );
        
        if ( $this->forceUniqueSlug ) {
            $i = 2;
            $baseSlug = $slug; 
            while (! $this->isUniqueSlug($slug)) {
                $slug = $baseSlug.'-'.$i;
                $i++;
            }
        }
        return $slug;
    }
    
    private function processSlug ($value)
    {
        if ( $this->slugFieldTemplate != self::DEFAULT_SLUG_TEMPLATE) {
        	$model = $this->owner;
            if ($model->isNewRecord) {
            	$Schema  = $model->getTableSchema();
            }
            $search  = [ self::DEFAULT_SLUG_TEMPLATE ];
            $replace = [ $value ];
            foreach ( $model->attributes as $attribute => $value) {
                if ($model->isNewRecord) {
                	$Sattr = $Schema->columns[$attribute];
                	if ($Sattr->isPrimaryKey && $Sattr->autoIncrement) {
                		$sql = "SHOW TABLE STATUS LIKE '".$model->tableName()."'";
                		$connection = Yii::$app->db;
                		$Table_props = $connection->createCommand($sql)->queryOne();
                		$value = $Table_props['Auto_increment'];
                	}
                }
            	
            	if ( $attribute != 'slug' ) { 
                    $search[] = '{'.$attribute.'}';
                    $replace[] = $value;
                }
            }
            $value = str_replace($search, $replace, $this->slugFieldTemplate);
        }
        return $this->slugify( $value );
    }
    
    private function slugify( $value ) 
    {
    	if (! $this->forceTranslit ) return $value;
    	return self::slug($value);
    }
    
    private function isUniqueSlug( $value )
    {
        $model = $this->owner;
        $pk = $model->primaryKey();
        
        $where = '('.$this->slugAttribute.'=:slugValue)';
        $params = [ ':slugValue' => $value ];
        if (! $model->isNewRecord ) {
        	$pkWhere  = 'false';
            foreach ($pk as $attribute) {
                $pkWhere .= ' OR ( '.$attribute.'<>:'.$attribute.')';
                $params[':'.$attribute] = $model->{$attribute};
            }
            $where .= ' AND ('.$pkWhere.')';
        }
        
        return !$model->find()->where( $where, $params )->exists();
    }
}