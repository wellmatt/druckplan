<?php
/**
 *  Copyright (c) 2017 Teuber Consult + IT GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <alexander.scherer@teuber-consult.de>, 2017
 *
 */
require_once 'libs/basic/model.php';


Class Permission extends Model{
    public $_table = 'permissions';

    public $name = '';
    public $description = '';
    public $slug = '';

    const ACCOUNTING_ADMIN = 'accounting_admin';
    const ASSOCIATION_DELETE = 'association_delete';
    const BC_DELETE = 'bc_delete';
    const BC_EDIT = 'bc_edit';
    const BC_NOTES = 'bc_notes';
    const CP_EDIT = 'cp_edit';
    const CP_DELETE = 'cp_delete';
    const CALC_STEP3 = 'calc_step3';
    const calc_delete = 'calc_delete';
    const calc_detail = 'calc_detail';
    const colinv_delete = 'colinv_delete';
    const colinv_combine = 'colinv_combine';
    const calendar_all = 'calendar_all';
    const calendar_all_see = 'calendar_all_see';
    const ticket_crtuser_edit = 'ticket_crtuser_edit';
    const ticket_commentoffical_edit = 'ticket_commentoffical_edit';
    const ticket_commentinternal_edit = 'ticket_commentinternal_edit';
    const vacation_grant = 'vacation_grant';

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
    }
}