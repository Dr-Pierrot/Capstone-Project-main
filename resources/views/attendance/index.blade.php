@extends('layouts.app')

@section('title', 'Attendance')

@section('content')
<div class="container mt-5">

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

        <!-- Attendance Type Selection Dropdown -->
        <div class="form-group mb-4">
            <label for="attendanceType">Select Attendance Type:</label>
            <select id="attendanceType" class="form-control">
                <option value="both">Both</option>
                <option value="lecture">Lecture Only</option>
                <option value="laboratory">Laboratory Only</option>
            </select>
        </div>

        <!-- Lecture Attendance Table -->
        <div id="lectureAttendance" class="attendance-table">
            <h4>Lecture Attendance</h4>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Days</th>
                        @for ($i = 1; $i <= 18; $i++)
                            <th>{{ $i }}</th>
                        @endfor
                    </tr>
                </thead>
                <tbody>
                    @foreach (['Mon' => 1, 'Tue' => 2, 'Wed' => 3, 'Thu' => 4, 'Fri' => 5, 'Sat' => 6] as $day => $dayNum)
                        <tr>
                            <td>{{ $day }}</td>
                            @for ($i = 1; $i <= 18; $i++)
                                @php
                                    // Check for existing attendance record for this day and date
                                    $attendance = $attendanceRecords
                                        ->where('day', $dayNum)
                                        ->where('type', 1)
                                        ->where('attendance_date', $i)
                                        ->first();
                                @endphp
                                <td id="lec-{{ $day }}-{{ $i }}" 
                                    data-day="{{ $dayNum }}" 
                                    data-type="1" 
                                    data-attendance-date="{{ $i }}" 
                                    onclick="checkAttendance(1, {{ $dayNum }}, {{ $i }}, {{ $attendance ? ($attendance->status == 1 ? 2 : 1) : 1}})"
                                    style="background-color: {{$attendance ? ($attendance->status == 1 ? 'Blue' : 'Red') : ''}};">
                                </td>
                            @endfor
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Laboratory Attendance Table -->
        <div id="laboratoryAttendance" class="attendance-table" style="display: none;">
            <h4>Laboratory Attendance</h4>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Days</th>
                        @for ($i = 1; $i <= 18; $i++)
                            <th>{{ $i }}</th>
                        @endfor
                    </tr>
                </thead>
                <tbody>
                    @foreach (['Mon' => 1, 'Tue' => 2, 'Wed' => 3, 'Thu' => 4, 'Fri' => 5, 'Sat' => 6] as $day1 => $dayNumLab)
                        <tr>
                            <td>{{ $day1 }}</td>
                            @for ($j = 1; $j <= 18; $j++)
                                @php
                                    // Check for existing attendance record for laboratory
                                    $attendance_lab = $labAttendanceRecords
                                        ->Where('day', $dayNumLab)
                                        ->Where('type', 2)
                                        ->firstWhere('attendance_date', $j);
                                @endphp
                                <td id="lab-{{ $day }}-{{ $j }}" 
                                    data-day="{{ $dayNum }}" 
                                    data-type="2" 
                                    data-attendance-date="{{ $j }}" 
                                    onclick="checkAttendance(2, {{ $dayNumLab }}, {{ $j }}, {{ $attendance_lab ? ($attendance_lab->status == 1 ? 2 : 1) : 1}})"
                                    style="background-color: {{$attendance_lab ? ($attendance_lab->status == 1 ? 'Blue' : 'Red') : ''}};">
                                </td>
                            @endfor
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    @endif

    <!-- Navigation Arrows -->
    <div class="row mt-4 text-center">
        <div class="col">
            <a href="{{ route('attendance.index', ['student_id' => $prevStudentId]) }}" class="btn btn-secondary">&lt;</a>
            <a href="{{ route('attendance.index', ['student_id' => $nextStudentId]) }}" class="btn btn-secondary">&gt;</a>
        </div>
    </div>
</div>

<script>
    // Pass PHP variables into JavaScript
    const studentId = {{ $student->id }};
    const subjectId = {{ $student->subject_id }};
    const sectionId = {{ $student->section_id }};

    // JavaScript to handle the dropdown change event
    document.getElementById('attendanceType').addEventListener('change', function() {
        const selectedType = this.value;
        
        // Hide all attendance tables initially
        document.querySelectorAll('.attendance-table').forEach(table => {
            table.style.display = 'none';
        });
        
        // Show the selected attendance table(s)
        if (selectedType === 'lecture') {
            document.getElementById('lectureAttendance').style.display = 'block';
        } else if (selectedType === 'laboratory') {
            document.getElementById('laboratoryAttendance').style.display = 'block';
        } else {
            document.getElementById('lectureAttendance').style.display = 'block';
            document.getElementById('laboratoryAttendance').style.display = 'block';
        }
    });
    
    // Trigger change event on page load to set the default view
    document.getElementById('attendanceType').dispatchEvent(new Event('change'));
</script>

<!-- <script>
    document.querySelectorAll('.attendance-btn').forEach(button => {
        button.addEventListener('click', function() {
            const day = this.getAttribute('data-day');
            const type = this.getAttribute('data-type');
            const status = this.getAttribute('data-status'); // 1 = Present, 2 = Absent
            const date = this.getAttribute('data-attendance-date'); // Get the attendance date

            fetch('{{ route('attendance.store') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    student_id: studentId,  // Use the JS variable passed from PHP
                    subject_id: subjectId,  // Use the JS variable passed from PHP
                    section_id: sectionId,  // Use the JS variable passed from PHP
                    day: day,
                    type: type,
                    status: status,
                    date: date
                })
            })
            .then(response => response.json())
            .then(data => {
                console.log(data); // Handle success response
                location.reload(); // Reload the page to see the updated attendance
            })
            .catch(error => {
                console.error('Error:', error); // Handle error response
            });
        });
    });
</script> -->

<script>
    function checkAttendance(type, day, attendanceDate, status) {
    const studentId = {{ $student->id }};
    const subjectId = {{ $student->subject_id }};
    const sectionId = {{ $student->section_id }};

    // Fetch request to store attendance
    fetch('{{ route('attendance.store') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                student_id: studentId,
                subject_id: subjectId,
                section_id: sectionId,
                day: day,
                type: type,
                status: status,
                attendance_date: attendanceDate // Match this with the back-end
            })
        })
        .then(response => response.json())
        .then(data => {
            console.log(data); // Handle success response
            if (data.success) {
                location.reload(); // Reload the page to see the updated attendance
            } else {
                alert(data.error); // Handle any errors returned from the server
            }
        })
        .catch(error => {
            console.error('Error:', error); // Handle error response
        });
    }

</script>
@endsection
