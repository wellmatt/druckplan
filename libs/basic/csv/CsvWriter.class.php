<?php

/**
 * Creates a CSV file from an array
 *
 * @SuppressWarnings(PHPMD)
 */
class CsvWriter
{
    protected $handle;
    protected $delimiter;
    protected $enclosure;
    protected $line;
    protected $headers;

    /**
     * Open CSV file
     *
     * @param object $file      the file
     * @param string $delimiter the delimiter
     * @param string $mode      the mode
     * @param string $enclosure the enclosure
     */
    public function open($file, $delimiter = ',', $mode = 'r+', $enclosure = '"')
    {
        file_put_contents($file, '');
        $this->handle = fopen($file, $mode);
        $this->delimiter = $delimiter;
        $this->enclosure = $enclosure;
        $this->line = 0;
    }

    /**
     * Convert to CSV row
     *
     * @param array   $row      The data to convert
     * @param boolean $addBreak Adds linebreak if true
     *
     * @return value
     */
    public function convertRow($row, $addBreak = true)
    {
        $formatValue = function($value) {
            if ($value instanceof \Datetime) {
                $value = date_format($value, 'Y-m-d');
            }
            $value = trim($value);
            $value = str_replace("\r\n", "", $value);
            $value = str_replace("\n", "", $value);

            return $value;
        };

        if (is_array($row)) {
            $row = array_map($formatValue, $row);
        } else {
            $row = explode(',', $row);
            $row = array_map('trim', $row);
        }
        if ($addBreak) {
            $row = <<<EOT
$row

EOT;
        }

        return $row;
    }

    /**
     * Write a row in the CSV file
     *
     * @param array $row The data to add to the CSV
     *
     * @return fput
     */
    public function writeRow($row)
    {
        $row = $this->convertRow($row, false);

        return fputcsv($this->handle, $row, $this->delimiter, $this->enclosure);
    }

    /**
     * Write an arrow of data to the CSV file
     *
     * @param array $array An array of data to write
     */
    public function writeFromArray(array $array)
    {
        foreach ($array as $key => $value) {
            $this->writeRow($value);
        }
    }

    /**
     * Write the header of the CSV file
     *
     * @param array $header The data to add to the header of the CSV
     *
     * @return fput
     */
    public function writeHeader($header)
    {
        $row = $this->convertRow($header, false);

        return fputcsv($this->handle, $row, $this->delimiter, $this->enclosure);
    }

    /**
     * Close file if necessary
     */
    public function __destruct()
    {
        if (is_resource($this->handle)) {
            fclose($this->handle);
        }
    }

}