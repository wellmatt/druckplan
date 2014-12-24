<?php

/* This Driver uses the SQL-Srv package,
 * available at: http://msdn.microsoft.com/en-us/library/cc296203.aspx */

class DBMssql {
    private $conn = FALSE;
    private $dbhost = "";
    private $dbname = "";
    private $dbpass = "";
    private $dbuser = "";
     
    // Constructor
    function __construct() {
    }
     
    // connect to mysql
    function connect($conf) {
        $this->dbhost = $conf->host;
        $this->dbuser = $conf->user;
        $this->dbpass = $conf->pass;
        $this->dbname = $conf->name;

        $this->conn = sqlsrv_connect($this->dbhost, Array(  "Database" => $this->dbname,
                "UID" => $this->dbuser,
                "PWD" => $this->dbpass));
        if ($this->conn)
            return true;
        else
            return false;
    }
     
    // Disconnect
    function disconnect() {
        mssql_close($this->conn);
        $this->conn = false;

        return false;
    }
     
    // select-anweisung
    function select($sql) {
        $retval = Array();
        $res = sqlsrv_query($this->conn, $sql);
        if ($res) {
            while ($data = sqlsrv_fetch_array($res, SQLSRV_FETCH_ASSOC))
            {   
                $retval[] = $data;
            }
            return $retval;
        } else
            return false;
    }
     
    // falls keine Daten ausgelesen werden
    function no_result($sql) {
        $res = sqlsrv_query($this->conn, $sql);
        if ($res)
            return true;
        else
            return false;
    }
     
    // num_rows
    function num_rows($sql) {
        $res = sqlsrv_query($this->conn, $sql);
        if ($res)
            return sqlsrv_num_rows($res);
        else
            return 0;
    }
     
    // get last error
    function getLastError()
    {
        return sqlsrv_errors();
    }
     
}
?>