<?php


namespace App\Template;


class MyPagerfantaTemplate extends \Pagerfanta\View\Template\TwitterBootstrap3Template
{
    /*
    static protected $defaultOptions = array(
        'prev_message'        => '&larr;',
        'next_message'        => '&rarr;',
        'dots_message'        => '&hellip;',
        'active_suffix'       => '',
        'css_container_class' => 'pagination',
        'css_prev_class'      => 'prev',
        'css_next_class'      => 'next',
        'css_disabled_class'  => 'disabled',
        'css_dots_class'      => 'disabled',
        'css_active_class'    => 'active',
        'rel_previous'        => 'prev',
        'rel_next'            => 'next'
    );
    */
    public function __construct()
    {
        parent::__construct();
        $options = [
            'prev_message' => '&larr;',
            'next_message' => '&rarr;',
            'css_container_class' => 'pagination with_border rounded'
        ];
        $this->setOptions($options);
    }
}