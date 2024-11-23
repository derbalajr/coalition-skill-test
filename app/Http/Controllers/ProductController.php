<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    private $fileName = 'products.json';
    public function index()
    {
        return view('products.index', ['products' => []]);
    }

    public function store(Request $request)
    {
        $data = $this->loadData();

        $newEntry = [
            'product_name' => $request->product_name,
            'quantity' => (int)$request->quantity,
            'price' => (float)$request->price,
            'datetime' => now()->toDateTimeString(),
            'total_value' => $request->quantity * $request->price
        ];

        $data[] = $newEntry;
        $this->saveData($data);

        return response()->json(['success' => true, 'data' => $data]);
    }

    private function loadData()
    {
        if (!Storage::exists($this->fileName)) {
            return [];
        }

        $content = Storage::get($this->fileName);
        return json_decode($content, true) ?? [];
    }

    private function saveData($data)
    {
        Storage::put($this->fileName, json_encode($data, JSON_PRETTY_PRINT));
    }
}
