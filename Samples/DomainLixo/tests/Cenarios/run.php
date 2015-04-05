<?php
require __DIR__.'/../../Modelo.php';
require __DIR__.'/../../../../bootstrap.php';

// Um dia qualquer numa cidade qualquer

use \Lixo\Lixo;
use \Lixo\Lixeiro;
use \Lixo\Morador;
use \Lixo\LocalDeRetirada;
use \Lixo\ServicoDeLixo;

use Iannsp\Scenery\Scenery;
use Iannsp\Scenery\Data;
use Iannsp\Scenery\RunStrategy\Strategy;
use Iannsp\Scenery\RunStrategy\Factory;

use Assert\Assertion;
$dados = new Data([
    'sistemaDeLixo'=>
        [
            ['key'=>1,['localDeRetirada'=>0, 'Lixao'=>0]]
        ]
    ]
    );

    $scenery = New Scenery($dados);
    $scenery->action('Produzir Lixo', function($state){
        $umDiaQualquer = new ServicoDeLixo($state['new']);    
        $umDiaQualquer->produzirLixo();
    }, function($state){
        $newStatelocalDeRetirada = $state['new']->get()['sistemaDeLixo'][1]['localDeRetirada'];
        $oldStatelocalDeRetirada = $state['old']->get()['sistemaDeLixo'][1]['localDeRetirada'];
        Assertion::true(($newStatelocalDeRetirada >= $oldStatelocalDeRetirada),"Coleta Esta mais rapida que geração" );
    });

    $scenery->action('Coletar Lixo', function($state){
        $umDiaQualquer = new ServicoDeLixo($state['new']);    
        $umDiaQualquer->coletarLixo();
    }, function($state){
        $newStateLixao = $state['new']->get()['sistemaDeLixo'][1]['Lixao'];
        $oldStateLixao = $state['old']->get()['sistemaDeLixo'][1]['Lixao'];
        Assertion::true(($newStateLixao >= $oldStateLixao),"Recolhendo Pouco Lixo" );
    });

    $rodarAte = new \Datetime();
    $rodarAte->add(new \DateInterval("P0YT3M0S"));
    $runnerStrategy = Factory::get(Strategy::RUN_UNTILDATE,$scenery);
    $result = $runnerStrategy->run(['until'=>$rodarAte,'by'=>1]);
    $info = ["localDeRetirada"=>0,"Lixao"=>0];
    foreach ($result as $r){
        echo "No Local de Retirada tem {$info['localDeRetirada']} Sacos de Lixo e no Lixao {$info['Lixao']}.\n";
        $info = $r['data']->get(['sistemaDeLixo'=>[1]])['sistemaDeLixo'][1];
//        var_dump ($info);
    }
    ?>