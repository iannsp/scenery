<?php
namespace Iannsp\Scenery\Context;

class DomainContextTest extends \PHPUnit_Framework_TestCase
{
    
    public function setUp()
    {
        require_once __DIR__."/../../Samples/DomainLixo/tests/Cenarios/Context/DomainContext.php";
    }
/**
    @test
*/    
    public function useLazyResource()
    {
        $dc = new DomainContext();
        $test = $dc->test->set( "Ivo");
    }

/**
    @test
*/    
    public function useResource()
    {
        $dc = new DomainContext();
        $test = $dc->test2->set( "Ivo");
    }

}