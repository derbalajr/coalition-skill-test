<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    private $fileName = 'products.json';

    public function index()
    {
        $data = $this->loadData();
        return view('products.index', ['products' => $data]);
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

    public function show($index)
    {
        $data = $this->loadData();

        if (!isset($data[$index])) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        return response()->json(['item' => $data[$index]]);
    }

    public function update(Request $request, $index)
    {
        $data = $this->loadData();

        if (!isset($data[$index])) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        $data[$index] = [
            'product_name' => $request->product_name,
            'quantity' => (int)$request->quantity,
            'price' => (float)$request->price,
            'datetime' => $data[$index]['datetime'],
            'total_value' => $request->quantity * $request->price
        ];

        $this->saveData($data);
        return response()->json(['success' => true, 'data' => $data]);
    }

    public function destroy($index)
    {
        $data = $this->loadData();

        if (!isset($data[$index])) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        unset($data[$index]);
        $data = array_values($data);
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
