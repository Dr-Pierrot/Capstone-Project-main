<?php

namespace App\Http\Controllers;
use App\Models\Student;
use App\Models\Section;
use App\Models\Subject;
use App\Models\Attendance;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        // Fetch students or any necessary data
        $teacherId = auth()->user()->id;

        $students = Student::whereHas('subject', function ($query) use ($teacherId) {
            $query->where('user_id', $teacherId);
        })->orderBy('id')->get();

        $student_id = $request->input('student_id') ?? $students->first()->id;

        $student = $students->find($student_id);
        if (!$student) {
            return redirect()->route('class-card.index')->with('error', 'Student not found.');
        }

        // Get all student IDs that belong to the teacher
        $studentIds = $students->pluck('id')->toArray();

        // Determine previous and next student IDs
        $currentIndex = array_search($student_id, $studentIds);
        $prevStudentId = $currentIndex > 0 ? $studentIds[$currentIndex - 1] : null;
        $nextStudentId = $currentIndex < count($studentIds) - 1 ? $studentIds[$currentIndex + 1] : null;

        // Fetch attendance records for the student
        $attendanceRecords = Attendance::where('student_id', $student_id)->where('type', 1)->get(); // For lectures
        $labAttendanceRecords = Attendance::where('student_id', $student_id)->where('type', 2)->get(); // For labs
        // return $labAttendanceRecords;
        return view('attendance.index', compact('student', 'prevStudentId', 'nextStudentId', 'attendanceRecords', 'labAttendanceRecords'));
    }


    public function store(Request $request)
    {
        // Validate incoming request
        $request->validate([
            'student_id' => 'required|integer',
            'subject_id' => 'required|integer',
            'section_id' => 'required|integer',
            'day' => 'required|integer',
            'attendance_date' => 'required|integer', // Ensure this matches the request
            'type' => 'required|integer',
            'status' => 'required|integer',
        ]);

        // Create or update attendance logic
        try {
            Attendance::updateOrCreate(
                [
                    'student_id' => $request->student_id,
                    'subject_id' => $request->subject_id,
                    'section_id' => $request->section_id,
                    'day' => $request->day,
                    'attendance_date' => $request->attendance_date,
                    'type' => $request->type,
                ],
                [
                    'status' => $request->status,
                ]
            );

            return response()->json(['success' => 'Attendance recorded successfully.']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    
}
