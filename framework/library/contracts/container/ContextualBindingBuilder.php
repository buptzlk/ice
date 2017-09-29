<?php

interface Framework_Library_Contracts_Container_ContextualBindingBuilder {

    /**
     * 确定依赖的目标类
     *
     * @param  string  $abstract
     * @return $this
     */
    public function needs($abstract);

    /**
     * Define the implementation for the contextual binding.
     * 
     * @param  \Closure|string  $implementation
     * @return void
     */
    public function give($implementation);
}
