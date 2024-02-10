<?php

namespace App\Services;

use App\Models\Category;

class CategoryService
{
    public function firstOrCreate(string $name): Category
    {
        return Category::firstOrCreate(['name' => $name]);
    }
}