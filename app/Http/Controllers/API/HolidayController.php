<?php

namespace App\Http\Controllers\API;

use App\Libraries\Permissions;
use App\Models\Holiday;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;

class HolidayController extends Controller
{
    public function index()
    {
        $data = Holiday::orderBy('id', 'DESC')->get();

        return $data;
    }

    public function sortedList(Request $request)
    {
        if ($request->columnKey) $columnName = $request->columnKey;
        if ($request->rowLimit) $limit = $request->rowLimit;
        $offset = $request->rowOffset;

        $data = Holiday::orderBy($columnName, $request->columnSortedBy)->take($limit)->skip($offset)->get();
        $totalCount = Holiday::count();

        return ['dataRows' => $data, 'count' => $totalCount];
    }

    public function permissionCheck()
    {
        $controller = new Permissions;

        return $controller;
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
        ]);

        $title = $request->input('title');
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        $created_by = Auth::user()->id;

        if ($holiday = Holiday::create([
            'title' => $title,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'created_by' => $created_by,
        ])) {

            $response = [
                'msg' => Lang::get('lang.created_successfully'),
                'Holiday' => $holiday
            ];

            return response()->json($response, 201);
        }

        $response = [
            'msg' => Lang::get('lang.error_during_creating')
        ];

        return response()->json($response, 404);
    }

    public function show($id)
    {
        $data = Holiday::find($id);

        return $data;
    }

    public function updateHolidays(Request $request, $id)
    {
        try {
            $holiday = Holiday::find($id);
            $holiday->update($request->all());

        } catch (\Exception $e) {

            return $e;
        }

    }

    public function destroy($id)
    {
        $role = Holiday::find($id);
        $role->delete();
    }

}
