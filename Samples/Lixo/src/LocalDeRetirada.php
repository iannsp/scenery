<?php
namespace SampleDomain\Lixo;

class LocalDeRetirada
{
    private $lixos;
    
    public function quantoLixoTem()
    {
        return count($this->lixos);
    }
    public function __construct($QuantidadedeLixos=0)
    {
        $this->lixos = new \SplStack();
        for($i=0; $i<$QuantidadedeLixos; $i++){
            $this->coloca(new Lixo());
        }
    }
    public function coloca(Lixo $lixo)
    {
        $this->lixos->push($lixo);
    }
    
    public function recolhe($quantidade=1)
    {
        $result = [];
        for($i=0; $i<$quantidade; $i++){
            try{
                $result[]= $this->lixos->pop();
            }catch(\Exception $e){
                return $result;
            }
        }
        return $result;
    }
}
