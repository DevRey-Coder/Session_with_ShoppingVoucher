<?php

namespace App\Http\Controllers;

use App\Http\Resources\VoucherResource;
use App\Models\Voucher;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class VoucherController extends Controller
{
    public function index()
    {
        $vouchers = Voucher::when(request()->has('keyword'),function($query){
            $query->where(function(Builder $builder){
                $keyword = request()->keyword;
                $builder->where('name','LIKE','%'.$keyword.'%');
            });
        })->latest('id')->paginate(10)->withQueryString();

        return response()->json([
            'status' => true,
            'data' => VoucherResource::collection($vouchers)
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
           'items' => 'required|array',
           'items.*.name' => 'required|string|in:products,name',
           'items.*.amount' => 'required|integer',
           'note' => 'required|string|min:3'
        ]);

        // $table->string('voucher_no');
        // $table->integer('total');
        // $table->string('note')->nullable();

        $voucher_no = fake()->regexify('LD\d{4}[a-z]{2}[!@#$%^&*]');
        $amounts = collect($request->items);
        $total = $amounts->sum('amount');
        $note = $request->note;

        $voucher = Voucher::create([
           'voucher_no' => $voucher_no,
           'total' => $total,
           'note' => $note
        ]);
        $request->session()->put('voucher',$voucher);

        return response()->json([
           'status' => true,
           'data' => new VoucherResource($voucher)
        ]);
    }

    public function show(Request $request ,Voucher $voucher)
    {
       $sessionVoucher = $request->session()->get('voucher',$voucher);

       return response()->json([
           'status' => true,
           'data' => new VoucherResource($sessionVoucher)
       ]);
    }

    public function destroy(Request $request ,Voucher $voucher)
    {
        if($request->session()){
            $request->session()->flush();
        }

        $voucher->delete();
        return response()->json([
            'data' => "Voucher has been deleted with repective sessions' data."
        ]);
    }
}
