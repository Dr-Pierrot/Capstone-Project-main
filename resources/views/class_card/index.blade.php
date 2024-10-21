@extends('layouts.app')

@section('title', 'Class Card')

@section('content')
<style>
    .square-table td {
        width: 100px;
        height: 100px;
        vertical-align: middle; /* Aligns content vertically */
    }
    .table .hover:hover {
        background-color: wheat;
        cursor: pointer;
    }
</style>
    <div class="container mt-5">
        <!-- Filters Section -->
        <form action="{{ route('class-card.index') }}" method="GET">
            <div class="row mb-4">
                <!-- Student Filter -->
                <div class="col-md-3">
                    <label for="student_id">Select Student:</label>
                    <select name="student_id" id="student_id" class="form-control">
                        <option value="">All Students</option>
                        @foreach($students as $studentOption)
                            <option value="{{ $studentOption->id }}" {{ request('student_id') == $studentOption->id ? 'selected' : '' }}>
                                {{ $studentOption->first_name }} {{ $studentOption->middle_name }} {{ $studentOption->last_name }} 
                                ({{ $studentOption->section->name ?? '' }} - {{ $studentOption->subject->name ?? '' }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="subject_id">Select Subject:</label>
                    <select name="subject_id" id="subject_id" class="form-control" onchange="filterStudents()">
                        <option value="">All Subjects</option>
                        @if(isset($subjects))
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>
                                    {{ $subject->name }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="section_id">Select Section:</label>
                    <select name="section_id" id="section_id" class="form-control" onchange="filterStudents()">
                        <option value="">All Sections</option>
                        @if(isset($sections))
                            @foreach($sections as $section)
                                <option value="{{ $section->id }}" {{ request('section_id') == $section->id ? 'selected' : '' }}>
                                    {{ $section->name }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary btn-block">Filter</button>
                </div>
            </div>
        </form>

        <!-- Student Information Section -->
        @if(isset($message))
            <div class="card text-center mt-4">
                <div class="card-body">
                    <h5 class="card-title">No Students Found</h5>
                    <p class="card-text">{{ $message }}</p>
                </div>
            </div>
        @else
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card p-3">
                        <h5 class="mb-3">Student Information</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Name:</strong> {{ $student->first_name }} {{ $student->middle_name }} {{ $student->last_name }}</p>
                            </div>
                            <div class="col-md-3">
                                <p><strong>Gender:</strong> {{ $student->gender }}</p>
                            </div>
                            <div class="col-md-3">
                                <p><strong>Course:</strong> {{ $student->course }}</p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Section:</strong> {{ $student->section->name }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Subject:</strong> {{ $student->subject->name ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            
        <!-- Exam Type Dropdown -->
        <div class="row mb-4">
            <div class="col-md-3">
                <label for="exam_type">Select Exam Type:</label>
                <select id="exam_type" class="form-control" onchange="showExamTables()">
                    <option value="prelim">Prelim</option>
                    <option value="midterm">Midterm</option>
                    <option value="finals">Finals</option>
                    <option value="exams">Exam</option>
                    <option value="periodic">Periodic</option>
                    <option value="total">Total</option>
                </select>
            </div>
        </div>
            <!-- Class Card Section -->
            <div class="row mb-4" id="examTables">
                <div class="col-md-12">
                    <div class="card p-3">
                        <h5 class="mb-3">Class Card</h5>

                        <div id="prelim-tables" class="exam-tables col-md-12">
                            <div class="row">
                                <h3>Prelim</h3>
                                <div class="col-md-12">
                                    <!-- Performance Tasks for Prelim -->
                                    <h6>Performance Tasks</h6>
                                    <div class="row">
                                        <div class="col-10">
                                            <div style="overflow-x: auto;">
                                                <table class="table table-bordered text-center square-table" style="width: 100%;">
                                                    <tbody>
                                                        <tr>
                                                            <td class="hover" data-class-card-id="{{ $classCard->id }}" data-student-id="{{ $student->id }}" onclick="openPerformanceModal({{ $classCard->id }}, {{ $student->id }}, 1, 1)">Add</td>

                                                            @foreach ($scores->get('prelim')->where('type', 'performance_task') as $performance_task)
                                                                <td class="hover" id="prelim-{{ $performance_task->id }}" style="min-width: 120px;" onclick="openEditPerformanceModal({{ $performance_task->id }}, {{ $performance_task->score }}, {{ $performance_task->over_score }})">
                                                                PT {{ $performance_task->item }} <br>{{ $performance_task->score }} / {{ $performance_task->over_score }}
                                                                </td>
                                                            @endforeach
                                                            
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                        <div class="col-2">
                                            <table class="table table-bordered text-center square-table">
                                                <tbody>
                                                    <tr>
                                                        <td>
                                                            {{$totalScore->get('prelim')->where('type', 'performance_task')->sum('score')}} / {{$totalScore->get('prelim')->where('type', 'performance_task')->sum('over_score')}} <br>
                                                            <button class="btn btn-danger" onclick="openRemoveModal('performance_task', 'prelim')">Remove PT</button>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <!-- Quizzes for Prelim -->
                                    <h6>Quizzes</h6>
                                    <div class="row">
                                        <div class="col-10">
                                            <div style="overflow-x: auto;">
                                                <table class="table table-bordered text-center square-table" style="width: 100%;">
                                                    <tbody>
                                                        <tr>
                                                            <td class="hover" data-class-card-id="{{ $classCard->id }}" data-student-id="{{ $student->id }}" onclick="openPerformanceModal({{ $classCard->id }}, {{ $student->id }}, 1, 2)">Add</td>

                                                            @foreach ($scores->get('prelim')->where('type', 'quiz') as $quiz)
                                                                <td class="hover" id="prelim-{{ $quiz->id }}" style="min-width: 120px;" onclick="openEditPerformanceModal({{ $quiz->id }}, {{ $quiz->score }}, {{ $quiz->over_score }})">
                                                                Quiz {{ $quiz->item }} <br>{{ $quiz->score }} / {{ $quiz->over_score }}
                                                                </td>
                                                            @endforeach
                                                            
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                        <div class="col-2">
                                            <table class="table table-bordered text-center square-table">
                                                <tbody>
                                                    <tr>
                                                        <td>
                                                            {{$totalScore->get('prelim')->where('type', 'quiz')->sum('score')}} / {{$totalScore->get('prelim')->where('type', 'quiz')->sum('over_score')}} <br>
                                                            <button class="btn btn-danger" onclick="openRemoveModal('quiz', 'prelim')">Remove Quiz</button>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            
                                <div class="col-md-12">
                                    <!-- Recitation for Prelim -->
                                    <h6>Recitation</h6>
                                    <div class="row">
                                        <div class="col-10">
                                            <div style="overflow-x: auto;">
                                                <table class="table table-bordered text-center square-table" style="width: 100%;">
                                                    <tbody>
                                                        <tr>
                                                            <td class="hover" data-class-card-id="{{ $classCard->id }}" data-student-id="{{ $student->id }}" onclick="openPerformanceModal({{ $classCard->id }}, {{ $student->id }}, 1, 3)">Add</td>

                                                            @foreach ($scores->get('prelim')->where('type', 'recitation') as $recitation)
                                                                <td class="hover" id="prelim-{{ $recitation->id }}" style="min-width: 120px;" onclick="openEditPerformanceModal({{ $recitation->id }}, {{ $recitation->score }}, {{ $recitation->over_score }})">
                                                                Recitation {{ $recitation->item }} <br>{{ $recitation->score }} / {{ $recitation->over_score }}
                                                                </td>
                                                            @endforeach
                                                            
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                        <div class="col-2">
                                            <table class="table table-bordered text-center square-table">
                                                <tbody>
                                                    <tr>
                                                        <td>
                                                            {{$totalScore->get('prelim')->where('type', 'recitation')->sum('score')}} / {{$totalScore->get('prelim')->where('type', 'recitation')->sum('over_score')}} <br>
                                                            <button class="btn btn-danger" onclick="openRemoveModal('recitation', 'prelim')">Remove Recitation</button>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="midterm-tables" class="exam-tables col-md-12"  style="display: none;">
                            <div class="row">
                                <h3>Midterm</h3>
                                <div class="col-md-12">
                                    <!-- Performance Tasks for Midterm -->
                                    <h6>Performance Tasks</h6>
                                    <div class="row">
                                        <div class="col-10">
                                            <div style="overflow-x: auto;">
                                                <table class="table table-bordered text-center square-table" style="width: 100%;">
                                                    <tbody>
                                                        <tr>
                                                            <td class="hover" data-class-card-id="{{ $classCard->id }}" data-student-id="{{ $student->id }}" onclick="openPerformanceModal({{ $classCard->id }}, {{ $student->id }}, 2, 1)">Add</td>

                                                            @foreach ($scores->get('midterm')->where('type', 'performance_task') as $performance_task)
                                                                <td class="hover" id="midterm-{{ $performance_task->id }}" style="min-width: 120px;" onclick="openEditPerformanceModal({{ $performance_task->id }}, {{ $performance_task->score }}, {{ $performance_task->over_score }})">
                                                                PT {{ $performance_task->item }} <br>{{ $performance_task->score }} / {{ $performance_task->over_score }}
                                                                </td>
                                                            @endforeach
                                                            
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                        <div class="col-2">
                                            <table class="table table-bordered text-center square-table">
                                                <tbody>
                                                    <tr>
                                                        <td>
                                                            {{$totalScore->get('midterm')->where('type', 'performance_task')->sum('score')}} / {{$totalScore->get('midterm')->where('type', 'performance_task')->sum('over_score')}} <br>
                                                            <button class="btn btn-danger" onclick="openRemoveModal('performance_task', 'midterm')">Remove PT</button>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                        
                                <div class="col-md-12">
                                    <!-- Quizzes for Midterm -->
                                    <h6>Quizzes</h6>
                                    <div class="row">
                                        <div class="col-10">
                                            <div style="overflow-x: auto;">
                                                <table class="table table-bordered text-center square-table" style="width: 100%;">
                                                    <tbody>
                                                        <tr>
                                                            <td class="hover" data-class-card-id="{{ $classCard->id }}" data-student-id="{{ $student->id }}" onclick="openPerformanceModal({{ $classCard->id }}, {{ $student->id }}, 2, 2)">Add</td>

                                                            @foreach ($scores->get('midterm')->where('type', 'quiz') as $quiz)
                                                                <td class="hover" id="midterm-{{ $quiz->id }}" style="min-width: 120px;" onclick="openEditPerformanceModal({{ $quiz->id }}, {{ $quiz->score }}, {{ $quiz->over_score }})">
                                                                Quiz {{ $quiz->item }} <br>{{ $quiz->score }} / {{ $quiz->over_score }}
                                                                </td>
                                                            @endforeach
                                                            
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                        <div class="col-2">
                                            <table class="table table-bordered text-center square-table">
                                                <tbody>
                                                    <tr>
                                                        <td>
                                                            {{$totalScore->get('midterm')->where('type', 'quiz')->sum('score')}} / {{$totalScore->get('midterm')->where('type', 'quiz')->sum('over_score')}} <br>
                                                            <button class="btn btn-danger" onclick="openRemoveModal('quiz', 'midterm')">Remove PT</button>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-12">
                                    <!-- Recitation for Midterm -->
                                    <h6>Recitation</h6>
                                    <div class="row">
                                        <div class="col-10">
                                            <div style="overflow-x: auto;">
                                                <table class="table table-bordered text-center square-table" style="width: 100%;">
                                                    <tbody>
                                                        <tr>
                                                            <td class="hover" data-class-card-id="{{ $classCard->id }}" data-student-id="{{ $student->id }}" onclick="openPerformanceModal({{ $classCard->id }}, {{ $student->id }}, 2, 3)">Add</td>

                                                            @foreach ($scores->get('midterm')->where('type', 'recitation') as $recitation)
                                                                <td class="hover" id="midterm-{{ $recitation->id }}" style="min-width: 120px;" onclick="openEditPerformanceModal({{ $recitation->id }}, {{ $recitation->score }}, {{ $recitation->over_score }})">
                                                                Recitation {{ $recitation->item }} <br>{{ $recitation->score }} / {{ $recitation->over_score }}
                                                                </td>
                                                            @endforeach
                                                            
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                        <div class="col-2">
                                            <table class="table table-bordered text-center square-table">
                                                <tbody>
                                                    <tr>
                                                        <td>
                                                            {{$totalScore->get('midterm')->where('type', 'recitation')->sum('score')}} / {{$totalScore->get('midterm')->where('type', 'recitation')->sum('over_score')}} <br>
                                                            <button class="btn btn-danger" onclick="openRemoveModal('recitation', 'midterm')">Remove Recitation</button>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="finals-tables" class="exam-tables col-md-12" style="display: none;"> 
                            <div class="row">
                                <h3>Finals</h3>
                                <div class="col-md-12">
                                    <!-- Performance Tasks for Finals -->
                                    <h6>Performance Tasks</h6>
                                    <div class="row">
                                        <div class="col-10">
                                            <div style="overflow-x: auto;">
                                                <table class="table table-bordered text-center square-table" style="width: 100%;">
                                                    <tbody>
                                                        <tr>
                                                            <td class="hover" data-class-card-id="{{ $classCard->id }}" data-student-id="{{ $student->id }}" onclick="openPerformanceModal({{ $classCard->id }}, {{ $student->id }}, 3, 1)">Add</td>

                                                            @foreach ($scores->get('finals')->where('type', 'performance_task') as $performance_task)
                                                                <td class="hover" id="finals-{{ $performance_task->id }}" style="min-width: 120px;" onclick="openEditPerformanceModal({{ $performance_task->id }}, {{ $performance_task->score }}, {{ $performance_task->over_score }})">
                                                                PT {{ $performance_task->item }} <br>{{ $performance_task->score }} / {{ $performance_task->over_score }}
                                                                </td>
                                                            @endforeach
                                                            
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                        <div class="col-2">
                                            <table class="table table-bordered text-center square-table">
                                                <tbody>
                                                    <tr>
                                                        <td>
                                                            {{$totalScore->get('finals')->where('type', 'performance_task')->sum('score')}} / {{$totalScore->get('finals')->where('type', 'performance_task')->sum('over_score')}} <br>
                                                            <button class="btn btn-danger" onclick="openRemoveModal('performance_task', 'finals')">Remove PT</button>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <!-- Quizzes for Finals -->
                                    <h6>Quizzes</h6>
                                    <div class="row">
                                        <div class="col-10">
                                            <div style="overflow-x: auto;">
                                                <table class="table table-bordered text-center square-table" style="width: 100%;">
                                                    <tbody>
                                                        <tr>
                                                            <td class="hover" data-class-card-id="{{ $classCard->id }}" data-student-id="{{ $student->id }}" onclick="openPerformanceModal({{ $classCard->id }}, {{ $student->id }}, 3, 2)">Add</td>

                                                            @foreach ($scores->get('finals')->where('type', 'quiz') as $quiz)
                                                                <td class="hover" id="finals-{{ $quiz->id }}" style="min-width: 120px;" onclick="openEditPerformanceModal({{ $quiz->id }}, {{ $quiz->score }}, {{ $quiz->over_score }})">
                                                                Quiz {{ $quiz->item }} <br>{{ $quiz->score }} / {{ $quiz->over_score }}
                                                                </td>
                                                            @endforeach
                                                            
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                        <div class="col-2">
                                            <table class="table table-bordered text-center square-table">
                                                <tbody>
                                                    <tr>
                                                        <td>
                                                            {{$totalScore->get('finals')->where('type', 'quiz')->sum('score')}} / {{$totalScore->get('finals')->where('type', 'quiz')->sum('over_score')}} <br>
                                                            <button class="btn btn-danger" onclick="openRemoveModal('quiz', 'finals')">Remove Quiz</button>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                        
                                <div class="col-md-12">
                                    <!-- Recitation for Finals -->
                                    <h6>Recitation</h6>
                                    <div class="row">
                                        <div class="col-10">
                                            <div style="overflow-x: auto;">
                                                <table class="table table-bordered text-center square-table" style="width: 100%;">
                                                    <tbody>
                                                        <tr>
                                                            <td class="hover" data-class-card-id="{{ $classCard->id }}" data-student-id="{{ $student->id }}" onclick="openPerformanceModal({{ $classCard->id }}, {{ $student->id }}, 3, 3)">Add</td>

                                                            @foreach ($scores->get('finals')->where('type', 'recitation') as $recitation)
                                                                <td class="hover" id="finals-{{ $recitation->id }}" style="min-width: 120px;" onclick="openEditPerformanceModal({{ $recitation->id }}, {{ $recitation->score }}, {{ $recitation->over_score }})">
                                                                Recitation {{ $recitation->item }} <br>{{ $recitation->score }} / {{ $recitation->over_score }}
                                                                </td>
                                                            @endforeach
                                                            
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                        <div class="col-2">
                                            <table class="table table-bordered text-center square-table">
                                                <tbody>
                                                    <tr>
                                                        <td>
                                                            {{$totalScore->get('finals')->where('type', 'recitation')->sum('score')}} / {{$totalScore->get('finals')->where('type', 'recitation')->sum('over_score')}} <br>
                                                            <button class="btn btn-danger" onclick="openRemoveModal('recitation', 'finals')">Remove Recitation</button>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div id="exam-tables" class="exam-tables col-md-12"  style="display: none;">
                            <div class="row">
                                <h3>Major Exams</h3>
                                <div class="col-md-4">
                                    <!-- Performance Tasks for Midterm -->
                                    <h6>Prelim</h6>
                                    <table class="table table-bordered text-center square-table">
                                        <thead>
                                            <tr>
                                                <th colspan="5">Prelim</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>Lecture</td>
                                                @foreach ($scores->get('prelim')->where('type', 'lec') as $exam_prelim)
                                                    <td class="hover" id="prelim-{{ $exam_prelim->id }}" style="min-width: 120px;" onclick="openEditPerformanceModal({{ $exam_prelim->id }}, {{ $exam_prelim->score }}, {{ $exam_prelim->over_score }})">
                                                        {{ $exam_prelim->score }} / {{ $exam_prelim->over_score }}
                                                    </td>
                                                @endforeach
                                                @for ($i = $scores->get('prelim')->where('type', 'lec')->count(); $i < 1; $i++)
                                                    <td class="hover" data-class-card-id="{{ $classCard->id }}" data-student-id="{{ $student->id }}" onclick="openPerformanceModal({{ $classCard->id }}, {{ $student->id }}, 1, 4)">Add</td>
                                                @endfor
                                               
                                            </tr>
                                            <tr>
                                                <td>Lab</td>
                                                @foreach ($scores->get('prelim')->where('type', 'lab') as $exam_prelim)
                                                    <td class="hover" id="prelim-{{ $exam_prelim->id }}" style="min-width: 120px;" onclick="openEditPerformanceModal({{ $exam_prelim->id }}, {{ $exam_prelim->score }}, {{ $exam_prelim->over_score }})">
                                                        {{ $exam_prelim->score }} / {{ $exam_prelim->over_score }}
                                                    </td>
                                                @endforeach
                                                @for ($i = $scores->get('prelim')->where('type', 'lab')->count(); $i < 1; $i++)
                                                    <td class="hover" data-class-card-id="{{ $classCard->id }}" data-student-id="{{ $student->id }}" onclick="openPerformanceModal({{ $classCard->id }}, {{ $student->id }}, 1, 5)">Add</td>
                                                @endfor

                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                        
                                <div class="col-md-4">
                                    <!-- Quizzes for Midterm -->
                                    <h6>Midterm</h6>
                                    <table class="table table-bordered text-center square-table">
                                        <thead>
                                            <tr>
                                                <th colspan="5">Midterm</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>Lecture</td>
                                                @foreach ($scores->get('midterm')->where('type', 'lec') as $exam_midterm)
                                                    <td class="hover" id="midterm-{{ $exam_midterm->id }}" style="min-width: 120px;" onclick="openEditPerformanceModal({{ $exam_midterm->id }}, {{ $exam_midterm->score }}, {{ $exam_midterm->over_score }})">
                                                        {{ $exam_midterm->score }} / {{ $exam_midterm->over_score }}
                                                    </td>
                                                @endforeach
                                                @for ($i = $scores->get('midterm')->where('type', 'lec')->count(); $i < 1; $i++)
                                                    <td class="hover" data-class-card-id="{{ $classCard->id }}" data-student-id="{{ $student->id }}" onclick="openPerformanceModal({{ $classCard->id }}, {{ $student->id }}, 2, 4)">Add</td>
                                                @endfor
                                               
                                            </tr>
                                            <tr>
                                                <td>Lab</td>
                                                @foreach ($scores->get('midterm')->where('type', 'lab') as $exam_midterm)
                                                    <td class="hover" id="midterm-{{ $exam_midterm->id }}" style="min-width: 120px;" onclick="openEditPerformanceModal({{ $exam_midterm->id }}, {{ $exam_midterm->score }}, {{ $exam_midterm->over_score }})">
                                                        {{ $exam_midterm->score }} / {{ $exam_midterm->over_score }}
                                                    </td>
                                                @endforeach
                                                @for ($i = $scores->get('midterm')->where('type', 'lab')->count(); $i < 1; $i++)
                                                    <td class="hover" data-class-card-id="{{ $classCard->id }}" data-student-id="{{ $student->id }}" onclick="openPerformanceModal({{ $classCard->id }}, {{ $student->id }}, 2, 5)">Add</td>
                                                @endfor

                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                
                                <div class="col-md-4">
                                    <!-- Recitation for Midterm -->
                                    <h6>Finals</h6>
                                    <table class="table table-bordered text-center square-table">
                                        <thead>
                                            <tr>
                                                <th colspan="5">Finals</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>Lecture</td>
                                                @foreach ($scores->get('finals')->where('type', 'lec') as $exam_finals)
                                                    <td class="hover" id="finals-{{ $exam_finals->id }}" style="min-width: 120px;" onclick="openEditPerformanceModal({{ $exam_finals->id }}, {{ $exam_finals->score }}, {{ $exam_finals->over_score }})">
                                                        {{ $exam_finals->score }} / {{ $exam_finals->over_score }}
                                                    </td>
                                                @endforeach
                                                @for ($i = $scores->get('finals')->where('type', 'lec')->count(); $i < 1; $i++)
                                                    <td class="hover" data-class-card-id="{{ $classCard->id }}" data-student-id="{{ $student->id }}" onclick="openPerformanceModal({{ $classCard->id }}, {{ $student->id }}, 3, 4)">Add</td>
                                                @endfor
                                               
                                            </tr>
                                            <tr>
                                                <td>Lab</td>
                                                @foreach ($scores->get('finals')->where('type', 'lab') as $exam_finals)
                                                    <td class="hover" id="finals-{{ $exam_finals->id }}" style="min-width: 120px;" onclick="openEditPerformanceModal({{ $exam_finals->id }}, {{ $exam_finals->score }}, {{ $exam_finals->over_score }})">
                                                        {{ $exam_finals->score }} / {{ $exam_finals->over_score }}
                                                    </td>
                                                @endforeach
                                                @for ($i = $scores->get('finals')->where('type', 'lab')->count(); $i < 1; $i++)
                                                    <td class="hover" data-class-card-id="{{ $classCard->id }}" data-student-id="{{ $student->id }}" onclick="openPerformanceModal({{ $classCard->id }}, {{ $student->id }}, 3, 5)">Add</td>
                                                @endfor

                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                        <div id="periodic-tables" class="exam-tables col-md-12"  style="display: none;">
                            <div class="row">
                                <h3>Periodic Grading</h3>
                                <div class="col-md-4">
                                    <!-- Performance Tasks for Midterm -->
                                    <table class="table table-bordered text-center square-table">
                                        <thead>
                                            <tr>
                                                <th colspan="5">Prelim</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>Class Standing</td>
                                                <td>
                                                    @php
                                                    $prelimCSweights = [
                                                            'performance_task' => 40, // 40%
                                                            'quiz' => 30,             // 30%
                                                            'recitation' => 30        // 30%
                                                        ];

                                                        
                                                        $prelimPT = $totalScore->get('prelim')->where('type', 'performance_task')->sum('score');
                                                        $prelimPTOver = $totalScore->get('prelim')->where('type', 'performance_task')->sum('over_score');
                                                        if($prelimPTOver == 0){
                                                            $prelimPTGrade = 60 * $prelimCSweights['performance_task'] / 100;
                                                        }else{
                                                            $prelimPTGrade = (($prelimPT / $prelimPTOver) * 40 + 60) * $prelimCSweights['performance_task'] / 100;                                                            
                                                        }

                                                        $prelimQuiz = $totalScore->get('prelim')->where('type', 'quiz')->sum('score');
                                                        $prelimQuizOver = $totalScore->get('prelim')->where('type', 'quiz')->sum('over_score');
                                                        if($prelimQuizOver == 0){
                                                            $prelimQuizGrade = 60 * $prelimCSweights['quiz'] / 100;
                                                        }else{
                                                            $prelimQuizGrade = (($prelimQuiz / $prelimQuizOver) * 40 + 60) * $prelimCSweights['quiz'] / 100;                                                            
                                                        }

                                                        $prelimRecitation = $totalScore->get('prelim')->where('type', 'recitation')->sum('score');
                                                        $prelimRecitationOver = $totalScore->get('prelim')->where('type', 'recitation')->sum('over_score');
                                                        if($prelimRecitationOver == 0){
                                                            $prelimRecitationGrade = 60 * $prelimCSweights['recitation'] / 100;
                                                        }else{
                                                            $prelimRecitationGrade = (($prelimRecitation / $prelimRecitationOver) * 40 + 60) * $prelimCSweights['recitation'] / 100;                                                            
                                                        }

                                                        $prelimGradeCS = number_format($prelimPTGrade + $prelimQuizGrade + $prelimRecitationGrade, 2);
                                                        
                                                    @endphp
                                                    {{
                                                         $prelimGradeCS;
                                                    }} 
                                            </tr>
                                            <tr>
                                                <td>Exam</td>
                                                <td>
                                                    @php
                                                        $prelimExamweights = [
                                                            'lec' => 50, // 50%
                                                            'lab' => 50, // 50%
                                                        ];
                                                            
                                                        // Calculate the weighted scores using the over_score for normalization
                                                        $prelimLec = $totalScore->get('prelim')->where('type', 'lec')->sum('score');
                                                        $prelimLecOver = $totalScore->get('prelim')->where('type', 'lec')->sum('over_score');
                                                        if($prelimLecOver == 0){
                                                            $prelimLecGrade = 60 * $prelimExamweights['lec'] / 100;
                                                        }else{
                                                            $prelimLecGrade = (($prelimLec / $prelimLecOver) * 40 + 60) * $prelimExamweights['lec'] / 100;
                                                        }

                                                        $prelimLab = $totalScore->get('prelim')->where('type', 'lab')->sum('score');
                                                        $prelimLabOVer = $totalScore->get('prelim')->where('type', 'lab')->sum('over_score');
                                                        if($prelimLabOVer == 0){
                                                            $prelimLabGrade = 60 * $prelimExamweights['lab'] / 100;
                                                        }else{
                                                            $prelimLabGrade = (($prelimLab / $prelimLabOVer) * 40 + 60) * $prelimExamweights['lab'] / 100;
                                                        }

                                                        $prelimGradeExam = number_format($prelimLecGrade + $prelimLabGrade, 2);
                                                    @endphp
                                                    {{
                                                        $prelimGradeExam;
                                                    }} 
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Grade</td>
                                                <td>
                                                    {{ number_format(($prelimGradeCS * 0.6) + ($prelimGradeExam * 0.4), 2) }}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                        
                                <div class="col-md-4">
                                    <!-- Quizzes for Midterm -->
                                    <table class="table table-bordered text-center square-table">
                                        <thead>
                                            <tr>
                                                <th colspan="5">Midterm</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>Class Standing</td>
                                                <td>
                                                    @php
                                                    $midtermCSweights = [
                                                            'performance_task' => 40, // 40%
                                                            'quiz' => 30,             // 30%
                                                            'recitation' => 30        // 30%
                                                        ];

                                                        
                                                        $midtermPT = $totalScore->get('midterm')->where('type', 'performance_task')->sum('score');
                                                        $midtermPTOver = $totalScore->get('midterm')->where('type', 'performance_task')->sum('over_score');
                                                        if($midtermPTOver == 0){
                                                            $midtermPTGrade = 60 * $midtermCSweights['performance_task'] / 100;
                                                        }else{
                                                            $midtermPTGrade = (($midtermPT / $midtermPTOver) * 40 + 60) * $midtermCSweights['performance_task'] / 100;                                                            
                                                        }

                                                        $midtermQuiz = $totalScore->get('midterm')->where('type', 'quiz')->sum('score');
                                                        $midtermQuizOver = $totalScore->get('midterm')->where('type', 'quiz')->sum('over_score');
                                                        if($midtermQuizOver == 0){
                                                            $midtermQuizGrade = 60 * $midtermCSweights['quiz'] / 100;
                                                        }else{
                                                            $midtermQuizGrade = (($midtermQuiz / $midtermQuizOver) * 40 + 60) * $midtermCSweights['quiz'] / 100;                                                            
                                                        }

                                                        $midtermRecitation = $totalScore->get('midterm')->where('type', 'recitation')->sum('score');
                                                        $midtermRecitationOver = $totalScore->get('midterm')->where('type', 'recitation')->sum('over_score');
                                                        if($midtermRecitationOver == 0){
                                                            $midtermRecitationGrade = 60 * $midtermCSweights['recitation'] / 100;
                                                        }else{
                                                            $midtermRecitationGrade = (($midtermRecitation / $midtermRecitationOver) * 40 + 60) * $midtermCSweights['recitation'] / 100;                                                            
                                                        }

                                                        $midtermGradeCS = number_format($midtermPTGrade + $midtermQuizGrade + $midtermRecitationGrade, 2);
                                                        
                                                    @endphp
                                                    {{
                                                         $midtermGradeCS;
                                                    }} 
                                            </tr>
                                            <tr>
                                                <td>Exam</td>
                                                <td>
                                                    @php
                                                        $midtermExamweights = [
                                                            'lec' => 50, // 50%
                                                            'lab' => 50, // 50%
                                                        ];
                                                            
                                                        // Calculate the weighted scores using the over_score for normalization
                                                        $midtermLec = $totalScore->get('midterm')->where('type', 'lec')->sum('score');
                                                        $midtermLecOver = $totalScore->get('midterm')->where('type', 'lec')->sum('over_score');
                                                        if($midtermLecOver == 0){
                                                            $midtermLecGrade = 60 * $midtermExamweights['lec'] / 100;
                                                        }else{
                                                            $midtermLecGrade = (($midtermLec / $midtermLecOver) * 40 + 60) * $midtermExamweights['lec'] / 100;
                                                        }

                                                        $midtermLab = $totalScore->get('midterm')->where('type', 'lab')->sum('score');
                                                        $midtermLabOVer = $totalScore->get('midterm')->where('type', 'lab')->sum('over_score');
                                                        if($midtermLabOVer == 0){
                                                            $midtermLabGrade = 60 * $midtermExamweights['lab'] / 100;
                                                        }else{
                                                            $midtermLabGrade = (($midtermLab / $midtermLabOVer) * 40 + 60) * $midtermExamweights['lab'] / 100;
                                                        }

                                                        $midtermGradeExam = number_format($midtermLecGrade + $midtermLabGrade, 2);
                                                    @endphp
                                                    {{
                                                        $midtermGradeExam;
                                                    }} 
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Grade</td>
                                                <td>
                                                    {{ number_format(($midtermGradeCS * 0.6) + ($midtermGradeExam * 0.4), 2) }}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                
                                <div class="col-md-4">
                                    <!-- Recitation for Midterm -->
                                    <table class="table table-bordered text-center square-table">
                                        <thead>
                                            <tr>
                                                <th colspan="5">Finals</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>Class Standing</td>
                                                <td>
                                                    @php
                                                    $finalsCSweights = [
                                                            'performance_task' => 40, // 40%
                                                            'quiz' => 30,             // 30%
                                                            'recitation' => 30        // 30%
                                                        ];

                                                        
                                                        $finalsPT = $totalScore->get('finals')->where('type', 'performance_task')->sum('score');
                                                        $finalsPTOver = $totalScore->get('finals')->where('type', 'performance_task')->sum('over_score');
                                                        if($finalsPTOver == 0){
                                                            $finalsPTGrade = 60 * $finalsCSweights['performance_task'] / 100;
                                                        }else{
                                                            $finalsPTGrade = (($finalsPT / $finalsPTOver) * 40 + 60) * $finalsCSweights['performance_task'] / 100;                                                            
                                                        }

                                                        $finalsQuiz = $totalScore->get('finals')->where('type', 'quiz')->sum('score');
                                                        $finalsQuizOver = $totalScore->get('finals')->where('type', 'quiz')->sum('over_score');
                                                        if($finalsQuizOver == 0){
                                                            $finalsQuizGrade = 60 * $finalsCSweights['quiz'] / 100;
                                                        }else{
                                                            $finalsQuizGrade = (($finalsQuiz / $finalsQuizOver) * 40 + 60) * $finalsCSweights['quiz'] / 100;                                                            
                                                        }

                                                        $finalsRecitation = $totalScore->get('finals')->where('type', 'recitation')->sum('score');
                                                        $finalsRecitationOver = $totalScore->get('finals')->where('type', 'recitation')->sum('over_score');
                                                        if($finalsRecitationOver == 0){
                                                            $finalsRecitationGrade = 60 * $finalsCSweights['recitation'] / 100;
                                                        }else{
                                                            $finalsRecitationGrade = (($finalsRecitation / $finalsRecitationOver) * 40 + 60) * $finalsCSweights['recitation'] / 100;                                                            
                                                        }

                                                        $finalsGradeCS = number_format($finalsPTGrade + $finalsQuizGrade + $finalsRecitationGrade, 2);
                                                        
                                                    @endphp
                                                    {{
                                                         $finalsGradeCS;
                                                    }} 
                                            </tr>
                                            <tr>
                                                <td>Exam</td>
                                                <td>
                                                    @php
                                                        $finalsExamweights = [
                                                            'lec' => 50, // 50%
                                                            'lab' => 50, // 50%
                                                        ];
                                                            
                                                        // Calculate the weighted scores using the over_score for normalization
                                                        $finalsLec = $totalScore->get('finals')->where('type', 'lec')->sum('score');
                                                        $finalsLecOver = $totalScore->get('finals')->where('type', 'lec')->sum('over_score');
                                                        if($finalsLecOver == 0){
                                                            $finalsLecGrade = 60 * $finalsExamweights['lec'] / 100;
                                                        }else{
                                                            $finalsLecGrade = (($finalsLec / $finalsLecOver) * 40 + 60) * $finalsExamweights['lec'] / 100;
                                                        }

                                                        $finalsLab = $totalScore->get('finals')->where('type', 'lab')->sum('score');
                                                        $finalsLabOVer = $totalScore->get('finals')->where('type', 'lab')->sum('over_score');
                                                        if($finalsLabOVer == 0){
                                                            $finalsLabGrade = 60 * $finalsExamweights['lab'] / 100;
                                                        }else{
                                                            $finalsLabGrade = (($finalsLab / $finalsLabOVer) * 40 + 60) * $finalsExamweights['lab'] / 100;
                                                        }

                                                        $finalsGradeExam = number_format($finalsLecGrade + $finalsLabGrade, 2);
                                                    @endphp
                                                    {{
                                                        $finalsGradeExam;
                                                    }} 
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Grade</td>
                                                <td>
                                                    {{ number_format(($finalsGradeCS * 0.6) + ($finalsGradeExam * 0.4), 2) }}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div id="total-tables" class="exam-tables col-md-12"  style="display: none;">
                            <div class="row">
                                <div class="col-md-12">
                                    <!-- Performance Tasks for Midterm -->
                                    <table class="table table-bordered text-center square-table">
                                        <thead>
                                            <tr>
                                                <th colspan="5">Final Grade</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td style="font-size: 55px;">
                                                    @php 
                                                        $totalPrelim = ($prelimGradeCS * 0.6) + ($prelimGradeExam * 0.4);
                                                        $totalMidterm = ($midtermGradeCS * 0.6) + ($midtermGradeExam * 0.4);
                                                        $totalFinals = ($finalsGradeCS * 0.6) + ($finalsGradeExam * 0.4);
                                                        $total = ($totalPrelim * 0.3 ) + ($totalMidterm * 0.3) + ($totalFinals * 0.4);
                                                    @endphp
                                                    {{ number_format($total, 2) }}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Navigation Arrows -->
                <div class="row mt-4 text-center">
                    <div class="col">
                        <a href="{{ route('class-card.index', ['student_id' => $prevStudentId]) }}" class="btn btn-secondary">&lt;</a>
                        <a href="{{ route('class-card.index', ['student_id' => $nextStudentId]) }}" class="btn btn-secondary">&gt;</a>
                    </div>
                </div>
            </div>

        @endif
    </div>



    <!-- Performance Task Modal -->
    <div class="modal fade" id="performanceModal" tabindex="-1" aria-labelledby="performanceModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="performanceModalLabel">Add Score</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="performanceForm" method="POST" action="{{ route('class-card.performance-task.store') }}">
                        @csrf
                        <input type="hidden" id="class_card_id" name="class_card_id">
                        <input type="hidden" id="student_id_performance" name="student_id">
                        <input type="hidden" id="term" name="term"> <!-- Use a value for term: 1 for prelim, 2 for midterm -->
                        <input type="hidden" id="type_activity" name="type_activity"> <!-- Use a value for type of activity: 1 for performance task, 2 for quiz, 3 recitation -->
                        <div class="mb-3">
                            <label for="performanceScore" class="form-label">Score</label>
                            <input type="number" class="form-control" id="performanceScore" placeholder="Enter score" name="score" required>
                        </div>
                        <div class="mb-3">
                            <label for="over" class="form-label">Over Score</label>
                            <input type="number" class="form-control" id="over" placeholder="Over score" name="over_score" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Save Score</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editperformanceModal" tabindex="-1" aria-labelledby="editperformanceModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editperformanceModalLabel">Edit Score</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editperformanceForm" method="POST">
                        @csrf
                        <!-- Spoof the PUT method -->
                        @method('PUT')
                        <div class="mb-3">
                            <label for="edit_performanceScore" class="form-label">Score</label>
                            <input type="number" class="form-control" id="edit_performanceScore" placeholder="Enter score" name="score" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_performanceOverScore" class="form-label">Over Score</label>
                            <input type="number" class="form-control" id="edit_performanceOverScore" placeholder="Enter over score" name="over_score" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Save Score</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="removeTaskModal" tabindex="-1" aria-labelledby="removeTaskModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="removeTaskModalLabel">Remove Task</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered" id="taskTable">
                        <tbody id="taskTableBody">
                            <!-- Dynamic content will be populated here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

<script>
    function openRemoveModal(taskType, term) {
        const taskTableBody = document.querySelector('#taskTableBody'); // Reference the tbody directly
        taskTableBody.innerHTML = ''; // Clear existing content

        let title;
        let tasks;

        // Fetch tasks based on taskType
        if(term === 'prelim'){
            if (taskType === 'performance_task') {
                title = 'Remove Performance Task';
                tasks = @json($scores->get('prelim')->where('type', 'performance_task'));
            } else if (taskType === 'quiz') {
                title = 'Remove Quiz';
                tasks = @json($scores->get('prelim')->where('type', 'quiz'));
            } else if (taskType === 'recitation') {
                title = 'Remove Recitation';
                tasks = @json($scores->get('prelim')->where('type', 'recitation'));
            }
        }else if(term === 'midterm'){
            if (taskType === 'performance_task') {
                title = 'Remove Performance Task';
                tasks = @json($scores->get('midterm')->where('type', 'performance_task'));
            } else if (taskType === 'quiz') {
                title = 'Remove Quiz';
                tasks = @json($scores->get('midterm')->where('type', 'quiz'));
            } else if (taskType === 'recitation') {
                title = 'Remove Recitation';
                tasks = @json($scores->get('midterm')->where('type', 'recitation'));
            }
        }else if(term === 'finals'){
            if (taskType === 'performance_task') {
                title = 'Remove Performance Task';
                tasks = @json($scores->get('finals')->where('type', 'performance_task'));
            } else if (taskType === 'quiz') {
                title = 'Remove Quiz';
                tasks = @json($scores->get('finals')->where('type', 'quiz'));
            } else if (taskType === 'recitation') {
                title = 'Remove Recitation';
                tasks = @json($scores->get('finals')->where('type', 'recitation'));
            }
        }
        

        document.getElementById('removeTaskModalLabel').textContent = title;

        // Populate the table rows dynamically using a normal for loop
        for (let key in tasks) {
            if (tasks.hasOwnProperty(key)) {
                const task = tasks[key];
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td class="text-center">${taskType.charAt(0).toUpperCase() + taskType.slice(1)} ${task.item}</td>
                    <td class="text-center">
                        <button class="btn btn-danger" onclick="deletePerformance(${taskType === 'performance_task' ? 1 : (taskType === 'quiz' ? 2 : 3)}, 1, '${task.item}', '{{ csrf_token() }}')">Remove</button>
                    </td>
                `;
                taskTableBody.appendChild(row);
            }
        }

        // Show the modal
        $('#removeTaskModal').modal('show');
    }
</script>

    <script src="{{ asset('js/class-card.js') }}"></script>
    <script>
        function showExamTables() {
            const examType = document.getElementById('exam_type').value;
            const examTables = document.querySelectorAll('.exam-tables');
            
            examTables.forEach(table => {
                table.style.display = 'none'; // Hide all tables
            });
    
            if (examType === 'prelim') {
                document.getElementById('prelim-tables').style.display = 'block'; // Show prelim tables
            } else if (examType === 'midterm') {
                document.getElementById('midterm-tables').style.display = 'block'; // Show midterm tables
            } else if (examType === 'finals') {
                document.getElementById('finals-tables').style.display = 'block'; // Show finals tables
            } else if (examType === 'exams') {
                document.getElementById('exam-tables').style.display = 'block'; // Show finals tables
            } else if (examType === 'periodic') {
                document.getElementById('periodic-tables').style.display = 'block'; // Show finals tables
            } else if (examType === 'total') {
                document.getElementById('total-tables').style.display = 'block'; // Show finals tables
            }
        }
       
        // function openPerformanceModal(classCardId, studentId, term, type_activity) {
        //     // Ensure these IDs are set correctly
        //     $('#class_card_id').val(classCardId);
        //     $('#student_id_performance').val(studentId); // Populate the student_id
        //     console.log(studentId);
        //     $('#term').val(term); // Set the term input field value
        //     $('#type_activity').val(type_activity)
        //     $('#performanceModal').modal('show');
        // }

        
    </script>
@endsection
