<?php

namespace App\Http\Controllers\Api;

use App\Models\Category;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Http\Requests\StoreCategoryRequest;

class CategoryController extends Controller
{
    //
    public function index()
    {
        return CategoryResource::collection(Category::all());
    }

    public function show(Category $category)
    {
        abort_if(! auth()->user()->tokenCan('categories-show'), 403);
        
        return new CategoryResource($category);
    }

    public function list()
    {
        abort_if(! auth()->user()->tokenCan('categories-list'), 403);
        return CategoryResource::collection(Category::all());
    }

    public function store(StoreCategoryRequest $request)
    {
        $data = $request->all();
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $name = 'category/' . Str::uuid() . '.' . $file->extension();
            $file->storePubliclyAs('public', $name);
            $data['photo'] = $name;
        }
        $category = Category::create($data);

        return new CategoryResource($category);
    }

    public function update(Category $category, StoreCategoryRequest $request)
    {
        $category->update($request->all());

        return new CategoryResource($category);
    }

    public function destroy(Category $category)
    {
        $category->delete();

        return response()->noContent();
    }
}
