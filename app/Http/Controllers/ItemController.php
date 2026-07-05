<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Category;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin|pengadaan');
    }

    public function index()
    {
        $items = Item::with('category')->latest()->paginate(10);
        return view('items.index', compact('items'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('items.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'nama_barang' => 'required|string|max:255',
            'spesifikasi' => 'nullable|string',
            'satuan' => 'required|string|max:50',
            'harga_estimasi_default' => 'nullable|numeric|min:0'
        ]);

        Item::create($request->all());

        return redirect()->route('items.index')
            ->with('success', 'Barang berhasil ditambahkan');
    }

    public function edit(Item $item)
    {
        $categories = Category::all();
        return view('items.edit', compact('item', 'categories'));
    }

    public function update(Request $request, Item $item)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'nama_barang' => 'required|string|max:255',
            'spesifikasi' => 'nullable|string',
            'satuan' => 'required|string|max:50',
            'harga_estimasi_default' => 'nullable|numeric|min:0'
        ]);

        $item->update($request->all());

        return redirect()->route('items.index')
            ->with('success', 'Barang berhasil diperbarui');
    }

    public function destroy(Item $item)
    {
        $item->delete();

        return redirect()->route('items.index')
            ->with('success', 'Barang berhasil dihapus');
    }
}