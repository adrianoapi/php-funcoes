<?php

require_once 'a.php';
require_once 'b.php';

use Application\Form as Form;
use Application\Field as Field;

var_dump(new Form);
var_dump(new Field);

use Components\Form;

var_dump();