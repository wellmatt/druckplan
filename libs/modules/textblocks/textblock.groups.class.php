<?php
/**
 *  Copyright (c) 2017 Teuber Consult + IT GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <alexander.scherer@teuber-consult.de>, 2017
 *
 */
require_once 'libs/basic/model.php';

class TextBlockGroup extends Model{
    public $_table = 'textblocks_groups';

    public $textblock = 0;
    public $group = 0;

    protected function bootClasses()
    {
        $this->group = new Group($this->group);
    }

    public static function getAllForTextblock(TextBlock $textBlock)
    {
        return self::fetch([
            [
                'column' => 'textblock',
                'value' => $textBlock->getId()
            ]
        ]);
    }

    /**
     * @return int
     */
    public function getTextblock()
    {
        return $this->textblock;
    }

    /**
     * @param int $textblock
     */
    public function setTextblock($textblock)
    {
        $this->textblock = $textblock;
    }

    /**
     * @return Group
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * @param Group $group
     */
    public function setGroup($group)
    {
        $this->group = $group;
    }
}