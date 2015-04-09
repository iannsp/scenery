<?php
namespace SampleDomain\Lixo;

class Lixao
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
            $this->armazena(new Lixo());
        }
    }
    public function armazena(Lixo $lixo)
    {
        return $this->lixos->push($lixo);
    }
}
