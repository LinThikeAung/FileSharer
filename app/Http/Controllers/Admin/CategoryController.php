<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\CategoryRepository;

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
