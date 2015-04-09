<?php
namespace SampleDomain\Lixo;

class Morador
{
    public function dispensaLixo(LocalDeRetirada $local)
    {
        $quantidade = rand(0, 3);
        $lixos = [];
        for($i=0; $i<$quantidade; $i++)
        {
            $local->coloca(new Lixo());
        }
    }
}
