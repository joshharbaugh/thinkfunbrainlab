<?php
require_once('errormgr.php');
$errText = "";
$errDisplay = "";
$errMgr  = new ErrorMgr();
$errMgr->getErrorText(201, $errText);
print "<p>";
print $errText;
$errMgr->getDisplayText(202, $errDisplay);
print "<p>";
print $errDisplay;
?>