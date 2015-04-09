<?php
namespace Iannsp\Scenery;

class MessageTest extends \PHPUnit_Framework_TestCase
{
    
/**
   @test 
*/
    public function getContext()
    {
        $msg = Message::getContext('ACTION');
        $this->assertInstanceOf('\Iannsp\Scenery\Message', $msg);
        $this->assertEquals('ACTION', $msg->getContextName());
    }

/**
   @test 
*/
    public function getSendedMessage()
    {
        Message::restart();
        $msg = Message::getContext('MYACTION');
        $this->assertInstanceOf('\Iannsp\Scenery\Message', $msg);
        $this->assertEquals('MYACTION', $msg->getContextName());
        $msg->send('Primeira Mensagem');
        $msg->send('Segunda Mensagem');
        $msgs =  $msg->read();
        $this->assertCount(2,$msgs);
        $msgs =  $msg->read(Message::GLOBALCONTEXT);
        $this->assertCount(1, $msgs);
        $this->assertArrayHasKey('MYACTION', $msgs);
        $this->assertCount(2, $msgs['MYACTION']);
        $msgs = Message::read('INEXISTENT_CONTEXT');
        $this->assertCount(0, $msgs);
    }
}