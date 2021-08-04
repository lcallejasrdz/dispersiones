<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\DirectMovement as MasterRequest;
use App\Models\Company;
use App\Models\Movement;
use App\Models\MovementEntry;
use App\Models\MovementOutput;
use Redirect;

class DirectController extends Controller
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
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $active = $this->active;

        $netx_movement = Movement::count() + 1;
        $companies = Company::orderBy('name', 'asc')->get();
        
        return view('new-movement/direct', compact($this->compact, 'netx_movement', 'companies'));
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
            'customer' => $request->customer,
            'amount_entry' => $request->entry_total,
            'amount_output' => $request->output_total,
            'type' => 1,
            'status' => 1
        ]);

        // Add Entries
        for($i = 0; $i < count($request->entry_company); $i++){
            MovementEntry::create([
                'movement_id' => $movement->id,
                'company_id' => $request->entry_company[$i],
                'amount' => $request->entry_quantity[$i],
                'bank' => $request->entry_bank[$i],
                'account' => $request->entry_account[$i]
            ]);
        }

        // Add Outputs
        for($i = 0; $i < count($request->output_company); $i++){
            MovementOutput::create([
                'movement_id' => $movement->id,
                'customer' => $request->output_company[$i],
                'movement_type' => $request->output_type[$i],
                'amount' => $request->output_quantity[$i],
                'disperser' => $request->output_disperser[$i],
                'bco_cta_disperser' => $request->output_bank_origen[$i],
                'bco_cta_customer' => $request->output_bank_destiny[$i],
                'comment' => $request->output_comment[$i]
            ]);
        }

        return Redirect::route($this->active.'.create')->with('success', trans('module_'.$this->active.'.crud.create.success'));
    }
}
