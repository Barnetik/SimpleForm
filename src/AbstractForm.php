<?php
/**
 * Created by Asier Marqués <asiermarques@gmail.com>
 */

namespace SimpleForm;


use SimpleForm\Exception\FieldNotConfiguredException;
use SimpleForm\Field\AbstractField;

abstract class AbstractForm implements \Iterator, \ArrayAccess {


    protected $_fields     = array();

    protected $_has_errors = false;

    protected $_name;

    protected $_data;

    /**
     * @var FormBuilder
     */
    protected $_builder;


    /**
     * @param array $data
     * @param FormBuilder $builder
     */
    function __construct($data, FormBuilder $builder){

        $this->_data      = is_object($data) ?  get_object_vars($data) : $data;
        $this->_builder   = $builder;
        $this->_builder->setForm($this);

        $this->configure($builder);

    }

    abstract function configure(FormBuilder $builder);


    function setName($name){
        $this->_name = $name;
    }

    function getName(){
        return $this->_name;
    }

    /**
     * @return FormBuilder
     */
    function getBuilder(){
        return $this->_builder;
    }


    function addField(AbstractField $field){

        if(isset($this->_data[$field->getName()])){
            $field->setValue($this->_data[$field->getName()]);
        }

        $this->_fields[$field->getName()] = $field;

    }

    function getValue($key){
        return $this->offsetGet($key)->getValue();
    }

    function getValues(){
        $values = array();
        foreach($this->_fields as $key=>$field){
            $values[$key] = $field->getValue();
        }
        return $values;
    }

    function bind(array $array){


        $this->_has_errors = false;

        /**
         * @var $field AbstractField
         */
        foreach($this->_fields as $field){

            $field->reset();
            $value = isset($array[$field->getName()]) ? $array[$field->getName()] : null;

            if(!$field->bind($value)){
                $this->_has_errors = true;
            }

        }

    }

    function isValid(){
        return $this->_has_errors == false;
    }


    function rewind() {
        reset($this->_fields);
    }

    function current() {
        return current($this->_fields);
    }

    function key() {
        return key($this->_fields);
    }

    function next() {
        next($this->_fields);
    }

    function valid() {
        return key($this->_fields) !== null;
    }

    function getErrors(){

        $errors = array();
        if($this->_has_errors){
            foreach($this->_fields as $field){

                /**
                 * @var $field AbstractField
                 */
                $errors[$field->getName()] = $field->getErrors();
            }
        }
        return $errors;

    }


   function offsetExists (  $offset ){
        return isset($this->_fields[$offset]);
   }


    /**
     * @param mixed $offset
     * @return AbstractField
     * @throws Exception\FieldNotConfiguredException
     */
    function offsetGet (  $offset ){

        if($this->offsetExists($offset)) {

            return $this->_fields[$offset];

        }else{

            throw new FieldNotConfiguredException();

        }

   }


   function offsetSet (  $offset ,  $value ){
        //todo: exception
   }


   function offsetUnset (  $offset ){
       $this->_fields[$offset] = null;
       unset($this->_fields[$offset]);
   }

} 