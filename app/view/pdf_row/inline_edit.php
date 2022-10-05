<?php
// capture original output
ob_start();
include F::appPath('view/scaffold/inline_edit.php');
$doc = Util::phpQuery(ob_get_clean());

// checkbox cursor & spacing
$doc->find('.form-check label')->addClass('cursor-pointer');
$doc->find('div.col-bold,div.col-italic,div.col-underline')->find('.form-group')->removeClass('mb-1')->addClass('mb-n1');

// adjust field size
$doc->find('div.col-align,div.col-size,div.col-color,div.col-height,div.col-width')->find('.input-group')->removeClass('input-group-sm')->addClass('input-group-xs');

// button column width
$doc->find('td.col-button')->attr('width', 220);

// add border
$doc->find('td.col-value,td.col-value-url')->addClass('bx-1 by-0 b-dark px-5');
$doc->find('td.col-value,td.col-value-url')->prev()->addClass('pr-2');
$doc->find('td.col-value,td.col-value-url')->next()->addClass('pl-2');

// done!
echo $doc;