<?php
/**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2016
 *
 */


class Configuration{
    public $settings = [
        'calc_zuschussprodp' => '0',
        'calc_zuschusspercent' => '0',
        'calc_detailed_printpreview' => '0',
        'datatables_showelements' => '25',
        'datatables_statesave' => '0',
    ];

    /**
     * Configuration constructor.
     * @param array $settings
     */
    public function __construct(array $settings = [])
    {
        global $DB;
        $dbsettings = [];
        $sql = "SELECT * FROM configuration";
        if($DB->num_rows($sql))
        {
            $result = $DB->select($sql);
            foreach($result as $r){
                $dbsettings[$r["setting"]] = $r["value"];
            }
        }

        $this->allocateSettings($dbsettings);
        $this->allocateSettings($settings);
    }

    private function allocateSettings(array $settings){
        foreach ($settings as $setting => $value) {
            if (array_key_exists($setting,$this->settings)){
                $this->settings[$setting] = $value;
            }
        }
    }

    public function getSetting($setting)
    {
        if (array_key_exists($setting,$this->settings)){
            return $this->settings[$setting];
        }
        return null;
    }
}