<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\ApiBaseController;

class ExpenseController extends ApiBaseController
{

    public function index(Request $request)
    {
        $expenses = \App\Models\Expense::with('vehicle')->orderBy('id','DESC');

        if (!blank($request->vehicle_id) && !blank($request->date_from) &&  !blank($request->date_to)) {
            $expenses = $expenses->where('vehicle_id',$request->vehicle_id)
                        ->whereDate('created_at','>=',$request->date_from)
                        ->whereDate('created_at','<=',$request->date_to);
        }elseif(!blank($request->date_from) && !blank($request->date_to)){
            $expenses = $expenses->whereDate('created_at','>=',$request->date_from)
            ->whereDate('created_at','<=',$request->date_to);
        }elseif(!blank($request->vehicle_id)){
            $expenses = $expenses->where('vehicle_id',$request->vehicle_id);
        }
        $expenses = $expenses->get();
        
        return $this->sendResponse($expenses, 'Expense list.',200,200);
    }

    public function create(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'project_id'        =>  'required|numeric|exists:projects,id',
            'vehicle_id'        =>  'required|numeric|exists:vehicles,id',
            'expense'           =>  'required|numeric',
            'expence_details'   =>  'required'
        ]);
        if ($validator->fails()) {
            return $this->sendSingleFieldError($validator->errors()->first(),201,201);
        }
        $inputs  = $request->all();
        $inputs['employee_id'] = \Auth::user()->id;
        $expense = \App\Models\Expense::create($inputs);

        return $this->sendResponse($expense, 'Expense addeed successfully.',200,200);
    }


}
