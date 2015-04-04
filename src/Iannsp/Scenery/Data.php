<?php
namespace Iannsp\Scenery;

class Data
{
    private $data=[];
    public function __construct(array $initialStructure=[])
    {
        $this->data = [];
    }
    public function add(array $structure)
    {
        foreach($structure as $model => $record)
        {
            foreach ($record as $each){
                $key = array_key_exists('key',$each)?$each['key']:null;
                $valueInKey = $each[0];
                if (!array_key_exists($model, $this->data)){
                    $this->data[$model] = [];
                }
                $this->data[$model][$key]=$valueInKey;
            }
        }
    }
    public function get()
    {
        return $this->data;
    }

}    
?>