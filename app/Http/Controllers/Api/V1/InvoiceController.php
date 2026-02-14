<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\V1\InvoiceResource;
use App\Models\Invoice;
use App\Traits\httpResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class InvoiceController
{
    use httpResponses;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        
        $invoices = Invoice::with('user')->get();

        if(!$invoices){
            return $this->error('Invoces is empty', 404);
        }

        return InvoiceResource::collection($invoices);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
       //Validação dos dados 
       $validator = Validator::make($request->all(), [
        'user_id' => 'required|integer|exists:users,id',
        'type' => 'required|max:1|in:' . implode(',', ['B', 'C', 'P']),
        'paid' => 'required|numeric|boolean',
        'payment_date' => 'nullable|date',
        'value' => 'required|numeric|between:1,9999.99'
       ]);

       if($validator->fails()){
        return $this->error('Data invalid', 422, $validator->errors());
       }

       $created = Invoice::create($validator->validated());

       if (!$created){
        return $this->error('Error to create invoice', 400);
       } 

       return $this->response('Invoice created', 201, new InvoiceResource($created->load('user')));
    }

    /**
     * Display the specified resource.
     */
    public function showId(string $id) // 1ª FORMA
    {
        $invoice = Invoice::with('user')->find($id);

        if(!$invoice){
            return $this->error('Invoice not found', 404);
        }

        return new InvoiceResource($invoice);
    }

    public function show(Invoice $invoice) // 2ª FORMA
    {
        return new InvoiceResource($invoice);
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator= Validator::make($request->all(), [
            'user_id' => 'required|integer|exists:users,id',
            'type' => 'required|max:1|in:' . implode(',', ['B', 'C', 'P']),
            'paid' => 'required|numeric|boolean',
            'value' => 'required|numeric',
            'payment_date' => 'nullable|date_format:Y-m-d H:i:s',
        ]);

        if($validator->fails()) {
            return $this->error('Validation failed', 422, $validator->errors());
        }

        // pega todos os valores já validados
        $validated = $validator->validated();
        
        $invoice = Invoice::find($id);

        if(!$invoice) {
            return $this->error('Invoice not found', 404);
        }
        
        $paymentDate = null;

        $validated['paid'] ? $paymentDate = $validated['payment_date'] ?? now() : null;

        $updated = $invoice->update(([
            'user_id' => $validated['user_id'],
            'type' => $validated['type'],
            'paid' => $validated['paid'],
            'value' => $validated['value'],
            'payment_date' => $paymentDate,
        ]));

        if(!$updated) {
            return $this->error('Invoice not updated', 400);
        }

        return $this->response('Invoice updated', 200, new InvoiceResource($invoice->load('user')));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Invoice $invoice)
    {
        $deleted = $invoice->delete();

        if(!$deleted){
            return $this->error('Invoice not found', 404);
        }

        return $this->response('Invoice deleted', 200);
    }
}
