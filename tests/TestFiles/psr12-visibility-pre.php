<?php

abstract class Test
{
    public abstract static function aus();
    static abstract protected  function aos();
    public abstract function au();
    abstract protected  function ao();

    public final static function fus() {}
    static final protected  function fos() {}
    static  private  function ps() {}
    public final function fu() {}

    function x() {}
    final protected function fo() {}
    private function p() {}
}
