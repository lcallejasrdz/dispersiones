<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MovementOutput;

class ListController extends Controller
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
    public function finals($year, $month)
    {
        $active = $this->active;

        $final_movements = MovementOutput::join('movements', function ($join) use($year, $month) {
                                                $join->on('movement_outputs.movement_id', '=', 'movements.id')
                                                        ->where('status', '>=', 6)
                                                        ->whereYear('movements.created_at', '=', $year)
                                                        ->whereMonth('movements.created_at', '=', $month);
                                            })
                                            ->select('movement_outputs.id', 'movement_outputs.movement_id', 'movements.created_at', 'movement_outputs.customer', 'movement_outputs.movement_type', 'movement_outputs.amount', 'movement_outputs.bco_cta_customer', 'movement_outputs.receipt')
                                            ->get();
        
        return view('list/finals', compact($this->compact, 'year', 'month', 'final_movements'));
    }
}
