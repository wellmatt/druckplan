<?php
/**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2016
 *
 */


class Backup{
    public $time = 0;
    public $filelist = [];
    public $folder;

    /**
     * Backup constructor.
     */
    public function __construct()
    {
        $this->time = time();
        $folder = __DIR__;
        $this->folder = str_replace('/libs/modules/backup','',$folder);
        $this->getFilelist();
    }

    public function getFilelist()
    {
        $files = [];
        if ($handle = opendir('.')) {
            while (false !== ($entry = readdir($handle))) {
                if ($entry != "." && $entry != ".." && $entry != "backups") {
                    if (is_dir($entry)) {
                        $directory_path = $entry.DIRECTORY_SEPARATOR;
                        $files[]  = $directory_path;
                    } elseif (is_file($entry)) {
                        $files[]  = $entry;
                    }
                }
            }
            closedir($handle);
        }
        $this->filelist = $files;
    }

    public static function formatBytes($bytes, $precision = 2) {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        // Uncomment one of the following alternatives
//         $bytes /= pow(1024, $pow);
         $bytes /= (1 << (10 * $pow));

        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    public static function getSqlBackups()
    {
        $folder = __DIR__;
        $folder = str_replace('/libs/modules/backup','',$folder);
        $dir = $folder.'/backups/sql/';
        return self::getFiles($dir);
    }

    public static function getFileBackups()
    {
        $folder = __DIR__;
        $folder = str_replace('/libs/modules/backup','',$folder);
        $dir = $folder.'/backups/files/';
        return self::getFiles($dir);
    }

    public static function getFiles($dir)
    {
        if (is_dir($dir)) {
            if ($handle = opendir($dir)) {
                $files = array();
                while (false !== ($file = readdir($handle))) {
                    if (is_file($dir.$file) && substr($file,0,1) != '.') {
                        $time = substr($file,0,strpos($file,'.'));
                        $time = date('d.m.y H:i',$time);
                        $files[] = ['name' => $file, 'size' => self::formatBytes(filesize($dir . $file)), 'time' => $time];
                    }
                }
                closedir($handle);
                if (is_array($files)) sort($files);
                return $files;
            }
        }
    }

    public function saveFiles()
    {
        $folder = $this->folder;
        $filelist = implode(' ',$this->filelist);
        chdir($folder);
        exec('tar -cz  -f "'.$folder.'/backups/files/'.$this->time.'.tar.gz" '.$filelist);
    }

    public function saveDB()
    {
        global $_CONFIG;
        $folder = $this->folder;
        $backupFile = $folder.'/backups/sql/'.$this->time.'.gz';
        $command = "mysqldump --opt -h {$_CONFIG->db->host} -u {$_CONFIG->db->user} -p{$_CONFIG->db->pass} {$_CONFIG->db->name} | gzip > {$backupFile}";
        exec($command);
    }


}