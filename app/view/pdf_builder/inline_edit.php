<?php
// capture original output
ob_start();
include F::appPath('view/scaffold/inline_edit.php');
$doc = Util::phpQuery(ob_get_clean());


// checkbox cursor & spacing
$doc->find('.form-check label')->addClass('cursor-pointer');
$doc->find('.col-bold,.col-italic,.col-underline')->find('.form-group')->removeClass('mb-1')->addClass('mb-n1');


// adjust field size
$doc->find('.col-align,.col-size,.col-color')->find('.input-group')->removeClass('input-group-sm')->addClass('input-group-xs');


// button column width
$doc->find('.col-button')->attr('width', 220);


// done!
echo $doc;