@extends('layouts.app')

@section('title', 'Student Shuffling')

@section('content')
<div class="container mt-5">
    <h2 style="color: #E3A833;">Student Shuffling</h2>

    <!-- Display Validation Errors -->
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Recitation Shuffling Section -->
    <div class="card mb-4" style="background-color: #ffffff;">
        <div class="card-header" style="background-color: #E3A833; color: white;">
            <h4>Recitation Shuffling</h4>
        </div>
        <div class="card-body" style="background-color: #F6F9FF;">
            <form action="{{ route('students.shuffle') }}" method="POST">
                @csrf <!-- This is necessary for CSRF protection -->
                <div class="form-group">
                    <label for="subject">Select Subject:</label>
                    <select class="form-control" id="subject" name="subject_id">
                        <option value="">All Subjects</option>
                        @if(isset($subjects))
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <div class="form-group">
                    <label for="section">Select Section:</label>
                    <select class="form-control" id="section" name="section_id">
                        <option value="">All Sections</option>
                        @if(isset($sections))
                            @foreach($sections as $section)
                                <option value="{{ $section->id }}">{{ $section->name }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <button type="submit" class="btn btn-primary" style="background-color:#E3A833;border-color: #E3A833;">Shuffle Recitation</button>
            </form>

            <h5 class="mt-4">Shuffled Students:</h5>

            <!-- Table to display shuffled students -->
            <table class="table table-bordered mt-3" style="background-color: #ffffff;">
                <thead>
                    <tr>
                        <th>Count</th>
                        <th>Student number</th>
                        <th>Student Name</th>
                        <th>Course</th>
                        <th>Gender</th>
                        <th>Section</th>
                        <th>Subject</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($shuffledStudents) && $shuffledStudents->isNotEmpty())
                        @foreach($shuffledStudents as $student)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $student->student_number }}</td>
                                <td>{{ $student->last_name }}, {{ $student->first_name }} {{ $student->middle_name }}</td>
                                <td>{{ $student->course }}</td>
                                <td>{{ $student->gender }}</td>
                                <td>{{ $student->section->name }}</td>
                                <td>{{ $student->subject->name }}</td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="7">No students found.</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
