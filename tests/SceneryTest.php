<?php
namespace Iannsp\Scenery;
use Assert\Assertion;
use \Iannsp\Scenery\RunStrategy\Factory;
use \Iannsp\Scenery\RunStrategy\Strategy;
class PessoaSample
{
    public $data;
    public function __construct(array $data)
    {
        $this->data = $data;
    }
    public function save(\PDO $pdo)
    {
        $result = $pdo->query("select count(id) from person where id={$this->data['id']}", \PDO::FETCH_ASSOC);
        $exist = $result->fetchAll()[0];
        if($exist['count(id)']==1){
            $result = $pdo->exec("update person set name='{$this->data['name']}', email='{$this->data['email']}' where id={$this->data['id']}");
        } else{
            $pdo->exec("insert into person (name, email) values ('{$this->data['name']}','{$this->data['email']}')");
        }
    }
    public static function find($pdo, $id)
    {
        $r = $pdo->query("select * from person where id={$id}", \PDO::FETCH_ASSOC);
      $pessoaData = $r->fetchAll()[0];
      return $pessoaData;
    }
}

class SceneryTest extends \PHPUnit_Framework_TestCase
{
    use Connection;

    public function assertPreConditions()
    {
        $this->assertTrue(class_exists('Iannsp\Scenery\Scenery'));
    }

    public function test_init_data_model()
    {
        $scenery = new Scenery($this->pdo);
    }

    public function test_add_action()
    {
        $scenery = new Scenery($this->pdo);
        $pdo = $this->pdo;
        $scenery->action('Altera Uma Pessoa', function()use ($pdo){
             $r = $pdo->query("select * from person where id=1", \PDO::FETCH_ASSOC);
           $pessoaData = $r->fetchAll()[0];
           $pessoa = new PessoaSample($pessoaData);
           $pessoa->data['name'] = "Ivo Nascimento";
           $pessoa->save($pdo);
        }, function() use($pdo){
            $r = $pdo->query("select * from person where id=1", \PDO::FETCH_ASSOC);
          $pessoaData = $r->fetchAll()[0];
            $pessoa = new PessoaSample($pessoaData);
            assert('$pessoa->data[\'name\']=="Ivo Nascimento"',"ahhhhhh");
        }
    );

    }

    public function test_run_actions()
    {
        assert_options(ASSERT_ACTIVE, 1);
        $scenery = new Scenery($this->pdo);
        $pdo = $this->pdo;
        $scenery->action('Altera Uma Pessoa', function($state)use ($pdo){
           $pessoaData = PessoaSample:: find($pdo, 1);
           $pessoa = new PessoaSample($pessoaData);
           $state->messages[]="update Person {$pessoa->data['name']} para 'Ivo Nascimento'";
           $pessoa->data['name'] = "Ivo Nascimento";
           $pessoa->save($pdo);
        }, function($state) use($pdo){
            $pessoaData = PessoaSample:: find($pdo, 1);
            $pessoa = new PessoaSample($pessoaData);
            $state->messages[]= " Verificação de Alteração name = {$pessoaData['name']}";
            assert('$pessoa->data[\'name\']=="Ivo Nascimento"',"ahhhhhh");
        }
    );
    $runnerStrategy = Factory::get(Strategy::RUN_BY_CYCLE_NUMBER,$scenery);
    $result = $runnerStrategy->run(['cycles'=>1, 'loud'=>false]);
    $this->assertTrue(is_array($result));
    $this->assertCount(1, $result);
}

    public function test_run_actions_lot_of_cycle()
    {
        $scenery = new Scenery($this->pdo);

        $scenery->action('Altera Uma Pessoa', function($state){
           $pessoaData = PessoaSample::find($state->new, 1);
           $pessoa = new PessoaSample($pessoaData);
           $pessoa->data['name'] = $pessoa->data['name']."X";
           $pessoa->save($state->new);
        }, function($state){
            $newPessoaData = PessoaSample:: find($state->new, 1);
            $oldPessoaData = PessoaSample:: find($state->old, 1);
          assert('$oldPessoaData[\'name\']."X"==$newPessoaData["name"]',"Nao esta seguindo a regra");
        }
    );
    $runnerStrategy = Factory::get(Strategy::RUN_BY_CYCLE_NUMBER,$scenery);
    $result = $runnerStrategy->run(['cycles'=>10, 'loud'=>false]);
    $this->assertTrue(is_array($result));
    $this->assertCount(10, $result);
    $final = PessoaSample:: find($this->pdo, 1);
    $this->assertEquals($final['name'], "IvoXXXXXXXXXX");
    }

    public function test_run_cycle_byDateTimeLimit()
    {
        $scenery = new Scenery($this->pdo);
        $scenery->action('Altera Uma Pessoa', function($state){
           $pessoaData = PessoaSample:: find($state->old, 1);
           $pessoa = new PessoaSample($pessoaData);
           $state->messages[]= "[ACTION] Nome Original = {$pessoaData['name']}";
           $pessoa->data['name'] = $pessoa->data['name']."X";
           $state->messages[]= "[ACTION] Nome Alterado +1'X' = {$pessoaData['name']} para {$pessoa->data['name']}";
           $pessoa->save($state->new);
        }, function($state){
            $newPessoaData = PessoaSample:: find($state->new, 1);
            $oldPessoaData = PessoaSample:: find($state->old, 1);
            $state->messages[]= "[DOMAIN] Old:{$oldPessoaData['name']}, new: {$newPessoaData['name']}";
            \Assert\that($newPessoaData['name'])->contains('IvoX');
        }
    );

    $rodarAte = new \DateTime();
    $rodarAte->add(new \DateInterval("P0YT0M4S"));
    $runnerStrategy = Factory::get(Strategy::RUN_UNTILDATE,$scenery);
    $result = $runnerStrategy->run(['until'=>$rodarAte,'by'=>1,'loud'=>false]);
    $this->assertTrue(is_array($result));
    $this->assertCount(4, $result);

    $final = PessoaSample:: find($this->pdo, 1);
    $this->assertEquals($final['name'], "IvoXXXX");
    }
}


?>