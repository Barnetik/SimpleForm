<?php
namespace SimpleForm\Test;



use SimpleForm\Test\Mock\MockExecutionContext;
use SimpleForm\Test\Mock\SomeTestField;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Validator\ConstraintValidatorFactory;
use Symfony\Component\Validator\Context\ExecutionContext;
use Symfony\Component\Validator\Context\ExecutionContextFactory;
use Symfony\Component\Validator\Mapping\Factory\LazyLoadingMetadataFactory;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\RecursiveValidator;
use Zend\Validator\NotEmpty;

class TestField  extends \PHPUnit_Framework_TestCase {





    function testCreateDefault() {


        $field = new SomeTestField("test_field", "test_form");

        $attributes = $field->getAttributesArray();
        $validators = $field->getValidators();

        $this->assertTrue(isset($attributes["required"]) && $attributes["required"]=="required");
        $this->assertTrue(false !== array_search(new NotEmpty(), $validators));


    }


    function testCreateOptional() {


        $field = new SomeTestField("test_field", "test_form", array("required"=>false));

        $attributes = $field->getAttributesArray();
        $validators = $field->getValidators();

        $this->assertFalse(isset($attributes["required"]));
        $this->assertCount(0, $validators);


    }


    function testValue() {



        $field = new SomeTestField("test_field", "test_form", array("required"=>false));
        $field->setValue("whatever");


        $this->assertEquals("whatever", $field->getValue());

        $field->bind("test");


        $this->assertEquals("test", $field->getValue());

    }


    function testRequired() {



        $field = new SomeTestField("test_field", "test_form", array("required"=>false));
        $field->bind("");


        $this->assertCount(0, $field->getErrors());

        $field = new SomeTestField("test_field", "test_form");
        $field->bind("");


        $this->assertCount(1, $field->getErrors());

    }

    function testGetLabelTag() {


        $field = new SomeTestField("test_field", "test_form", array("required"=>false));

        $this->assertEquals('<label for="test_form_test_field">test_field</label>', $field->getLabelTag());
        $this->assertEquals('<label for="test_form_test_field">Test</label>', $field->getLabelTag("Test"));

    }

    function testGetInputTag() {


        $field = new SomeTestField("test_field", "test_form", array("required"=>false));

        $field->setValue("test");
        $this->assertEquals('<input type="text" id="test_form_test_field"  name="test_form[test_field]" value="test">', $field->getInputTag());

    }

    function testRenderField(){
        $field = new SomeTestField("test_field", "test_form", array("required"=>false));

        $field->setValue("test");

        $label = '<label for="test_form_test_field">test_field</label>';
        $input = '<input type="text" id="test_form_test_field"  name="test_form[test_field]" value="test">';
        $this->assertEquals($label.$input, (string) $field);
    }



}