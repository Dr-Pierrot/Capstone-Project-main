<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Student;
use App\Models\Section;
use App\Models\ClassCard;
use App\Models\Enrollment;


class SubjectController extends Controller
{
    public function index()
    {
        $subjects = Subject::where('user_id', Auth::id())->get();
        return view('subjects.index', compact('subjects'));
    }

    public function showEnroll(Request $request)
    {
        $teacherId = auth()->user()->id;

        $subjectID = $request->input('subject_id');

        $query = Student::where('user_id', $teacherId);
        $students = $query->orderBy('id', 'desc')->get();

        $sections = Section::where('user_id', $teacherId)->get();
        $subject = Subject::where('user_id', Auth::id())->where('id', $subjectID)->first();
        $enrolls = ClassCard::where('subject_id', $subjectID)->get();
        return view('subjects.enroll', compact('students', 'subject', 'sections', 'enrolls'));
    }

    public function getSubjectApi()
    {
        $subjects = Subject::where('user_id', Auth::id())->get();
        
        return response()->json([
            'success' => true,
            'subjects' => $subjects, // Return the token
        ]);
    }

    public function create()
    {
        return view('subjects.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'course_code' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        // Check if the student is already enrolled in the subject
        $subjectExists = Subject::where('course_code', $request->course_code) 
            ->where('name', $request->name)
            ->exists();

        // Optional: Check if student is already enrolled
        if ($subjectExists) {
            return redirect()->back()->with('error', 'Subject already exists.');
        }

        Subject::create([
            'course_code' => $request->course_code,
            'name' => $request->name,
            'description' => $request->description,
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('subjects.index')->with('success', 'Subject created successfully.');
    }

    public function enrollStudents(Request $request)
    {
        // Validate the request data
        $request->validate([
            'student_id' => 'required|exists:students,id', // Check if student exists
            'subject_id' => 'required|exists:subjects,id', // Check if subject exists
        ]);

        if($request->section_id == ""){
            return redirect()->back()->with('error', 'Please select a section.');
        }

        // Check if the student is already enrolled in the subject
        $enrollmentExists = ClassCard::where('student_id', $request->student_id)
            ->where('subject_id', $request->subject_id)
            ->exists();

        // Optional: Check if student is already enrolled
        if ($enrollmentExists) {
            return redirect()->back()->with('error', 'Student is already enrolled in this subject.');
        }

        // Create the enrollment record
        ClassCard::create([
            'student_id' => $request->student_id,
            'user_id' => Auth::id(), // Get the authenticated user
            'subject_id' => $request->subject_id,
            'section_id' => $request->section_id,
        ]);


        return redirect()->route('subjects.showEnroll', ['subject_id' => $request->subject_id])
                        ->with('success', 'Student enrolled successfully.');
    }




    public function edit(Subject $subject)
    {
        return view('subjects.edit', compact('subject'));
    }

    public function update(Request $request, Subject $subject)
    {
        $request->validate([
            'course_code' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        if ($subject->user_id !== Auth::id()) {
            return redirect()->route('subjects.index')->with('error', 'You are not authorized to update this subject.');
        }

        $subjectExists = Subject::where('course_code', $request->course_code) 
            ->where('name', $request->name)
            ->exists();

        // Optional: Check if student is already enrolled
        if ($subjectExists) {
            return redirect()->back()->with('error', 'Subject already exists.');
        }

        $subject->update([
            'course_code' => $request->course_code,
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return redirect()->route('subjects.index')->with('success', 'Subject updated successfully.');
    }

    public function destroy(Subject $subject)
    {
        if ($subject->user_id !== Auth::id()) {
            return redirect()->route('subjects.index')->with('error', 'You are not authorized to delete this subject.');
        }

        $subject->delete();

        return redirect()->route('subjects.index')->with('success', 'Subject deleted successfully.');
    }

    public function unEnrollStudent(ClassCard $enroll){
        if ($enroll->user_id !== Auth::id()) {
            return redirect()->back()->with('error', 'You are not authorized to unenroll this student.');
        }

        $enroll->delete();
        return redirect()->back()->with('success', 'Student unenrolled successfully.');
    }
}
