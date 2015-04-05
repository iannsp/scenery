<?php
namespace Iannsp\Scenery;

class Data
{
    private $data=[];
    public function __construct(array $initialStructure = [])
    {
        $this->add($initialStructure);
    }
    public function add(array $structure)
    {
        foreach ($structure as $model => $record) {
            foreach ($record as $each) {
                $key = array_key_exists('key', $each)?$each['key']:null;
                $valueInKey = $each[0];
                if (!array_key_exists($model, $this->data)) {
                    $this->data[$model] = [];
                }
                if (is_null($key)) {
                    $this->data[$model][]=$valueInKey;
                } else {
                    $this->data[$model][$key]=$valueInKey;
                }
            }
        }
    }
    public function get(array $strucutre = [])
    {
        if (!count($strucutre)) {
            return $this->data;
        }
        $result = [];
        
        foreach ($strucutre as $model => $lookFor) {
            if (!array_key_exists($model, $result)) {
                $result[$model] = [];
            }
            if (!count($lookFor)) {
                $result[$model]= $this->data[$model];
            }

            foreach ($lookFor as $item) {
                if (array_key_exists($item, $this->data[$model])) {
                    $result[$model][$item] = $this->data[$model][$item];
                }
            }
        }
        return $result;
            
    }
}
