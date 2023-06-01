<?php

namespace App\Repositories;

use JasonGuru\LaravelMakeRepository\Repository\BaseRepository;
//use Your Model

/**
 * Class CategoryRepository.
 */
class CategoryRepository
{
    /**
     * @return string
     *  Return the model
     */
    public function save($request)
    {
        return $request->all();
    }
}
