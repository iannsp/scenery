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
    public function save(Data $repositorio)
    {
        $repositorio->add(
        [
            'person'=>[
                ["key"=>$this->data['id'],$this->data]
                    ]
        ]);
    }
}

class SceneryTest extends \PHPUnit_Framework_TestCase
{
    public function assertPreConditions()
    {
        $this->assertTrue(class_exists('Iannsp\Scenery\Scenery'));
    }
    
    public function test_init_data_model()
    {
        $scenery = new Scenery(new Data());
    }
    
    public function test_add_action()
    {
        assert_options(ASSERT_ACTIVE, 1);
        $data = new Data();
        $data->add([
            'person'=>[
                ["key"=>'1',['id'=>1,"name"=>'Ivo','email'=>'iannsp@gmail.com']]
                ]
        ]);
        $scenery = new Scenery($data);
        $scenery->action('Altera Uma Pessoa', function()use ($data){
           $pessoaData = $data->get(['person'=>[1]])['person'][1];
           $pessoa = new PessoaSample($pessoaData);
           $pessoa->data['nome'] = "Ivo Nascimento";
           $pessoa->save($data);
        }, function() use($data){
            $pessoaData = $data->get(['person'=>[1]])['person'][1];
            $pessoa = new PessoaSample($pessoaData);
            assert('$pessoa->data[\'nome\']=="Ivo Nascimento"',"ahhhhhh");
        }
    );
        
    }
    
    public function test_run_actions()
    {
        assert_options(ASSERT_ACTIVE, 1);
        $data = new Data();
        $data->add([
            'person'=>[
                ["key"=>'1',['id'=>1,"name"=>'Ivo','email'=>'iannsp@gmail.com']]
                ]
        ]);
        $scenery = new Scenery($data);
        $scenery->action('Altera Uma Pessoa', function()use ($data){
           $pessoaData = $data->get(['person'=>[1]])['person'][1];
           $pessoa = new PessoaSample($pessoaData);
           $pessoa->data['nome'] = "Ivo Nascimento";
           $pessoa->save($data);
        }, function() use($data){
            $pessoaData = $data->get(['person'=>[1]])['person'][1];
            $pessoa = new PessoaSample($pessoaData);
            assert('$pessoa->data[\'nome\']=="Ivo Nascimento"',"ahhhhhh");
        }
    );
    $result = $scenery->run(1);
    $this->assertTrue(is_array($result));
    $this->assertCount(1, $result);
    $this->assertArrayHasKey('data', $result);
    }
    
    public function test_run_actions_lot_of_cycle()
    {
        assert_options(ASSERT_ACTIVE, 1);
        $data = new Data();
        $data->add([
            'person'=>[
                ["key"=>'1',['id'=>1,"name"=>'Ivo','email'=>'iannsp@gmail.com']]
                ]
        ]);
        $scenery = new Scenery($data);
        $scenery->action('Altera Uma Pessoa', function($state){
           $pessoaData = $state['new']->get(['person'=>[1]])['person'][1];
           $pessoa = new PessoaSample($pessoaData);
           $pessoa->data['name'] = $pessoa->data['name']."X";
           $pessoa->save($state['new']);
        }, function($state){
            $newPessoaData = $state['new']->get(['person'=>[1]])['person'][1];
            $oldPessoaData = $state['old']->get(['person'=>[1]])['person'][1];
          assert('$oldPessoaData[\'name\']."X"==$newPessoaData["name"]',"Nao esta seguindo a regra");
        }
    );
    $runnerStrategy = Factory::get(Strategy::RUN_BY_CYCLE_NUMBER,$scenery);
    $result = $runnerStrategy->run(10);
    $this->assertTrue(is_array($result));
    $this->assertCount(10, $result);
    $this->assertArrayHasKey('data', $result[0]);
    $final = $result[9]['data']->get(['person'=>[1]])['person'][1];
    $this->assertEquals($final['name'], "IvoXXXXXXXXXX");
    }
    
    public function test_run_cycle_byDateTimeLimit()
    {
        $data = new Data();
        $data->add([
            'person'=>[
                ["key"=>'1',['id'=>1,"name"=>'Ivo','email'=>'iannsp@gmail.com']]
                ]
        ]);
        $scenery = new Scenery($data);
        $scenery->action('Altera Uma Pessoa', function($state){
           $pessoaData = $state['new']->get(['person'=>[1]])['person'][1];
           $pessoa = new PessoaSample($pessoaData);
           $pessoa->data['name'] = $pessoa->data['name']."X";
           $pessoa->save($state['new']);
        }, function($state){
            $newPessoaData = $state['new']->get(['person'=>[1]])['person'][1];
            $oldPessoaData = $state['old']->get(['person'=>[1]])['person'][1];
            \Assert\that($newPessoaData['name'])->contains('IvoX');
        }
    );
    
    $rodarAte = new \Datetime();
    $rodarAte->add(new \DateInterval("P0YT0M4S"));
    $runnerStrategy = factory::get(Strategy::RUN_UNTILDATE,$scenery);
    $result = $runnerStrategy->run(['until'=>$rodarAte,'by'=>1]);
    $this->assertTrue(is_array($result));
    $this->assertCount(4, $result);
    $this->assertArrayHasKey('data', $result[0]);
    $final = $result[3]['data']->get(['person'=>[1]])['person'][1];
    $this->assertEquals($final['name'], "IvoXXXX");
    }
}
    

?>