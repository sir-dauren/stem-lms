<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\HandlesTranslations;
use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\MenuItem;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    use HandlesTranslations;
    public function index()
    {
        $menus = Menu::with('allItems')->get();

        // ensure default menus exist
        foreach (['header' => 'Главное меню', 'footer' => 'Меню в подвале'] as $loc => $name) {
            if (! $menus->firstWhere('location', $loc)) {
                Menu::create(['name' => $name, 'location' => $loc]);
            }
        }
        $menus = Menu::with('allItems')->get();

        return view('admin.menus.index', compact('menus'));
    }

    public function storeItem(Request $request, Menu $menu)
    {
        $request->validate(
            $this->translationRules(['label']) + [
                'url'       => ['required', 'string', 'max:255'],
                'parent_id' => ['nullable', 'exists:menu_items,id'],
                'target'    => ['nullable', 'in:_self,_blank'],
            ],
            $this->translationMessages(['label'])
        );

        $menu->allItems()->create([
            'label'      => $this->translations($request, 'label'),
            'url'        => $request->input('url'),
            'parent_id'  => $request->input('parent_id'),
            'target'     => $request->input('target', '_self'),
            'sort_order' => (int) $menu->allItems()->where('parent_id', $request->input('parent_id'))->max('sort_order') + 1,
        ]);

        return back()->with('status', 'Пункт меню добавлен.');
    }

    public function updateItem(Request $request, MenuItem $item)
    {
        $request->validate(
            $this->translationRules(['label']) + [
                'url'       => ['required', 'string', 'max:255'],
                'target'    => ['nullable', 'in:_self,_blank'],
                'is_active' => ['nullable', 'boolean'],
            ],
            $this->translationMessages(['label'])
        );

        $item->update([
            'label'     => $this->translations($request, 'label'),
            'url'       => $request->input('url'),
            'target'    => $request->input('target', '_self'),
            'is_active' => $request->boolean('is_active'),
        ]);

        return back()->with('status', 'Пункт меню обновлён.');
    }

    public function destroyItem(MenuItem $item)
    {
        $item->delete();
        return back()->with('status', 'Пункт меню удалён.');
    }
}
