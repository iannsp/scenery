# Iannsp\Scenery#

* A gente devia ter duas vidas, uma pra ensaiar e outra para viver.

* we should have two lives, one to assay and another to live.

([Vittorio Gassman](http://pt.wikipedia.org/wiki/Vittorio_Gassman))

#### O que é? ####

**Scenery é uma ferramenta de criação de cenários para testes de integração e validação de expectativas nas camadas da aplicação/domínio e infra estrutura que permite execuções ciclicas(repetições do conjunto de ações) para simulações de comportamento real de uso.**

Em um dominio existem ações a serem tomadas e os reflexos esperados dessas ações.

O reflexo das ações são esperados como mudanças no estado da aplicação que 
podem ser verificados atraves do estado dos dados e o estado do dominio. 
Essas verificações são a expectativa de mudança do arquiteto/desenvolvedor no 
momento em que ele modela o dominio.


### O que é um cenário (*Scenery*)? ###

Um cenário é uma *configuração de ambiente para execução* e teste de *expectativas* de um conjunto de *Ações*. 

As caracteristicas de um cenário são:

* Conjunto inicial de dados.
* Conjunto de ações.
* Modelo de execução. 

####Criando um cenário####

```php
   $initialDataState = new [Data](https://github.com/iannsp/scenery/blob/master/src/Iannsp/Scenery/Data.php)();
   $scenery = new [Scenery]((https://github.com/iannsp/scenery/blob/master/src/Iannsp/Scenery/Scenery.php))($initialDataState); 
```


### O que é uma Ação(*Action*)? ###

Action é um comportamento existente num Domínio cujos efeitos podem ser testados através das expectativas.

características de uma Action:

* uma Action tem um estado composto por
 * estado dos Dados
   * New: Os dados no estado atual do cenário.
   * Old: Os dados no estado anterior a execução da Action.
 * transienteData: Dados temporários disponíveis durante a execução de uma ação e suas expectativas:
   * Cycle: O número identificador do ciclo de execução em que esta o cenário.
   * udd: user defined data. Dados transientes definidos pela action necessários para sua execução e/ou teste de expectativas. 
 
####Criando uma ação####

```php
    $action = function($state){
            // codigo de execução do comportamento a ser testado no domínio ou aplicação. 
        }, function($state){
            // código de teste de expectativa utilizando código do domínio.
        }, function ($state){
            // código de teste de expectativa utilizando recursos de código de infra estrutura.
        }
    );
    // adicionando a action em um cenário.
    $scenery->action([Nome da Action], $action);
```

### O que é uma Expectativa(*Expectation*)? ###

Expectativa é uma verificação dos efeitos relacionados a execução de uma ação. 

Caracteristicas de uma expectativa:
* São dois tipos de expectativa.
 * layer de Domínio (expectedDomain): utiliza código de domínio para validar a Action.
 * layer de infra estrutura (expectedInfraStructure): utiliza recursos de infra estrutura para validar a Action.
* cada tipo de expectativa tem seu conjunto de assertions. 
 

# Estado dos Dados #

O estado do dados é representado por uma estrutura de array representando o modelo dos dados utilizados.

Se um sistema utiliza uma tabela modela da como 

```php
Person
(
id serial primary key,
name varchar(100),
email varchar(100)
)
``` 

O estado de dados após alguem criar uma Person de nome 'Ivo' e email 'iannsp@gmail.com' será.

```php
$data = [
    'person'=>[
        ['id'=>1, 'name'=>'Ivo', 'email'=>'iannsp@gmail.com']
    ]
];
```
    com Scenery, a descrição do teste de criação de Person pode ser feita com
    
```php

require 'bootstrap.php';
    $scenery = new Scenery($data);
    um caso de teste é escrito com
    test(
        String $ActionNome, 
        function $Action, // acoes do dominio
        function $ExpectedInDomain=null, //testes usando modelagem de dominio
        function $ExpectedInData=null, // testar os reflexos nos dados
        );

    Scenery::action('Criar Pessoa', function()use($scenery){
        // para usar Scenery a strutura de persistencia da aplicação deve poder aceitar um driver de Array
            $person = new Person\Service\Person($diFromBootstrapLikeLiveApp);
            $fakePerson = Sceneray::makeFake('person');
            $personEntity= $person->create($fakePerson['name'],$fakePerson['email']);
            $scenery->addData('person', $personEntity);
        },function()use($scenery){

            $expectedPerson = $scenery->getData('person');
            $person = new Person\Service\Person($diFromBootstrapLikeLiveApp);
            $exist = $person->find($expectedPerson->get('id'));
            Scenery::assertEquals($expectedPerson,$exist);

        },function()use($scenery){

            $expectedPerson = $scenery->getData('person');
            $antesTinha = $scenery->data()->before('person')->count();
            Scenery::assertEquals($antesTinha+1, $person->findAll()->count());
            $testeRepository = $scenery->data('person')->has(
                [
                'id'=>$expectedPerson->get('id'),
                'name'=>'Ivo',
                'email'=>'iannsp@gmail.com'
                ]
                );
        });
```

    a Action ActionNome pode ser repetida multiplas vezes e sua expectativa testada com os dados em movimento

```php
    run([nome das acoes a serem executadas], function a ser usada no loop)
    $results = $scenery::run(['Criar Pessoa'],function()use($scenery)
    {
       if (!$scenery::exist('loop_id')) 
           $scenery::loop_id =0;
           if ($loop_id > 100)
           return false;
          return (bool) ++$scenery::loop_id;
    });
    
    // o cenario acima sera executado 100 vezes e vai parar.

```
    No final Scenery::run devolve a estrutura de dados afetada pela acoes.
    devolve tambem um relatorio de estado para cada loop de execucao das acoes de estado final.

    Os asserts devem ser simples.
    
    Pode ser criado um loop infinito para manter o softare em teste constante, e alertas
    para adicionar um novo teste basta abrir um terminal digitar scenery-snapshot e pegar esse snapshp e testar com a nova acao.
    para adicionar a nova acao de cenario eh so scenery-add-action actionFile.php que a action sera adicionada no llop.
    
    O software de teste rodaria para sempre, mesmo com os erros(se o programador quisesse)
    

    Scenery é uma proposta
    
    Scenery utiliza Asserts
    assert_options(ASSERT_ACTIVE, 1);
    assert_options(ASSERT_WARNING, 0);
    assert_options(ASSERT_QUIET_EVAL, 1);
    function my_assert_handler($file, $line, $code)
    {
        echo "<hr>Afirmação falhou:
            Arquivo '$file'<br />
            Linha '$line'<br />
            Código '$code'<br /><hr />";
    }
    // Define a função
    assert_options(ASSERT_CALLBACK, 'my_assert_handler');
    
    