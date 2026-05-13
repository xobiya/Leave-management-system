<?php

namespace App\Http\Controllers;

use App\Models\LunchProduct;
use App\Models\LunchCategory;
use App\Models\LunchOrder;
use App\Models\LunchOrderLine;
use Illuminate\Http\Request;

class LunchController extends Controller
{
    public function index()
    {
        $this->authorize('lunch.read');
        $products = LunchProduct::with('category')->where('is_active', true)->latest()->get();
        $orders = LunchOrder::with(['employee.user'])->latest()->take(10)->get();
        $categories = LunchCategory::all();
        return view('erp.lunch.index', compact('products', 'orders', 'categories'));
    }

    public function store(Request $request)
    {
        $this->authorize('leave.create');
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:lunch_categories,id',
            'price' => 'required|numeric|min:0',
        ]);

        LunchProduct::create($validated);

        return back()->with('success', 'Product added successfully.');
    }

    public function orders()
    {
        $this->authorize('lunch.read');
        $orders = LunchOrder::with(['employee.user', 'lines.product'])->latest()->get();
        $employees = Employee::with('user')->where('status', 'active')->get();
        $products = LunchProduct::with('category')->where('is_active', true)->get();
        return view('erp.lunch.orders', compact('orders', 'employees', 'products'));
    }

    public function storeOrder(Request $request)
    {
        $this->authorize('lunch.create');
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:lunch_products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $employee = auth()->user()->employee;
        if (!$employee) {
            return back()->withErrors(['error' => 'You must be linked to an employee record to place orders.']);
        }
        $order = LunchOrder::create([
            'employee_id' => $employee->id,
            'order_date' => now(),
            'status' => 'pending',
        ]);

        foreach ($validated['items'] as $item) {
            LunchOrderLine::create([
                'lunch_order_id' => $order->id,
                'lunch_product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
            ]);
        }

        return back()->with('success', 'Order placed successfully.');
    }

    public function updateOrderStatus(Request $request, LunchOrder $order)
    {
        $this->authorize('lunch.update');
        $validated = $request->validate([
            'status' => 'required|string|max:50',
        ]);

        $order->update(['status' => $validated['status']]);

        return back()->with('success', 'Order status updated successfully.');
    }
}
