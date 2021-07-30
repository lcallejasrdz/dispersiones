<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\Company as MasterRequest;
use App\Models\Company as MasterModel;
use DateTime;
use Redirect;
use Illuminate\Support\Str as Str;

class CompanyController extends Controller
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

        $items = MasterModel::all();

        return view('admin.crud.index', compact('items', $this->compact));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $active = $this->active;
        
        return view('admin.crud.create', compact($this->compact));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(MasterRequest $request)
    {
        if(MasterModel::where('name', $request->name)->count() > 0){
            return Redirect::back()->with('error', trans('module_'.$this->active.'.crud.create.exists'));
        }else{
            /* Create slug */
            $now = new DateTime();
            $current_date = $now->format('YmdHis');
            $slug = Str::slug($current_date.$request->_token.rand(1000,9999));

            /* Create Item */
            $item = MasterModel::create([
                        'slug' => $slug,
                        'name' => $request->name,
                    ]);

            if(MasterModel::where('id', $item->id)->count() > 0){
                return Redirect::route($this->active)->with('success', trans('module_'.$this->active.'.crud.create.success'));
            }else{
                $item->forceDelete();

                return Redirect::back()->with('error', trans('module_'.$this->active.'.crud.create.error'));
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $slug
     * @return \Illuminate\Http\Response
     */
    public function show($slug)
    {
        $active = $this->active;
        
        $item = MasterModel::where('slug', $slug)
                ->select(
                    'id',
                    'slug',
                    'name',
                )
                ->first();

        return view('admin.crud.show', compact('item', $this->compact));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  string  $slug
     * @return \Illuminate\Http\Response
     */
    public function edit($slug)
    {
        $active = $this->active;
        
        $item = MasterModel::where('slug', $slug)
                ->select(
                    'id',
                    'slug',
                    'name',
                )
                ->first();

        return view('admin.crud.edit', compact('item', $this->compact));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $slug
     * @return \Illuminate\Http\Response
     */
    public function update(MasterRequest $request, $slug)
    {
        $item = MasterModel::where('slug', $slug)->first();

        $item->name = $request->name;

        if($item->save()){
            return Redirect::route('companies.edit', $item->slug)->with('success', trans('module_'.$this->active.'.crud.update.success'));
        }else{
            return Redirect::back()->with('error', trans('module_'.$this->active.'.crud.update.error'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  string  $slug
     * @return \Illuminate\Http\Response
     */
    public function destroy(MasterRequest $request)
    {
        $item = MasterModel::withTrashed()->where('slug', $request->slug)->first();

        if(MasterModel::destroy($item->id)){
            return Redirect::route($this->active)->with('success', trans('module_'.$this->active.'.crud.delete.success'));
        }else{
            return Redirect::back()->with('error', trans('module_'.$this->active.'.crud.delete.error'));
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function deleted()
    {
        $active = $this->active;
        
        $items = MasterModel::onlyTrashed()->get();

        return view('admin.crud.deleted', compact('items', $this->compact));
    }

    /**
     * Restore the specified resource from storage.
     *
     * @param  string  $slug
     * @return \Illuminate\Http\Response
     */
    public function restore($slug)
    {
        $item = MasterModel::withTrashed()->where('slug', $slug)->first();

        if($item->restore()){
            return Redirect::route('companies.deleted')->with('success', trans('module_'.$this->active.'.crud.restore.success'));
        }else{
            return Redirect::back()->with('error', trans('module_'.$this->active.'.crud.restore.error'));
        }
    }
}
