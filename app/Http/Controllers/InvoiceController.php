<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MovementEntry;
use App\Models\MovementDispersion;
use DateTime;
use Illuminate\Support\Str as Str;
use Redirect;

class InvoiceController extends Controller
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
    public function accounting($year, $month)
    {
        $active = $this->active;

        $direct_movements = MovementEntry::join('movements', function ($join) use($year, $month) {
                                                $join->on('movement_entries.movement_id', '=', 'movements.id')
                                                        ->where('status', '>=', 3)
                                                        ->where('type', 1)
                                                        ->whereYear('movements.created_at', '=', $year)
                                                        ->whereMonth('movements.created_at', '=', $month);
                                            })
                                            ->join('companies', 'movement_entries.company_id', '=', 'companies.id')
                                            ->select('movement_entries.id', 'movement_entries.movement_id', 'movements.created_at', 'companies.name', 'movements.customer', 'movement_entries.amount', 'movement_entries.folio', 'movement_entries.receipt')
                                            ->get();

        $payroll_movements = MovementEntry::join('movements', function ($join) use($year, $month) {
                                                $join->on('movement_entries.movement_id', '=', 'movements.id')
                                                        ->where('status', '>=', 3)
                                                        ->where('type', 2)
                                                        ->whereYear('movements.created_at', '=', $year)
                                                        ->whereMonth('movements.created_at', '=', $month);
                                            })
                                            ->join('companies', 'movement_entries.company_id', '=', 'companies.id')
                                            ->select('movement_entries.id', 'movement_entries.movement_id', 'movements.created_at', 'companies.name', 'movements.customer', 'movement_entries.amount', 'movement_entries.folio', 'movement_entries.receipt')
                                            ->get();

        $simple_movements = MovementEntry::join('movements', function ($join) use($year, $month) {
                                                $join->on('movement_entries.movement_id', '=', 'movements.id')
                                                        ->where('status', '>=', 3)
                                                        ->where('type', 3)
                                                        ->whereYear('movements.created_at', '=', $year)
                                                        ->whereMonth('movements.created_at', '=', $month);
                                            })
                                            ->join('companies', 'movement_entries.company_id', '=', 'companies.id')
                                            ->select('movement_entries.id', 'movement_entries.movement_id', 'movements.created_at', 'companies.name', 'movements.customer', 'movement_entries.amount', 'movement_entries.folio', 'movement_entries.receipt')
                                            ->get();
        
        return view('invoice/accounting', compact($this->compact, 'year', 'month', 'direct_movements', 'payroll_movements', 'simple_movements'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function accounting_store(Request $request)
    {
        $control = 0;

        // Add Invoice
        for($i = 0; $i < count($request->movement_entry_id); $i++){
            if(isset($request->file('receipt')[$i]) && ($request->folio[$i] != '' && $request->folio[$i] != null)){
                $control = 1;

                /* Create slug */
                $now = new DateTime();
                $current_date = $now->format('YmdHis');
                $slug = Str::slug($current_date.$request->_token.rand(1000,9999));

                $destinationPath = public_path() . '/uploads/accounting_invoices/';

                $file_temp = $request->file('receipt')[$i];
                $extension = $file_temp->getClientOriginalExtension() ?: 'pdf';
                $safeName = $slug.'.'.$extension;

                $entry = MovementEntry::find($request->movement_entry_id[$i]);
                $entry->receipt = $safeName;
                $entry->folio = $request->folio[$i];
                $entry->save();

                $file_temp->move($destinationPath, $safeName);
            }
        }

        if($control == 1){
            return Redirect::back()->with('success', trans('module_'.$this->active.'.crud.accounting.success'));
        }else{
            return Redirect::back()->with('warning', trans('module_'.$this->active.'.crud.accounting.any'));
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function dispersions($year, $month)
    {
        $active = $this->active;

        $dispersion_movements = MovementDispersion::join('movements', function ($join) use($year, $month) {
                                                $join->on('movement_dispersions.movement_id', '=', 'movements.id')
                                                        ->where('status', '>=', 5)
                                                        ->whereYear('movements.created_at', '=', $year)
                                                        ->whereMonth('movements.created_at', '=', $month);
                                            })
                                            ->join('companies as dis', 'movement_dispersions.disperser_id', '=', 'dis.id')
                                            ->join('companies as des', 'movement_dispersions.destiny_id', '=', 'des.id')
                                            ->select('movement_dispersions.id', 'movement_dispersions.movement_id', 'movements.created_at', 'des.name as destiny', 'dis.name as disperser', 'movement_dispersions.amount', 'movement_dispersions.folio', 'movement_dispersions.receipt')
                                            ->get();
        
        return view('invoice/dispersions', compact($this->compact, 'year', 'month', 'dispersion_movements'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function dispersions_store(Request $request)
    {
        $control = 0;

        // Add Invoice
        for($i = 0; $i < count($request->movement_dispersion_id); $i++){
            if(isset($request->file('receipt')[$i]) && ($request->folio[$i] != '' && $request->folio[$i] != null)){
                $control = 1;

                /* Create slug */
                $now = new DateTime();
                $current_date = $now->format('YmdHis');
                $slug = Str::slug($current_date.$request->_token.rand(1000,9999));

                $destinationPath = public_path() . '/uploads/dispersions_invoices/';

                $file_temp = $request->file('receipt')[$i];
                $extension = $file_temp->getClientOriginalExtension() ?: 'pdf';
                $safeName = $slug.'.'.$extension;

                $entry = MovementDispersion::find($request->movement_dispersion_id[$i]);
                $entry->receipt = $safeName;
                $entry->folio = $request->folio[$i];
                $entry->save();

                $file_temp->move($destinationPath, $safeName);
            }
        }

        if($control == 1){
            return Redirect::back()->with('success', trans('module_'.$this->active.'.crud.dispersions.success'));
        }else{
            return Redirect::back()->with('warning', trans('module_'.$this->active.'.crud.dispersions.any'));
        }
    }
}
