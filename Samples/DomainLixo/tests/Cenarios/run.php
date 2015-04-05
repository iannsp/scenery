<?php
require __DIR__.'/../../Modelo.php';
require __DIR__.'/../../../../bootstrap.php';

// Um dia qualquer numa cidade qualquer

use \Lixo\Lixo;
use \Lixo\Lixeiro;
use \Lixo\Morador;
use \Lixo\LocalDeRetirada;
use \Lixo\ServicoDeLixo;
use \Lixo\LixoRepository;

use Iannsp\Scenery\Scenery;
use Iannsp\Scenery\Data;
use Iannsp\Scenery\RunStrategy\Strategy;
use Iannsp\Scenery\RunStrategy\Factory;

use Assert\Assertion;

$dsn ='sqlite:/tmp/lixeiro.sq3';
$pdo = new \PDO( $dsn);
$pdo->exec('
drop table sistemaDeLixo; 
create table sistemaDeLixo(id integer primary key, localDeRetirada integer, Lixao integer)');
$pdo->exec("insert into sistemaDeLixo (localDeRetirada, Lixao) values (0,0)");
$pdo->newFromDsn = new \PDO( $dsn);

    $scenery = New Scenery($pdo);
    $scenery->action('Produzir Lixo', function($state){
        $umDiaQualquer = new ServicoDeLixo($state['new']);    
        $umDiaQualquer->produzirLixo();
    }, function($state){
        $repoNew = new LixoRepository($state['new']);
        $repoOld = new LixoRepository($state['old']);
        $newStatelocalDeRetirada = $repoNew->find(1);
        $oldStatelocalDeRetirada = $repoOld->find(1);        Assertion::true(($newStatelocalDeRetirada >= $oldStatelocalDeRetirada),"Coleta Esta mais rapida que geração" );
    });

    $scenery->action('Coletar Lixo', function($state){
        $umDiaQualquer = new ServicoDeLixo($state['new']);    
        $umDiaQualquer->coletarLixo();
    }, function($state){
        $repositoryNew = new LixoRepository($state['new']);
        $repositoryOld = new LixoRepository($state['old']);
        $newStateLixao = $repositoryNew->find(1);
        $oldStateLixao = $repositoryOld->find(1);
        Assertion::true(($oldStateLixao['localDeRetirada'] >= $newStateLixao['localDeRetirada']),"Recolhendo Pouco");
    });

    $rodarAte = new \Datetime();
    $rodarAte->add(new \DateInterval("P0YT0M6S"));
    $runnerStrategy = Factory::get(Strategy::RUN_UNTILDATE,$scenery);
    $result = $runnerStrategy->run(['until'=>$rodarAte,'by'=>1,'loud'=>true]);
    $info = ["localDeRetirada"=>0,"Lixao"=>0];
    foreach ($result as $r){
/*
        echo "No Local de Retirada tem {$info['localDeRetirada']} Sacos de Lixo e no Lixao {$info['Lixao']}.\n";
        $info = $r['data']->get(['sistemaDeLixo'=>[1]])['sistemaDeLixo'][1];
//        var_dump ($info);
*/
    }
    ?>