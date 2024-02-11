<?php

namespace App\Services;

use App\Models\Category;

class CategoryService
{
    /**
     * Get Category by name or create it if not exists.
     *
     * @param string $name
     * @return Category
     */
    public function firstOrCreateByName(string $name): Category
    {
        return Category::firstOrCreate(['name' => $name]);
    }
}