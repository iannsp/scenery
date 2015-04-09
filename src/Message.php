<?php
namespace Iannsp\Scenery;

class Message
{
    const GLOBALCONTEXT = 'GLOBAL';
    private static $contexts = [];
    private $contextName;
    private $messages = []; 
    private function __construct($contextName)
    {
        $this->contextName = $contextName;
    }
    public static function getContext($contextName)
    {
        $contextMessenger =  new Message($contextName);
        self::$contexts[$contextName] = $contextMessenger;
        return $contextMessenger;
    }
    
    public function getContextName()
    {
        return $this->contextName;
    }
    public function send($message)
    {
        $now = new \DateTime();
        $this->messages[] = [
            'timestamp'=>$now->getTimestamp(), 
            'message'=>$message
            ];
    }
    public function read($contextName=null)
    {
        if (is_null($contextName))
            return $this->messages;
        if ($contextName != Self::GLOBALCONTEXT)
            return [];
        $messages = [];
        foreach(self::$contexts as $context){
            $contextName = $context->getContextName();
            $messages[$contextName]= $context->read();
        }
        return $messages;
    }
    public static function restart()
    {
        self::$contexts = [];
    }
}