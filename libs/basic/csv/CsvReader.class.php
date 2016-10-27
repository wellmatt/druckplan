<?php

/**
 * Read a CSV file
 *
 * @SuppressWarnings(PHPMD)
 */
class CsvReader
{
    protected $handle;
    protected $delimiter;
    protected $enclosure;
    protected $line;
    protected $headers;
    protected $skipemptylines;


    /**
     * Open CSV file
     *
     * @param object  $file       the file
     * @param string  $delimiter  the delimiter
     * @param string  $mode       the mode
     * @param string  $enclosure  the enclosure
     * @param boolean $hasHeaders if has headers
     */
    public function open($file, $delimiter = ',', $mode = 'r+', $enclosure = '"', $hasHeaders = true, $skipEmptyLines = true)
    {
        $this->handle = fopen($file, $mode);
        $this->delimiter = $delimiter;
        $this->enclosure = $enclosure;
        $this->hasHeaders = $hasHeaders;
        $this->skipemptylines = $skipEmptyLines;
        $this->line = 0;

        if ($this->hasHeaders) {
            $this->headers = $this->formatHeaders($this->getRow());
        }
    }

    /**
     * Safely Return the number of lines
     * restore the line
     *
     * @return int the number of rows
     */
    public function safeCountRowNumber()
    {
        $line = $this->getLine();
        $count = count($this->getAll());
        $this->line = $line;

        return $count;
    }

    /**
     * Return the number of lines
     *
     * @return int the number of rows
     */
    public function getRowNumber()
    {
        return count($this->getAll());
    }

    /**
     * Return true if file has one unique row
     *
     * @return boolean
     */
    public function hasUniqueRow()
    {
        if ($this->getRowNumber() == 1) {
            return true;
        }

        return false;
    }

    /**
     * Return a row
     *
     * @return object row read by the reader
     */
    public function getRow()
    {
        do {
            $row = fgetcsv($this->handle, 0, $this->delimiter, $this->enclosure);
        } while ($this->skipemptylines && $row === array(null));

        if ($row === false) {
            return false;
        }

        $this->line++;
        return $row;
    }

    /**
     * Return entire table
     *
     * @return array results
     */
    public function getAll()
    {
        $data = array();

        while ($row = $this->getRow()) {
            $data[] = $row;
        }

        // Rewind
        rewind($this->handle);
        if ($this->hasHeaders) {
            $this->getRow();
        }

        return $data;
    }

    /**
     * Get headers
     *
     * @return array the headers
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Format header names
     *
     * @param HeaderRow $row the row
     *
     * @return array the formatted headers
     */
    public function formatHeaders($row)
    {
        $headers = array();
        foreach ($row as $k => $v) {
            $headers[] = $v;
        }

        return $headers;
    }

    /**
     * Get line
     *
     * @return array the current line
     */
    public function getLine()
    {
        return $this->line;
    }

    /**
     * Close file
     */
    public function __destruct()
    {
        if (is_resource($this->handle)) {
            fclose($this->handle);
        }
    }
}