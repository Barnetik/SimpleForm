<?php
/**
 * Created by Asier Marqués <asiermarques@gmail.com>
 * Date: 10/5/16
 * Time: 14:29
 */

namespace SimpleForm\Test\Mock;


use SimpleForm\AbstractForm;
use SimpleForm\FormBuilder;

class TestChoiceForm extends AbstractForm{


    function configure(FormBuilder $builder){

        $types     = array("image"=> "Imagen", "audio"=>"Audio");

        $this->setName("content_info");


        $builder->add("type", "choice", array("choices"=>$types, "label"=>"Tipo de contenido", "class"=>"form-control"));


    }
}

