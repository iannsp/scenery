<?php
namespace SampleDomain\Lixo;

class Lixeiro
{
    private $lixos = [];
    public function retiraNoLocal(LocalDeRetirada $local)
    {
        $this->lixos = $local->recolhe(rand(3,6));
    }
    public function armazenarNoLixao(Lixao $lixao)
    {
        do{
            $lixo  = array_shift($this->lixos);
            if (!is_null($lixo))
                $lixao->armazena($lixo);
        }while(!is_null($lixo));
    }
}
