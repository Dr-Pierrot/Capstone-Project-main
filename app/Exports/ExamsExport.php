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

class ExamsExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles, WithTitle
{
    protected $teacherId;

    public function __construct($teacherId)
    {
        $this->teacherId = $teacherId;
    }

    public function collection()
    {
        return Student::with(['section', 'section.subject', 'scores'])
            ->whereHas('section', function ($query) {
                $query->where('user_id', $this->teacherId);
            })
            ->orderBy('section_id', 'ASC')
            ->get()
            ->map(function ($student) {
                // Calculate sums for lec
                $prelimLec = $this->calculateScoreSum($student->scores, 'lec', 'prelim', 'score') .'/'. $this->calculateScoreSum($student->scores, 'lec', 'prelim' , 'over_score');
                $midtermLec = $this->calculateScoreSum($student->scores, 'lec', 'midterm' , 'score') . '/'. $this->calculateScoreSum($student->scores, 'lec', 'midterm' , 'over_score');
                $finalsLec = $this->calculateScoreSum($student->scores, 'lec', 'final' , 'score') . '/'. $this->calculateScoreSum($student->scores, 'lec', 'final' , 'over_score');

                // Calculate sums for lab
                $prelimLab = $this->calculateScoreSum($student->scores, 'lab', 'prelim' , 'score') . '/'. $this->calculateScoreSum($student->scores, 'lab', 'prelim' , 'over_score');
                $midtermLab = $this->calculateScoreSum($student->scores, 'lab', 'midterm' , 'score') . '/'. $this->calculateScoreSum($student->scores, 'lab', 'midterm' , 'over_score');
                $finalsLab = $this->calculateScoreSum($student->scores, 'lab', 'final' , 'score') . '/'. $this->calculateScoreSum($student->scores, 'lab', 'final' , 'over_score');

                return [
                    'ID' => $student->id,
                    'First Name' => $student->first_name,
                    'Middle Name' => $student->middle_name,
                    'Last Name' => $student->last_name,
                    'Section' => optional($student->section)->name,
                    'Prelim Lec' => $prelimLec,
                    'Midterm Lec' => $midtermLec,
                    'Final Lec' => $finalsLec,
                    'Prelim Lab' => $prelimLab,
                    'Midterm Lab' => $midtermLab,
                    'Final Lab' => $finalsLab,
                ];
            });
    }

    protected function calculateScoreSum($scores, $type, $term, $look)
    {
        return $scores->where('type', $type)->where('term', $term)->sum($look);
    }

    public function headings(): array
    {
        return [
            'ID',
            'First Name',
            'Middle Name',
            'Last Name',
            'Section',
            'Prelim Lec',
            'Midterm Lec',
            'Final Lec',
            'Prelim Lab',
            'Midterm Lab',
            'Final Lab',
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
        return 'Exams'; // Custom sheet name
    }
}
