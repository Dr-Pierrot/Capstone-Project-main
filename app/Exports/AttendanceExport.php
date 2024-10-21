<?php

namespace App\Exports;

use App\Models\Scrore;
use App\Models\Student;
use App\Models\Attendance;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AttendanceExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles, WithTitle
{
    protected $teacherId;

    public function __construct($teacherId)
    {
        $this->teacherId = $teacherId;
    }

    public function collection()
    {
        return Student::with(['section', 'section.subject', 'attendances', 'scores'])
        ->whereHas('section', function ($query) {
            $query->where('user_id', $this->teacherId);
        })
        ->orderBy('section_id', 'ASC')
        ->get()
        ->map(function ($student) {
            // Count the attendances with type 1 for each student
            $attendanceCountLec = $student->attendances->where('type', 1)->where('status', 1)->count();
            $attendanceCountLab = $student->attendances->where('type', 2)->where('status', 1)->count();

            $scores = $student->scores;

            return [
                'ID' => $student->id,
                'First Name' => $student->first_name,
                'Middle Name' => $student->middle_name,
                'Last Name' => $student->last_name,
                'Section' => optional($student->section)->name,
                'Total Attendance (Lecture)' => $attendanceCountLec ? $attendanceCountLec : '0',  // Count of type 1 attendance
                'Total Attendance (Laboratory)' => $attendanceCountLab ? $attendanceCountLab : '0',  // Count of type 1 attendance
            ];
        });
    }

    protected function calculateScoreSum($scores, $type, $term)
    {
        return $scores->where('type', $type)->where('term', $term)->sum('score');
    }

    public function headings(): array
    {
        return [
            'ID',
            'First Name',
            'Middle Name',
            'Last Name',
            'Section',
            'Total Attendance (Lecture)',
            'Total Attendance (Laboratory)',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Apply styles to the sheet
        return [
            // Style the first row as bold (headings)
            1 => ['font' => ['bold' => true]],
            // Additional styling can be added here
        ];
    }

    public function title(): string
    {
        return 'Attendance'; // Custom sheet name
    }
}
