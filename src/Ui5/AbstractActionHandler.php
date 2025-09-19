<?php

namespace LaravelUi5\Core\Ui5;

use LaravelUi5\Core\Contracts\Ui5Args;
use LaravelUi5\Core\Ui5\Contracts\ActionHandlerInterface;
use LaravelUi5\Core\Ui5\Contracts\ParameterizableInterface;

abstract class AbstractActionHandler implements ActionHandlerInterface, ParameterizableInterface
{
    protected Ui5Args $args;

     public function withArgs(Ui5Args $args): static
     {
         $this->args = $args;
         return $this;
     }

     public function args(): Ui5Args
     {
         return $this->args;
     }
 }
