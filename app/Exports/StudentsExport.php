<?php

namespace App\Exports;

use App\Models\Student;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StudentsExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles, WithTitle
{
    protected $teacherId;

    public function __construct($teacherId)
    {
        $this->teacherId = $teacherId;
    }

    public function collection()
    {
        return Student::with('section')
            ->whereHas('section', function ($query) {
                $query->where('user_id', $this->teacherId);
            })
            ->orderBy('section_id', 'ASC')
            ->get()
            ->map(function ($student) {
                return [
                    'ID' => $student->id,
                    'First Name' => $student->first_name,
                    'Middle Name' => $student->middle_name,
                    'Last Name' => $student->last_name,
                    'Section' => optional($student->section)->name
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
        return 'Students List'; // Custom sheet name
    }
}
