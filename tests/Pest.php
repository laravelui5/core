<?php

use Tests\TestCase;
use Tests\UnitTestCase;

pest()->extends(TestCase::class)->in('Feature');
pest()->extends(UnitTestCase::class)->in('Unit');
