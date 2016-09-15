<?php
require_once('simpletest/unit_tester.php');
require_once('simpletest/reporter.php');

$group = new TestSuite();
$group->addFile('price_test.php');

$group->run(new HtmlReporter());
?>