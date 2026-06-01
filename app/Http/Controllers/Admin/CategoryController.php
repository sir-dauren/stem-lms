<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\HandlesTranslations;
use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    use HandlesTranslations;

    public function index()
    {
        $roots = Category::whereNull('parent_id')
            ->with('descendants')->withCount('courses')
            ->orderBy('sort_order')->get();

        $flat = Category::orderBy('parent_id')->orderBy('sort_order')->get();

        return view('admin.categories.index', compact('roots', 'flat'));
    }

    public function store(Request $request)
    {
        $request->validate(
            $this->translationRules(['name'], ['description']) + [
                'parent_id' => ['nullable', 'exists:categories,id'],
                'icon'      => ['nullable', 'string', 'max:8'],
                'color'     => ['nullable', 'string', 'max:9'],
            ],
            $this->translationMessages(['name'])
        );

        Category::create([
            'name'        => $this->translations($request, 'name'),
            'description' => $this->translations($request, 'description'),
            'parent_id'   => $request->input('parent_id'),
            'icon'        => $request->input('icon'),
            'color'       => $request->input('color'),
            'sort_order'  => (int) Category::where('parent_id', $request->input('parent_id'))->max('sort_order') + 1,
        ]);

        return back()->with('status', 'Категория создана.');
    }

    public function update(Request $request, Category $category)
    {
        $request->validate(
            $this->translationRules(['name'], ['description']) + [
                'parent_id' => ['nullable', 'exists:categories,id'],
                'icon'      => ['nullable', 'string', 'max:8'],
                'color'     => ['nullable', 'string', 'max:9'],
                'is_active' => ['nullable', 'boolean'],
            ],
            $this->translationMessages(['name'])
        );

        if ((int) $request->input('parent_id') === $category->id) {
            return back()->withErrors(['parent_id' => 'Категория не может быть родителем самой себя.']);
        }

        $category->update([
            'name'        => $this->translations($request, 'name'),
            'description' => $this->translations($request, 'description'),
            'parent_id'   => $request->input('parent_id'),
            'icon'        => $request->input('icon'),
            'color'       => $request->input('color'),
            'is_active'   => $request->boolean('is_active'),
        ]);

        return back()->with('status', 'Категория обновлена.');
    }

    public function reorder(Request $request)
    {
        foreach ($request->input('order', []) as $position => $id) {
            Category::where('id', $id)->update(['sort_order' => $position]);
        }
        return response()->json(['ok' => true]);
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return back()->with('status', 'Категория удалена (подкатегории откреплены).');
    }
}
