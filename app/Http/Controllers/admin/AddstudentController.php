<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Room;
use App\Models\RoomStudent;
use App\Models\Semester;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AddstudentController extends Controller
{
    public function index(Request $request)
    {


        if ($request->ajax()) {
            if ($request->search) {
                $data = Room::where('id', $request->search)->first();
                $teacher = Teacher::where('id', $data->teacher_id)->first();
                $semester = Semester::where('id', $data->semester_id)->first();
                $arr = RoomStudent::where('room_id', $data->id)->pluck('student_id')->toArray();
                $student = Student::whereNotIn('id', $arr)->get();
            }
            return response()->json(['data' => $data, 'arr' => $arr, 'teacher' => $teacher, 'semester' => $semester, 'student' => $student]);
        } else {
            $student = Student::all();
            $room = Room::all();
            $teacher = Teacher::all();
            $semester = Semester::all();
        }

        return view('admin.student.add-student-room', ['student' => $student, 'semester' => $semester, 'room' => $room, 'teacher' => $teacher, 'semester' => $semester]);
    }
    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'student_id' => 'required',
                'teacher_id' => 'required',
                'room_id' => 'required',
                'semester_id' => 'required',
            ],
            [
                'student_id.required' => "Sinh viên không được để trống.",
                'teacher_id.required' => 'Giáo viên không được để trống.',
                'room_id.required' => 'Lớp học không được để trống.',
                'semester_id.required' => 'Kỳ học không được để trống.',
            ]
        );
        if ($validator->passes()) {
            $room = Room::where('id', $request->room_id)->first();
            $student = Student::whereIn('id', $request->student_id)->get();
            $nofi = "Bạn đã được thêm vào lớp " . $room->name;
            // $data =  RoomStudent::whereIn('student_id', $request->student_id)->get();
            foreach ($student as $row) {
                $data = new RoomStudent();
                $data->student_id = $row->id;
                $data->teacher_id = $request->teacher_id;
                $data->room_id = $request->room_id;
                $data->semester_id = $request->semester_id;
                $data->save();
            };

            foreach ($student as $item) {
                $notifi = new Notification();
                $notifi->name = $nofi;
                $notifi->student_id = $item->id;
                $notifi->save();
            }


            return response()->json(['success' => 'Thêm sinh viên vào lớp ' . $room->name . ' thành công', 'data' => $request->all(), 'nofi' => $nofi]);
        }
        return response()->json([
            'message' => array_combine($validator->errors()->keys(), $validator->errors()->all()),
        ]);
    }
}
