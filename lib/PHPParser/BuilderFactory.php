<?php

/**
 * "class" and "function" are reserved keywords, so the methods are defined as _class()
 * and _function() in the class and are made available as class() and function() through
 * __call() magic.
 *
 * @method PHPParser_Builder_Class    class(string $name)    Creates a class builder.
 * @method PHPParser_Builder_Function function(string $name) Creates a function builder
 */
class PHPParser_BuilderFactory
{
    /**
     * Creates a class builder.
     *
     * @param string $name Name of the class
     *
     * @return PHPParser_Builder_Class The created class builder
     */
    protected function _class($name) {
        return new PHPParser_Builder_Class($name);
    }

    /**
     * Creates a method builder.
     *
     * @param string $name Name of the method
     *
     * @return PHPParser_Builder_Method The created method builder
     */
    public function method($name) {
        return new PHPParser_Builder_Method($name);
    }

    /**
     * Creates a parameter builder.
     *
     * @param string $name Name of the parameter
     *
     * @return PHPParser_Builder_Param The created parameter builder
     */
    public function param($name) {
        return new PHPParser_Builder_Param($name);
    }

    /**
     * Creates a property builder.
     *
     * @param string $name Name of the property
     *
     * @return PHPParser_Builder_Property The created property builder
     */
    public function property($name) {
        return new PHPParser_Builder_Property($name);
    }

    /**
     * Creates a function builder.
     *
     * @param string $name Name of the function
     *
     * @return PHPParser_Builder_Property The created function builder
     */
    protected function _function($name) {
        return new PHPParser_Builder_Function($name);
    }

    public function __call($name, array $args) {
        if ('class' === $name) {
            return call_user_func_array(array($this, '_class'), $args);
        } elseif ('function' === $name) {
            return call_user_func_array(array($this, '_function'), $args);
        }

        throw new LogicException(sprintf('Method "%s" does not exist', $name));
    }
}