<?php

require_once 'CsvIterator.php';

use Csv\CsvIterator;

class IteratorTest extends \PHPUnit_Framework_TestCase
{
    private function getExampleData()
    {
        return
            'region,city,population' . "\n" .
            'MI,Detroit,5050' . "\n" .
            'WI,Madison,1777' . "\n" .
            'PA,Philadelphia,1824' . "\n" .
            'OH,Cincinnati,8223' . "\n" .
            'HI,Honolulu,5353' . "\n" .
            'PA,Omaha,4814' . "\n" .
            'WI,Seattle,5427' . "\n" .
            'ID,Nampa,9218' . "\n" .
            'PA,Topeka,7541' . "\n" .
            'WI,Davenport,1887';
    }


    public function testSetHeaders()
    {
        $csv = new CsvIterator($this->getExampleData());


        $this->assertEquals($csv->getHeaders(), array('region', 'city', 'population'));
    }

    public function testAddToData()
    {
        $csv = new CsvIterator($this->getExampleData());

        $this->assertEquals(count($csv), 10);
    }

    public function testDataWithFilter1()
    {
        $filter = array('region' => 'PA');
        $csv = new CsvIterator($this->getExampleData(), $filter);

        $this->assertEquals(count($csv), 3);
    }

    public function testDataWithFilter2()
    {
        $filter = array('region' => 'PA', 'city' => 'Omaha');
        $csv = new CsvIterator($this->getExampleData(), $filter);

        $this->assertEquals(count($csv), 1);
    }

    public function testParseEntryWithCommaDelimiter()
    {
        $csv = new CsvIterator($this->getExampleData());
        $parsed = $this->invokeMethod($csv, 'parseEntry', array('a,b,c', ','));
        $this->assertEquals($parsed, array('a','b','c'));
    }

    public function testParseEntryWithNewLineDelimiter()
    {
        $csv = new CsvIterator($this->getExampleData());
        $parsed = $this->invokeMethod($csv, 'parseEntry', array("a,b,c\nd,e,f", "\n"));
        $this->assertEquals($parsed, array('a,b,c', 'd,e,f'));
    }

    /**
     * Call protected/private method of a class.
     *
     * @param object &$object    Instantiated object that we will run method on.
     * @param string $methodName Method name to call
     * @param array  $parameters Array of parameters to pass into method.
     *
     * @return mixed Method return.
     */
    public function invokeMethod(&$object, $methodName, array $parameters = array())
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }

}
