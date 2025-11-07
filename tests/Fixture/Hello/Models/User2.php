<?php

namespace Tests\Fixture\Hello\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use LaravelUi5\Core\Attributes\SemanticLink;

class User2 extends Model
{

    #[SemanticLink]
    public function user(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
