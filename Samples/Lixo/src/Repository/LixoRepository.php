<?php
namespace SampleDomain\Lixo\Repository;

class LixoRepository
{
    private $pdo;
    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }
    public function find($id)
    {
        $r = $this->pdo->query("select * from sistemaDeLixo where id={$id}", \PDO::FETCH_ASSOC);
      $dados = $r->fetchAll();
      return $dados[0];
    }
    public function save(array $data)
    {
        $r = $this->pdo->exec("update sistemaDeLixo set localDeRetirada={$data['localDeRetirada']}, Lixao={$data['Lixao']} where id={$data['id']}");
        
    }
}
