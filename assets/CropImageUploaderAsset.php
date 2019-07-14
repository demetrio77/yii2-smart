<?php
namespace demetrio77\smartadmin\assets;

class CropImageUploaderAsset extends BaseAsset
{
    public $sourcePath = '@demetrio77/smartadmin/assets/cropImageUploader';
    public $js = ['cropImageUploader.js'];
    public $depends = ['demetrio77\smartadmin\assets\SmartAdminAsset','demetrio77\smartadmin\assets\JcropAsset','demetrio77\smartadmin\assets\FileUploaderAsset'];
}