<?php

namespace App\Repositories;

use App\Models\Category;
use App\RepositoryInterface\CategoryInterface;

class CategoryRepository implements CategoryInterface
{
    public function getAllCategories()
    {
        $categories = Category::withCount('products')->get();
        return $categories;
    }

    public function getCategoryById($id)
    {
        return Category::findOrFail($id);
    }


    public function createCategory(array $data)
    {
        if (isset($data['image']) && $data['image'] instanceof \Illuminate\Http\UploadedFile) {
            $file = $data['image'];
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('categories'), $filename);
            $data['image'] = 'categories/' . $filename;
        }

        return Category::create($data);
    }

    public function updateCategory($id, array $data)
    {
        $category = Category::findOrFail($id);

        if (isset($data['image']) && $data['image'] instanceof \Illuminate\Http\UploadedFile) {
        
            if ($category->image && file_exists(public_path($category->image))) {
                @unlink(public_path($category->image));
            }

            $file = $data['image'];
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('categories'), $filename);
            $data['image'] = 'categories/' . $filename;
        }

        $category->update($data);
        return $category;
    }
    public function deleteCategory($id)
    {
        $category = Category::findOrFail($id);
        if ($category->image && file_exists(public_path($category->image))) {
            @unlink(public_path($category->image));
        }
        $category->delete();
        return true;
    }
}
