<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index()
    {
        $menus = Menu::all();
        return view('superadmin.menus.index', compact('menus'));
    }

    public function create()
    {
        return view('superadmin.menus.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:menus,name',
        ]);

        Menu::create([
            'name' => $request->name,
            'group'=>$request->group
        ]);

        return redirect()->route('superadmin.menus.index')->with('success', 'Menu créé avec succès.');
    }

    public function edit(Menu $menu)
    {
        return view('superadmin.menus.edit', compact('menu'));
    }

    public function update(Request $request, Menu $menu)
    {
        $request->validate([
            'name' => 'required|string|unique:menus,name,' . $menu->id,
        ]);

        $menu->update([
            'name' => $request->name,
            'group'=>$request->group
        ]);

        return redirect()->route('superadmin.menus.index')->with('success', 'Menu mis à jour.');
    }

    public function destroy(Menu $menu)
    {
        $menu->delete();
        return redirect()->route('superadmin.menus.index')->with('success', 'Menu supprimé.');
    }
}
