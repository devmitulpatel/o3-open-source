<?php

namespace Laravel\Nova\Tests\Fixtures;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    /**
     * Get the author of the comment.
     */
    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /**
     * The sub categories for this category.
     *
     * @return HasMany
     */
    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }
}
