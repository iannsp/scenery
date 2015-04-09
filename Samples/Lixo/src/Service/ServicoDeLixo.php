<?php
namespace SampleDomain\Lixo\Service;
use SampleDomain\Lixo\Repository\LixoRepository;
use SampleDomain\Lixo\LocalDeRetirada;
use SampleDomain\Lixo\Lixao;
use SampleDomain\Lixo\Morador;
use SampleDomain\Lixo\Lixeiro;

class ServicoDeLixo
{
    private $dataStructure;
    private $localDeRetirada;
    private $lixao;
    private $repository;
    public function __construct($dataStructure)
    {
        $this->dataStructure = $dataStructure;
        $this->repository = new LixoRepository($dataStructure);
        $initialState = $this->repository->find(1);
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
        $dadosAtuais = $this->repository->find(1);
        $quantoTemlocal = $dadosAtuais['localDeRetirada'];
        $quantoTemLixao = $dadosAtuais['Lixao'];
        $this->repository->save(
        [
            'id'=>1,
            'localDeRetirada'=>$this->localDeRetirada->quantoLixoTem(), 
            'Lixao'=>$this->lixao->quantoLixoTem()
        ]);
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