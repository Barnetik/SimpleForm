<?php
/**
 * Created by Asier Marqués <asiermarques@gmail.com>
 */

namespace SimpleForm\Field;

use Zend\Validator\AbstractValidator;
use Zend\Validator\NotEmpty;


abstract class AbstractField {


    protected $_errors     = array();
    protected $_options    = array();
    protected $_validators = array();


    protected $_html_attributes = array();

    protected $_form_name;

    protected $_name;
    protected $_value;

    function __construct($name, $form_name, $options=array(), $validators=array()){

        $this->_name       = $name;
        $this->_form_name  = $form_name;
        $this->_options    = $options;
        $this->_validators = $validators;

        $this->_checkOptionsRequisites();
        $this->_configureHTMLAttributes();
    }


    function reset(){
        $this->setValue(null);
    }

    function getOptions(){
        return $this->_options;
    }

    function getValidators(){
        return $this->_validators;
    }

    function getErrors(){
        return $this->_errors;
    }


    /**
     * Override this method in order to check the required options
     * @return bool
     */
    protected function _checkOptionsRequisites(){
        return true;
    }


    protected function _configureHTMLAttributes(){
        unset($this->_options["validators"]);

        if(isset($this->_options["required"])){

            if($this->_options["required"]){
                $this->_html_attributes["required"] = "required";
                $this->_validators[] = new NotEmpty();
            }

            unset($this->_options["required"]);

        }else{

            $this->_html_attributes["required"] = "required";
            $this->_validators[] = new NotEmpty();
        }

        $this->_html_attributes["id"]   = $this->_form_name . "_" . $this->_name;
        $this->_html_attributes["name"] = $this->_form_name . "[" . $this->_name . "]";

        foreach($this->_options as $key=>$option){
            $this->_html_attributes[$key] = $option;
        }

    }

    function getName(){
        return $this->_name;
    }

    function getAttributesArray(){
        return $this->_html_attributes;
    }

    function getAttributes(){
        $html = " ";
        foreach($this->_html_attributes as $key=>$value){
            $html .= ' ' .  $key . '="' . $value . '"';
        }


        return $html;
    }

    function getValue(){
        return $this->_value;
    }

    function setValue($value){
        $this->_value = $value;
    }


    function bind($value){

        $this->reset();

        $this->setValue($value);

        $this->_errors = array();

        /**
         * @var $validator AbstractValidator
         */
        foreach($this->_validators as $validator){

            if(is_array($value)){

                foreach($value as $_value){

                    if(!$validator->isValid($_value)){

                        foreach($validator->getMessages() as $i=>$message){
                            $this->_errors[] = $message;
                        }

                    }

                }

            }else{

                if(!$validator->isValid($value)){

                    foreach($validator->getMessages() as $i=>$message){
                        $this->_errors[] = $message;
                    }

                }

            }

        }

        if(count($this->_errors)){

            if(!isset($this->_html_attributes["class"]))
                $this->_html_attributes["class"] = "";

            $this->_html_attributes["class"] .= " error";
        }

        return !count($this->_errors);

    }


    function __toString(){
        return $this->getLabelTag() . $this->getInputTag() . $this->getErrorTag();
    }

    function getLabelTag($label=null){
        $label = isset($this->_options["label"]) ? $this->_options["label"] : ($label ? $label :$this->_name);
        return '<label for="' . $this->_form_name . '_'. $this->_name .'">' . $label . '</label>';
    }

    function hasError(){
        return count($this->_errors);
    }

    function getErrorArray(){
        return $this->_errors;
    }

    function getErrorTag(){

        if($this->hasError()){

            $html = "";
            foreach($this->getErrorArray() as $key=>$error){
                $html .= '<span class="error">' . $error . '</span>';
            }
            return $html;
        }

        return null;
    }

    abstract function  getInputTag();

} 