<?php

namespace Tests\Fixture\Hello\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use LaravelUi5\Core\Attributes\SemanticLink;

class Order2 extends Model
{

    #[SemanticLink(model: User::class)]
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
