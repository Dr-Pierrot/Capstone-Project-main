<?php

namespace App\Http\Controllers;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Imports\StudentsImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Section;
use App\Models\Subject;
use Illuminate\Support\Facades\Log;
use App\Models\ClassCard;


class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Get the currently authenticated teacher's ID
        $teacherId = auth()->user()->id;

        // Fetch the subject and section filter from the request
        $subjectId = $request->input('subject_id');
        $sectionId = $request->input('section_id');

        // Start with the query to fetch students related to the teacher
        $query = Student::where('user_id', $teacherId);

        // Apply subject filter if selected
        if ($subjectId) {
            $query->where('subject_id', $subjectId);
        }

        // Apply section filter if selected
        if ($sectionId) {
            $query->where('section_id', $sectionId);
        }

        // Get the filtered or unfiltered list of students, ordered by id in descending order
        $students = $query->orderBy('id', 'desc')->get();

        // Fetch sections and subjects related to the teacher for the dropdowns
        $sections = Section::where('user_id', $teacherId)->get();
        $subjects = Subject::where('user_id', $teacherId)->get();

        // Return the view with the list of students and dropdown data
        return view('student.index', compact('students', 'sections', 'subjects'));
    }

    public function getStudentApi(Request $request)
    {
        // Get the currently authenticated teacher's ID
        $teacherId = Auth::id();

        // Fetch the subject and section filter from the request
        // $subjectId = $request->input('subject_id');
        // $sectionId = $request->input('section_id');

        // Start with the query to fetch students related to the teacher
        $query = Student::where('user_id', $teacherId);

        // // Apply subject filter if selected
        // if ($subjectId) {
        //     $query->where('subject_id', $subjectId);
        // }

        // // Apply section filter if selected
        // if ($sectionId) {
        //     $query->where('section_id', $sectionId);
        // }

        // Get the filtered or unfiltered list of students, ordered by id in descending order
        $students = $query->orderBy('id', 'desc')->get();

        // Fetch sections and subjects related to the teacher for the dropdowns
        // $sections = Section::where('user_id', $teacherId)->get();
        // $subjects = Subject::where('user_id', $teacherId)->get();

        // Return the view with the list of students and dropdown data
        // return view('student.index', compact('students', 'sections', 'subjects'));
        return response()->json([
            'success' => true,
            'students' => $students, // Return the token
        ]);
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
        // Validate the incoming request
        $request->validate([
            'student_number' => 'required|string|max:255',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'middle_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'gender' => 'required|string|max:255',
            'course' => 'required|string|max:255',
            'section_id' => 'required|exists:sections,id',
            'subject_id' => 'required|exists:subjects,id',
            'student_type' => 'nullable|in:regular,irregular', // Add validation for student_type
        ]);

        // Create the student
        $student = Student::create([
            'student_number' => $request->student_number,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'middle_name' => $request->middle_name,
            'date_of_birth' => $request->date_of_birth,
            'gender' => $request->gender,
            'course' => $request->course,
            'section_id' => $request->section_id,
            'subject_id' => $request->subject_id,
            'user_id' => Auth::id(),
            'student_type' => $request->student_type, // Add student_type to the create method
        ]);

        // Create a ClassCard for the newly created student
        ClassCard::create([
            'student_id' => $student->id,
            'subject_id' => $request->subject_id,
            'section_id' => $request->section_id,
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('students.index')->with('success', 'Student created successfully.');
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Student $student)
    {
        // Validate the incoming request
        $request->validate([
            'student_number' => 'required|string|max:255',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'middle_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'gender' => 'required|string|max:255',
            'course' => 'required|string|max:255',
            'section_id' => 'required|exists:sections,id', // Validate section exists
            'subject_id' => 'required|exists:subjects,id', // Validate subject exists
            'student_type' => 'nullable|in:regular,irregular', // Add validation for student_type
        ]);

        // Check if the authenticated user is authorized to update the student
        if ($student->user_id !== Auth::id()) {
            return redirect()->route('students.index')->with('error', 'You are not authorized to update this student.');
        }

        // Update the student with the request data
        $student->update([
            'student_number' => $request->student_number,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'middle_name' => $request->middle_name,
            'date_of_birth' => $request->date_of_birth,
            'gender' => $request->gender,
            'course' => $request->course,
            'section_id' => $request->section_id, // Update section
            'subject_id' => $request->subject_id, // Update subject
            'student_type' => $request->student_type, // Update student_type
        ]);

        return redirect()->route('students.index')->with('success', 'Student updated successfully.');
    }


    
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Student $student)
    {
        // Ensure the student being deleted belongs to the currently authenticated user
        if ($student->user_id !== Auth::id()) {
            return redirect()->route('students.index')->with('error', 'You are not authorized to delete this student.');
        }

        $student->delete();
        return redirect()->route('students.index')->with('success', 'Student deleted successfully.');
    }

    public function uploadCSV(Request $request)
    {
        // Validate the uploaded CSV file and associated fields
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt',
            'section_id' => 'required|exists:sections,id',
            'subject_id' => 'required|exists:subjects,id',
        ]);

        // Additional validation for student_type
        $csvData = file($request->file('csv_file')->getRealPath());
        $header = str_getcsv(array_shift($csvData)); // Get the header row
        $validStudentTypes = ['regular', 'irregular'];

        foreach ($csvData as $line) {
            $row = str_getcsv($line);
            $studentData = array_combine($header, $row);
            
            // Validate student_type
            if (!in_array($studentData['student_type'], $validStudentTypes)) {
                return redirect()->route('students.index')->with('error', 'Invalid student type in CSV. Allowed values are: ' . implode(', ', $validStudentTypes));
            }
        }

        // Log a message to confirm that the file upload passed validation
        Log::info('CSV file upload request validated successfully.');

        // Get the authenticated user ID
        $userId = Auth::id();

        // Try importing the CSV and catch any errors that may occur during the import
        try {
            // Import the CSV data using the StudentsImport class
            Excel::import(new StudentsImport($userId, $request->section_id, $request->subject_id), $request->file('csv_file'));

            // Log the success message after a successful import
            Log::info('CSV file processed successfully.');

            // Redirect back to the students index page with a success message
            return redirect()->route('students.index')->with('success', 'CSV uploaded and students imported successfully.');

        } catch (\Exception $e) {
            // Log the error message if an exception occurs
            Log::error('CSV import failed: ' . $e->getMessage());

            // Redirect back to the students index page with an error message
            return redirect()->route('students.index')->with('error', 'An error occurred while uploading the CSV. Please try again.');
        }
    }


    public function shuffleStudent(Request $request)
    {
        // Get the authenticated teacher ID
        $teacherId = auth()->user()->id;

        // Fetch sections and subjects for the dropdowns
        $sections = Section::where('user_id', $teacherId)->get();
        $subjects = Subject::where('user_id', $teacherId)->get();

        // Check if the request is a POST (form submission)
        if ($request->isMethod('post')) {
            $subjectId = $request->input('subject_id');
            $sectionId = $request->input('section_id');

            // Fetch students based on the selected subject and section
            $students = Student::where('user_id', $teacherId)
                ->when($subjectId, function ($query) use ($subjectId) {
                    return $query->where('subject_id', $subjectId);
                })
                ->when($sectionId, function ($query) use ($sectionId) {
                    return $query->where('section_id', $sectionId);
                })
                ->get();

            // Shuffle the students
            $shuffledStudents = $students->shuffle();

            // Return the view with the shuffled students and form options
            return view('student.shuffle_student', compact('sections', 'subjects', 'shuffledStudents'));
        }

        // If not a POST request, just show the form
        return view('student.shuffle_student', compact('sections', 'subjects'));
    }

    public function groupShuffle(Request $request)
    {
        // Get the authenticated teacher ID
        $teacherId = auth()->user()->id;

        // Fetch sections and subjects for the dropdowns
        $sections = Section::where('user_id', $teacherId)->get();
        $subjects = Subject::where('user_id', $teacherId)->get();

        // Check if the request is a POST (form submission)
        if ($request->isMethod('post')) {
            $subjectId = $request->input('subject_id');
            $sectionId = $request->input('section_id');
            $studentsPerGroup = $request->input('students_per_group');

            // Fetch students based on the selected subject and section
            $students = Student::where('user_id', $teacherId)
                ->when($subjectId, function ($query) use ($subjectId) {
                    return $query->where('subject_id', $subjectId);
                })
                ->when($sectionId, function ($query) use ($sectionId) {
                    return $query->where('section_id', $sectionId);
                })
                ->get();

            // Shuffle the students
            $shuffledStudents = $students->shuffle();

            // Create groups
            $groups = [];
            foreach ($shuffledStudents as $index => $student) {
                $groupIndex = floor($index / $studentsPerGroup); // Determine the group index
                if (!isset($groups[$groupIndex])) {
                    $groups[$groupIndex] = [];
                }
                $groups[$groupIndex][] = $student; // Add the student to the corresponding group
            }

            // Return the view with the groups and form options
            return view('student.group_shuffle', compact('sections', 'subjects', 'groups'));
        }

        // If not a POST request, just show the form
        return view('student.group_shuffle', compact('sections', 'subjects'));
    }

}
