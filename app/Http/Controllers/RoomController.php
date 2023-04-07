<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Semester;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;

class RoomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Room::with(['teachers', 'subjects','semesters'])->where('teacher_id', '=', auth('teacher')->user()->id)->select('rooms.*')->get();
            return Datatables::of($data)
                ->addIndexColumn()
                ->editColumn('teachers', function ($data) {
                    return $data->teachers->name;
                })
                ->editColumn('subjects', function ($data) {
                    return $data->subjects->name;
                })
                ->editColumn('semesters', function ($data) {
                    return $data->semesters->name;
                })
                ->addColumn('action', function ($row) {

                    $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $row->id . '" data-original-title="Edit" class="edit btn btn-primary btn-sm editProduct">Edit</a>';

                    $btn = $btn . ' <a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $row->id . '" data-original-title="Delete" class="btn btn-danger btn-sm deleteProduct">Delete</a>';

                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        $subject = Subject::all();
        $semester = Semester::all();
        // $data = Room::with(['teachers','subjects'])->select('rooms.*')->get();
        $data = Room::with(['teachers', 'subjects'])->select('rooms.*')->get();
        return view('teacher.room.index', ['subject' => $subject, 'semester' => $semester, 'data' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data =  Room::updateOrCreate(
            ['id' => $request->_id],
            [
                'name' => $request->name,
                'teacher_id' => $request->teacher_id,
                'student_id' => $request->student_id,
                'subject_id' => $request->subject_id,
                'semester_id' => $request->semester_id,
            ]
        );
        return response()->json(['success' => 'Product successfully.', $data]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $product = Room::find($id);
        return response()->json($product);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Room::find($id)->delete();
        return response()->json(['success' => 'Product deleted successfully.']);
    }
}
