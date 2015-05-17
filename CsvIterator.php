<?php

namespace Csv;

class CsvIterator extends \ArrayIterator
{
    const ROW_DELIMITER = "\n";
    const COLUMN_DELIMITER = ",";

    private $data = array();
    private $input;
    private $filter;
    private $headers;

    /**
     * @param $input
     * @param null $filter
     */
    public function __construct($input, $filter = null)
    {
        $this->input = $input;
        $this->filter = $filter;

        $this->exportInputToArray();

        if ($this->filter) {
            $this->filterData();
        }
        parent::__construct($this->data);
    }

    public function exportInputToArray()
    {
        $rows = $this->parseEntry($this->input, self::ROW_DELIMITER);

        foreach ($rows as $key => $value) {
            $data = $this->parseEntry($value, self::COLUMN_DELIMITER);
            if (0 === $key) {
                $this->headers = $data;
            } else {
                $this->addToData($data);
            }
        }
    }

    /**
     * @param $value
     * @param string $delimiter
     * @return array
     */
    private function parseEntry($value, $delimiter = self::COLUMN_DELIMITER)
    {
        $entry = explode($delimiter, $value);

        if (!$entry) {
            throw new \InvalidArgumentException("Wrong data forma.");
        }

        return $entry;

    }

    /**
     * @param array $values
     */
    private function addToData(array $values)
    {
        $this->data[] = array_combine($this->headers, $values);
    }

    private function filterData()
    {
        $data = array_filter($this->data, function ($item) {
            if ($this->filterItem($item)) {
                return $item;
            }
        });

        $this->data = array_values($data); // this is needed to reset keys
    }

    private function filterItem(array $item)
    {
        foreach ($this->filter as $key => $value) {
            if ($item[$key] !== $value) {
                return false;
            }
        }
        return true;
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->data);
    }


}
