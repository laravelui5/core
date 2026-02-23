<?php

namespace LaravelUi5\Core\Contracts;

use Illuminate\Http\Request;
use LaravelUi5\Core\Ui5\Contracts\Ui5ArtifactInterface;

interface Ui5ContextFactoryInterface
{
    public function build(Request $request, Ui5ArtifactInterface $artifact): Ui5ContextInterface;
}
