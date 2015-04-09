<?php
namespace Iannsp\Scenery\Context;

interface Context
{
    public function __construct();
    public function __get($resourceName);
    public function message($message);
}