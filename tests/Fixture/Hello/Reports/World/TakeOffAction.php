<?php

namespace Tests\Fixture\Hello\Reports\World;

use LaravelUi5\Core\Ui5\Contracts\ReportActionInterface;

class TakeOffAction implements ReportActionInterface
{
    protected array $context;

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
}
