<?php

namespace PhpParser;

use PhpParser\Builder;

/**
 * "namespace", "class", "interface" and "function" are reserved keywords, so the methods are defined as
 * _class(),  _interface() and _function() in the class and are made available as class(), interface() and
 * function() through __call() magic.
 *
 * @method Builder\Namespace_ namespace(string $name) Creates a namespace builder.
 * @method Buidler\Use_       use(string $name)       Creates a use statement.
 * @method Builder\Class_     class(string $name)     Creates a class builder.
 * @method Builder\Function_  function(string $name)  Creates a function builder
 * @method Builder\Interface_ interface(string $name) Creates an interface builder.
 */
class BuilderFactory
{
    /**
     * Creates a namespace builder.
     * 
     * @param string $name Fully qualified namespace
     *
     * @return Builder\Namespace_ The created namespace builder
     */
    protected function _namespace($name) {
        return new Builder\Namespace_($name);
    }

    /**
     * Creates a use statement builder.
     * 
     * @param string $name Fully qualified classname
     *
     * @return Builder\Use_ The created use statement builder
     */
    protected function _use($name) {
        return new Builder\Use_($name);
    }

    /**
     * Creates a class builder.
     *
     * @param string $name Name of the class
     *
     * @return Builder\Class_ The created class builder
     */
    protected function _class($name) {
        return new Builder\Class_($name);
    }

    /**
     * Creates a interface builder.
     *
     * @param string $name Name of the interface
     *
     * @return Builder\Interface_ The created interface builder
     */
    protected function _interface($name) {
        return new Builder\Interface_($name);
    }

    /**
     * Creates a method builder.
     *
     * @param string $name Name of the method
     *
     * @return Builder\Method The created method builder
     */
    public function method($name) {
        return new Builder\Method($name);
    }

    /**
     * Creates a parameter builder.
     *
     * @param string $name Name of the parameter
     *
     * @return Builder\Param The created parameter builder
     */
    public function param($name) {
        return new Builder\Param($name);
    }

    /**
     * Creates a property builder.
     *
     * @param string $name Name of the property
     *
     * @return Builder\Property The created property builder
     */
    public function property($name) {
        return new Builder\Property($name);
    }

    /**
     * Creates a function builder.
     *
     * @param string $name Name of the function
     *
     * @return Builder\Function_ The created function builder
     */
    protected function _function($name) {
        return new Builder\Function_($name);
    }

    public function __call($name, array $args) {
        if (method_exists($this, '_' . $name)) {
            return call_user_func_array(array($this, '_' . $name), $args);
        }

        throw new \LogicException(sprintf('Method "%s" does not exist', $name));
    }
}