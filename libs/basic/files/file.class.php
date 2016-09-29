<?php
/**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2016
 *
 */
require_once 'libs/basic/model.php';

class File extends Model{
    public $_table = 'files';

    public $filename = '';
    public $extension = '';
    public $mimetype = '';
    public $size = 0.0;
    public $crtdate = 0;
    public $module = '';
    public $objectid = 0;
    public $_object = 0;

    const FILE_PATH = 'docs/files/';
    const FILE_PATH_THUMBNAIL = 'docs/files/thumbnails/';

    protected function bootClasses()
    {
        if ($this->module != '')
            $this->_object = new $this->module($this->objectid);
    }

    /**
     * @param $module
     * @param $objectid
     * @return File[]
     */
    public static function getAllForModuleAndObject($module, $objectid)
    {
        $retval = self::fetch([
            [
                'column'=>'module',
                'value'=>$module
            ],
            [
                'column'=>'objectid',
                'value'=>$objectid
            ]
        ]);
        return $retval;
    }
    
    public function getPreview($width,$height)
    {
        $file = $this->getFileUrl();
        $thumbnail = self::FILE_PATH_THUMBNAIL.$width.'_'.$height.'_'.self::generateFilename().'.jpg';
        if (!file_exists($thumbnail)){
            if ($this->extension == 'pdf'){
                $im = new imagick($file.'[0]');
                $im->setImageFormat('jpg');
                $im->thumbnailImage($width,$height,true);
                $im->writeImage($thumbnail);
            }
        }
        return $thumbnail;
    }

    public function getFileUrl()
    {
        return self::FILE_PATH.self::generateFilename();
    }

    public function generateFilename()
    {
        return $this->crtdate.'_'.$this->filename;
    }

    /**
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * @param string $filename
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;
    }

    /**
     * @return string
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * @param string $extension
     */
    public function setExtension($extension)
    {
        $this->extension = $extension;
    }

    /**
     * @return string
     */
    public function getMimetype()
    {
        return $this->mimetype;
    }

    /**
     * @param string $mimetype
     */
    public function setMimetype($mimetype)
    {
        $this->mimetype = $mimetype;
    }

    /**
     * @return float
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @param float $size
     */
    public function setSize($size)
    {
        $this->size = $size;
    }

    /**
     * @return int
     */
    public function getCrtdate()
    {
        return $this->crtdate;
    }

    /**
     * @param int $crtdate
     */
    public function setCrtdate($crtdate)
    {
        $this->crtdate = $crtdate;
    }

    /**
     * @return string
     */
    public function getModule()
    {
        return $this->module;
    }

    /**
     * @param string $module
     */
    public function setModule($module)
    {
        $this->module = $module;
    }

    /**
     * @return int
     */
    public function getObjectid()
    {
        return $this->objectid;
    }

    /**
     * @param int $objectid
     */
    public function setObjectid($objectid)
    {
        $this->objectid = $objectid;
    }

    /**
     * @return int
     */
    public function getObject()
    {
        return $this->_object;
    }

    /**
     * @param int $object
     */
    public function setObject($object)
    {
        $this->_object = $object;
    }
}