<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\BorrowMovement as MasterRequest;
use App\Models\Movement;
use App\Models\MovementOutput;
use Redirect;

class BorrowController extends Controller
{
    public function __construct()
    {
        // $this->middleware('moduleProducts');

        // General
        $this->active = explode('.',\Request::route()->getName())[0];

        // Final compact
        $this->compact = ['active'];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $active = $this->active;

        $netx_movement = Movement::count() + 1;
        
        return view('new-movement/borrow', compact($this->compact, 'netx_movement'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(MasterRequest $request)
    {
        // Movement type
        // 1 - Direct Movement
        // 2 - Payroll Movement
        // 3 - Simple Movement
        // 4 - Borrow Movement

        // Status Movement
        // 1 - New Movement Registered

        $movement = Movement::create([
            'customer' => $request->company_input,
            'amount_entry' => 0,
            'amount_output' => $request->quantity_input,
            'type' => 4,
            'status' => 1
        ]);

        // Add Outputs
        MovementOutput::create([
            'movement_id' => $movement->id,
            'customer' => $request->company_input,
            'amount' => $request->quantity_input,
            'disperser' => $request->disperser_input,
            'bco_cta_disperser' => $request->bank_origen_input,
            'bco_cta_customer' => $request->bank_destiny_input,
            'comment' => $request->comment_input
        ]);

        return Redirect::route($this->active.'.create')->with('success', trans('module_'.$this->active.'.crud.create.success'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
