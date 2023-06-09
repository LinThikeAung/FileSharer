<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\CategoryRepository;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    protected $category;
    public function __construct(CategoryRepository $category)
    {
        $this->category = $category;
    }

    public function save(Request $request)
    {
      return $this->category->save($request);
    }
}
