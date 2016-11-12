<?php
require_once(dirname(__FILE__)."/simpletest/autorun.php");
require_once(dirname(__FILE__).'/../Price.class.php');
require_once(dirname(__FILE__).'/../_helpers.php');


class TestOfHelpers extends UnitTestCase {
    function testPhonePositive() {
        $this->assertTrue(IsPhone("+61 333-444-55 99"));
    }
    
    function testPhoneNegative() {
        $this->assertFalse(IsPhone("ABC +61 333-444-55 99"));
    }
    
    function testEmailPositive(){
        $this->assertTrue(IsEmail("a-artur@yandex.ru"));
    }
    
    function testEmailNegative(){
        $this->assertFalse(IsEmail("1233 _@yandex.ru"));
    }
    
}
?>