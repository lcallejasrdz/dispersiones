<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Company;
use App\Models\Movement;
use App\Models\MovementEntry;
use App\Models\MovementOutput;
use App\Models\MovementDispersion;
use Redirect;
use DateTime;
use Illuminate\Support\Str as Str;

class FollowingController extends Controller
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
        $active = $this->active;

        $direct_movements = Movement::where('type', 1)->where('status', '<', 6)->get();
        $payroll_movements = Movement::where('type', 2)->where('status', '<', 6)->get();
        $simple_movements = Movement::where('type', 3)->where('status', '<', 6)->get();
        $borrow_movements = Movement::where('type', 4)->where('status', '<', 6)->get();

        return view('following/index', compact($this->compact, 'direct_movements', 'payroll_movements', 'simple_movements', 'borrow_movements'));
    }

    // Direct Movements

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function direct_following_create($id)
    {
        $active = $this->active;

        $movement = Movement::find($id);
        if($movement->status > 4 || $movement->type != 1){
            return Redirect::route($this->active);
        }
        $entries = DB::table('movement_entries')->where('movement_id', $id)
            ->join('companies', 'movement_entries.company_id', '=', 'companies.id')
            ->select('movement_entries.*', 'companies.name')
            ->get();
        $outputs = MovementOutput::where('movement_id', $id)->get();
        $dispersions = DB::table('movement_dispersions')->where('movement_id', $id)
            ->join('companies as dis', 'movement_dispersions.disperser_id', '=', 'dis.id')
            ->join('companies as des', 'movement_dispersions.destiny_id', '=', 'des.id')
            ->select('movement_dispersions.*', 'dis.name as disperser_name', 'des.name as destiny_name')
            ->get();

        $companies = Company::orderBy('name', 'asc')->get();
        
        return view('following/direct_following', compact($this->compact, 'companies', 'movement', 'entries', 'outputs', 'dispersions'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function direct_following_store(Request $request, $id)
    {
        // Add Dispersions
        for($i = 0; $i < count($request->disperser); $i++){
            MovementDispersion::create([
                'movement_id' => $id,
                'disperser_id' => $request->disperser[$i],
                'disperser_bank_account' => $request->disperser_bank[$i],
                'amount' => $request->quantity[$i],
                'destiny_id' => $request->destiny[$i],
                'destiny_bank_account' => $request->destiny_bank[$i],
                'final_account' => $request->final_account[$i],
            ]);
        }

        $movement = Movement::find($id);
        if($movement->status == 1){
            $movement->status = 2;
        }else if($movement->status == 3){
            $movement->status = 4;
        }

        if($movement->save()){
            return Redirect::back()->with('success', trans('module_'.$this->active.'.crud.create.success'));
        }else{
            return Redirect::back()->with('error', trans('module_'.$this->active.'.crud.create.error'));
        }
    }

    // Payroll Movements

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function payroll_following_create($id)
    {
        $active = $this->active;

        $movement = Movement::find($id);
        if($movement->status > 4 || $movement->type != 2){
            return Redirect::route($this->active);
        }
        $entries = DB::table('movement_entries')->where('movement_id', $id)
            ->join('companies', 'movement_entries.company_id', '=', 'companies.id')
            ->select('movement_entries.*', 'companies.name')
            ->get();
        $dispersions = DB::table('movement_dispersions')->where('movement_id', $id)
            ->join('companies as dis', 'movement_dispersions.disperser_id', '=', 'dis.id')
            ->join('companies as des', 'movement_dispersions.destiny_id', '=', 'des.id')
            ->select('movement_dispersions.*', 'dis.name as disperser_name', 'des.name as destiny_name')
            ->get();

        $companies = Company::orderBy('name', 'asc')->get();
        
        return view('following/payroll_following', compact($this->compact, 'companies', 'movement', 'entries', 'dispersions'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function payroll_following_store(Request $request, $id)
    {
        // Add Dispersions
        for($i = 0; $i < count($request->disperser); $i++){
            MovementDispersion::create([
                'movement_id' => $id,
                'disperser_id' => $request->disperser[$i],
                'disperser_bank_account' => $request->disperser_bank[$i],
                'amount' => $request->quantity[$i],
                'destiny_id' => $request->destiny[$i],
                'destiny_bank_account' => $request->destiny_bank[$i],
                'final_account' => $request->final_account[$i],
            ]);
        }

        $movement = Movement::find($id);
        if($movement->status == 1){
            $movement->status = 2;
        }else if($movement->status == 3){
            $movement->status = 4;
        }

        if($movement->save()){
            return Redirect::back()->with('success', trans('module_'.$this->active.'.crud.create.success'));
        }else{
            return Redirect::back()->with('error', trans('module_'.$this->active.'.crud.create.error'));
        }
    }

    // Simple Movements

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function simple_following_create($id)
    {
        $active = $this->active;

        $movement = Movement::find($id);
        if($movement->status > 4 || $movement->type != 3){
            return Redirect::route($this->active);
        }
        $entries = DB::table('movement_entries')->where('movement_id', $id)
            ->join('companies', 'movement_entries.company_id', '=', 'companies.id')
            ->select('movement_entries.*', 'companies.name')
            ->get();
        $outputs = MovementOutput::where('movement_id', $id)->get();
        $dispersions = DB::table('movement_dispersions')->where('movement_id', $id)
            ->join('companies as dis', 'movement_dispersions.disperser_id', '=', 'dis.id')
            ->join('companies as des', 'movement_dispersions.destiny_id', '=', 'des.id')
            ->select('movement_dispersions.*', 'dis.name as disperser_name', 'des.name as destiny_name')
            ->get();

        $companies = Company::orderBy('name', 'asc')->get();
        
        return view('following/simple_following', compact($this->compact, 'companies', 'movement', 'entries', 'outputs', 'dispersions'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function simple_following_store(Request $request, $id)
    {
        // Add Dispersions
        for($i = 0; $i < count($request->disperser); $i++){
            MovementDispersion::create([
                'movement_id' => $id,
                'disperser_id' => $request->disperser[$i],
                'disperser_bank_account' => $request->disperser_bank[$i],
                'amount' => $request->quantity[$i],
                'destiny_id' => $request->destiny[$i],
                'destiny_bank_account' => $request->destiny_bank[$i],
                'final_account' => $request->final_account[$i],
            ]);
        }

        $movement = Movement::find($id);
        $entries = MovementEntry::where('movement_id', $id)->get();
        if($movement->status == 1 && $entries->count() > 0){
            $movement->status = 2;
        }else if($movement->status == 3 || ($movement->status == 1 && $entries->count() == 0)){
            $movement->status = 4;
        }

        if($movement->save()){
            return Redirect::back()->with('success', trans('module_'.$this->active.'.crud.create.success'));
        }else{
            return Redirect::back()->with('error', trans('module_'.$this->active.'.crud.create.error'));
        }
    }

    // Direct movements

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function direct_finishing_create($id)
    {
        $active = $this->active;

        $movement = Movement::find($id);
        if($movement->status != 5 || $movement->type != 1){
            return Redirect::route($this->active);
        }
        $outputs = MovementOutput::where('movement_id', $id)->get();
        
        return view('following/direct_finishing', compact($this->compact, 'movement', 'outputs'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function direct_finishing_store(Request $request, $id)
    {
        // Add Dispersions
        for($i = 0; $i < count($request->movement_id); $i++){
            if(isset($request->file('receipt')[$i])){
                /* Create slug */
                $now = new DateTime();
                $current_date = $now->format('YmdHis');
                $slug = Str::slug($current_date.$request->_token.rand(1000,9999));

                $destinationPath = public_path() . '/uploads/final_receipts/';

                $file_temp = $request->file('receipt')[$i];
                $extension = $file_temp->getClientOriginalExtension() ?: 'pdf';
                $safeName = $slug.'.'.$extension;

                $output = MovementOutput::find($request->movement_id[$i]);
                $output->receipt = $safeName;
                $output->save();

                $file_temp->move($destinationPath, $safeName);
            }
        }

        $outputs = MovementOutput::where('movement_id', $id)->whereNull('receipt')->get();
        if($outputs->count() == 0){
            $movement = Movement::find($id);
            $movement->status = 6;
            if($movement->save()){
                return Redirect::route($this->active)->with('success', trans('module_'.$this->active.'.crud.finish.success'));
            }else{
                return Redirect::back()->with('error', trans('module_'.$this->active.'.crud.finish.error'));
            }
        }

        return Redirect::back()->with('success', trans('module_'.$this->active.'.crud.file.success'));
    }

    // Simple movements

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function simple_finishing_create($id)
    {
        $active = $this->active;

        $movement = Movement::find($id);
        if($movement->status != 5 || $movement->type != 3){
            return Redirect::route($this->active);
        }
        $outputs = MovementOutput::where('movement_id', $id)->get();
        
        return view('following/simple_finishing', compact($this->compact, 'movement', 'outputs'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function simple_finishing_store(Request $request, $id)
    {
        // Add Dispersions
        for($i = 0; $i < count($request->movement_id); $i++){
            if(isset($request->file('receipt')[$i])){
                /* Create slug */
                $now = new DateTime();
                $current_date = $now->format('YmdHis');
                $slug = Str::slug($current_date.$request->_token.rand(1000,9999));

                $destinationPath = public_path() . '/uploads/final_receipts/';

                $file_temp = $request->file('receipt')[$i];
                $extension = $file_temp->getClientOriginalExtension() ?: 'pdf';
                $safeName = $slug.'.'.$extension;

                $output = MovementOutput::find($request->movement_id[$i]);
                $output->receipt = $safeName;
                $output->save();

                $file_temp->move($destinationPath, $safeName);
            }
        }

        $outputs = MovementOutput::where('movement_id', $id)->whereNull('receipt')->get();
        if($outputs->count() == 0){
            $movement = Movement::find($id);
            $movement->status = 6;
            if($movement->save()){
                return Redirect::route($this->active)->with('success', trans('module_'.$this->active.'.crud.finish.success'));
            }else{
                return Redirect::back()->with('error', trans('module_'.$this->active.'.crud.finish.error'));
            }
        }

        return Redirect::back()->with('success', trans('module_'.$this->active.'.crud.file.success'));
    }

    // Borrow movements

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function borrow_finishing_create($id)
    {
        $active = $this->active;

        $movement = Movement::find($id);
        if($movement->type != 4){
            return Redirect::route($this->active);
        }
        $outputs = MovementOutput::where('movement_id', $id)->get();
        
        return view('following/borrow_finishing', compact($this->compact, 'movement', 'outputs'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function borrow_finishing_store(Request $request, $id)
    {
        // Add Dispersions
        for($i = 0; $i < count($request->movement_id); $i++){
            if(isset($request->file('receipt')[$i])){
                /* Create slug */
                $now = new DateTime();
                $current_date = $now->format('YmdHis');
                $slug = Str::slug($current_date.$request->_token.rand(1000,9999));

                $destinationPath = public_path() . '/uploads/final_receipts/';

                $file_temp = $request->file('receipt')[$i];
                $extension = $file_temp->getClientOriginalExtension() ?: 'pdf';
                $safeName = $slug.'.'.$extension;

                $output = MovementOutput::find($request->movement_id[$i]);
                $output->receipt = $safeName;
                $output->save();

                $file_temp->move($destinationPath, $safeName);
            }
        }

        $outputs = MovementOutput::where('movement_id', $id)->whereNull('receipt')->get();
        if($outputs->count() == 0){
            $movement = Movement::find($id);
            $movement->status = 6;
            if($movement->save()){
                return Redirect::route($this->active)->with('success', trans('module_'.$this->active.'.crud.finish.success'));
            }else{
                return Redirect::back()->with('error', trans('module_'.$this->active.'.crud.finish.error'));
            }
        }

        return Redirect::back()->with('success', trans('module_'.$this->active.'.crud.file.success'));
    }

    // General functions

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function confirm_entry(Request $request, $id)
    {
        if($request->ajax()){
            $entry = MovementEntry::find($id);
            $entry->confirm = 1;

            $result = 0;
            if($entry->save()){
                $result = 1;

                $entries = MovementEntry::where('movement_id', $entry->movement_id)->whereNull('confirm')->get();
                if($entries->count() <= 0){
                    $movement = Movement::find($entry->movement_id);
                    if($movement->status == 1){
                        $movement->status = 3;
                    }else if($movement->status == 2){
                        $movement->status = 4;
                        $result = 2;
                    }

                    $movement->save();
                }
            }

            return response()->json($result);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function confirm_dispersion(Request $request, $id)
    {
        if($request->ajax()){
            $dispersion = MovementDispersion::find($id);
            $dispersion->confirm = 1;

            $result = 0;
            if($dispersion->save()){
                $result = 1;

                $entries = MovementDispersion::where('movement_id', $dispersion->movement_id)->whereNull('confirm')->get();
                if($entries->count() <= 0){
                    $movement = Movement::find($dispersion->movement_id);
                    if($movement->type == 1 || $movement->type == 3){
                        $movement->status = 5;
                    }else if($movement->type == 2){
                        $movement->status = 6;
                    }
                    $movement->save();

                    $result = 2;

                    if($movement->type == 1){
                        Session::flash('success', trans('module_'.$this->active.'.crud.finish.success'));
                    }else if($movement->type == 2){
                        Session::flash('success', trans('module_'.$this->active.'.crud.dispersions.success'));
                    }
                }
            }

            return response()->json($result);
        }
    }
}
