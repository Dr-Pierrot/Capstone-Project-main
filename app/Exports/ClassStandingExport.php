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

class ClassStandingExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles, WithTitle
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
                // Calculate sums for different score types
                $prelimPerformanceTask = $this->calculateScoreSum($student->scores, 'performance_task', 'prelim', 'score') .'/'. $this->calculateScoreSum($student->scores, 'performance_task', 'prelim', 'over_score');
                $midtermPerformanceTask = $this->calculateScoreSum($student->scores, 'performance_task', 'midterm', 'score') . '/'. $this->calculateScoreSum($student->scores, 'performance_task', 'midterm', 'over_score');
                $finalPerformanceTask = $this->calculateScoreSum($student->scores, 'performance_task', 'final', 'score') . '/'. $this->calculateScoreSum($student->scores, 'performance_task', 'final', 'over_score');

                // Calculate sums for quizzes
                $prelimQuiz = $this->calculateScoreSum($student->scores, 'quiz', 'prelim', 'score') . '/'. $this->calculateScoreSum($student->scores, 'quiz', 'prelim', 'over_score');
                $midtermQuiz = $this->calculateScoreSum($student->scores, 'quiz', 'midterm', 'score') . '/'. $this->calculateScoreSum($student->scores, 'quiz', 'midterm', 'over_score');
                $finalQuiz = $this->calculateScoreSum($student->scores, 'quiz', 'final', 'score') . '/'. $this->calculateScoreSum($student->scores, 'quiz', 'final', 'over_score');

                // Calculate sums for recitations
                $prelimRecitation = $this->calculateScoreSum($student->scores, 'recitation', 'prelim', 'score') . '/'. $this->calculateScoreSum($student->scores, 'recitation', 'prelim', 'over_score');
                $midtermRecitation = $this->calculateScoreSum($student->scores, 'recitation', 'midterm', 'score') . '/'. $this->calculateScoreSum($student->scores, 'recitation', 'midterm', 'over_score');
                $finalRecitation = $this->calculateScoreSum($student->scores, 'recitation', 'final', 'score') . '/'. $this->calculateScoreSum($student->scores, 'recitation', 'final', 'over_score');

                return [
                    'ID' => $student->id,
                    'First Name' => $student->first_name,
                    'Middle Name' => $student->middle_name,
                    'Last Name' => $student->last_name,
                    'Section' => optional($student->section)->name,
                    'Prelim Performance Task' => $prelimPerformanceTask,
                    'Midterm Performance Task' => $midtermPerformanceTask,
                    'Final Performance Task' => $finalPerformanceTask,
                    'Prelim Quiz' => $prelimQuiz,
                    'Midterm Quiz' => $midtermQuiz,
                    'Final Quiz' => $finalQuiz,
                    'Prelim Recitation' => $prelimRecitation,
                    'Midterm Recitation' => $midtermRecitation,
                    'Final Recitation' => $finalRecitation,
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
            'Prelim Performance Task',
            'Midterm Performance Task',
            'Final Performance Task',
            'Prelim Quiz',
            'Midterm Quiz',
            'Final Quiz',
            'Prelim Recitation',
            'Midterm Recitation',
            'Final Recitation',
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
        return 'Class Standing'; // Custom sheet name
    }
}
