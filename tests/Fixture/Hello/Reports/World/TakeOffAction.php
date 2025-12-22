<?php

namespace Tests\Fixture\Hello\Reports\World;

use LaravelUi5\Core\Contracts\Ui5Args;
use LaravelUi5\Core\Ui5\Contracts\ReportActionInterface;

class TakeOffAction implements ReportActionInterface
{
    protected array $context;
    private Ui5Args $args;

    public function withContext(array $context): self
    {
        $this->context = $context;

        return $this;
    }

    public function label(): string
    {
        return 'Return the label for a button';
    }

    public function description(): string
    {
        return 'Return a one-line description of what the action does.';
    }

    public function execute(): array
    {
        return [
            'status' => 'Success',
            'message' => 'The action was executed successfully.'
        ];
    }

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
