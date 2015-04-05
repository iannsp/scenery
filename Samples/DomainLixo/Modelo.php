<?php
namespace Lixo;

class Lixo
{
    
}

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
    public function coloca(\Lixo\Lixo $lixo)
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

class ServicoDeLixo
{
    private $dataStructure;
    private $localDeRetirada;
    private $lixao;
    public function __construct($dataStructure)
    {
        $this->dataStructure = $dataStructure;
        $initialState = $this->dataStructure->get()['sistemaDeLixo'][1];
        $this->localDeRetirada = new LocalDeRetirada($initialState['localDeRetirada']);
        $this->lixao           = new Lixao($initialState['Lixao']);
    }
    public function coletarLixo()
    {
        $numeroDeLixeiros = rand(3,4);
        $lixeiros = [];
        for($i=0; $i<$numeroDeLixeiros; $i++)
        {
            $lixeiro= New Lixeiro();
            $lixeiro->retiraNoLocal($this->localDeRetirada);
            $lixeiro->armazenarNoLixao($this->lixao);
        }
        $this->persist();
    }
    
    public function produzirLixo()
    {
        $numeroDeMoradores = rand(1,15);
        $moradores = [];
        for($i=0; $i<$numeroDeMoradores; $i++)
        {
            $morador= New Morador();
            $morador->dispensaLixo($this->localDeRetirada);
        }
        $this->persist();
    }
    private function persist()
    {
        $dadosAtuais = $this->dataStructure->get(['sistemaDeLixo'=>[1]]);
        $quantoTemlocal = $dadosAtuais['sistemaDeLixo'][1]['localDeRetirada'];
        $quantoTemLixao = $dadosAtuais['sistemaDeLixo'][1]['Lixao'];
        $this->dataStructure->add(
        [
            'sistemaDeLixo'=>
            [
                ['key'=>1,[
                    'localDeRetirada'=>$this->localDeRetirada->quantoLixoTem(), 
                    'Lixao'=>$this->lixao->quantoLixoTem()]
                ]
            ]
        ]
        );
        
    }
    public function getInfo()
    {
        return [
            'data'=>$this->dataStructure,
            'localDeRetirada'=>$this->localDeRetirada,
            'lixao'=>$this->lixao
            ];
    }
}