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

class PeriodicGradesExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles, WithTitle
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
                $CSweights = [
                    'performance_task' => 40, // 40%
                    'quiz' => 30,             // 30%
                    'recitation' => 30        // 30%
                ];
                $Examweights = [
                    'lec' => 50, // 50%
                    'lab' => 50, // 50%
                ];

                // Calculate sums for different score types
                $prelimPerformanceTaskScore = $this->calculateScoreSum($student->scores, 'performance_task', 'prelim', 'score');
                $midtermPerformanceTaskScore = $this->calculateScoreSum($student->scores, 'performance_task', 'midterm', 'score');
                $finalsPerformanceTaskScore = $this->calculateScoreSum($student->scores, 'performance_task', 'final', 'score');

                $prelimPerformanceTaskOverScore = $this->calculateScoreSum($student->scores, 'performance_task', 'prelim', 'over_score');
                $midtermPerformanceTaskOverScore = $this->calculateScoreSum($student->scores, 'performance_task', 'midterm', 'over_score');
                $finalsPerformanceTaskOverScore = $this->calculateScoreSum($student->scores, 'performance_task', 'final', 'over_score');

                $calculatePrelimPerfomanceTask = $prelimPerformanceTaskOverScore != 0? (($prelimPerformanceTaskScore/$prelimPerformanceTaskOverScore)*40+60) * $CSweights['performance_task'] / 100 : 60 * $CSweights['performance_task'] / 100;
                $calculateMidtermPerformanceTask = $midtermPerformanceTaskOverScore != 0? (($midtermPerformanceTaskScore/$midtermPerformanceTaskOverScore)*30+60) * $CSweights['performance_task'] / 100 : 60 * $CSweights['performance_task'] / 100;
                $calculateFinalsPerformanceTask = $finalsPerformanceTaskOverScore != 0? (($finalsPerformanceTaskScore/$finalsPerformanceTaskOverScore)*30+60) * $CSweights['performance_task'] / 100 : 60 * $CSweights['performance_task'] / 100;

                // Calculate sums for quizzes
                $prelimQuizScore = $this->calculateScoreSum($student->scores, 'quiz', 'prelim', 'score');
                $midtermQuizScore = $this->calculateScoreSum($student->scores, 'quiz', 'midterm', 'score');
                $finalsQuizScore = $this->calculateScoreSum($student->scores, 'quiz', 'final', 'score');

                $prelimQuizOverScore = $this->calculateScoreSum($student->scores, 'quiz', 'prelim', 'over_score');
                $midtermQuizOverScore = $this->calculateScoreSum($student->scores, 'quiz', 'midterm', 'over_score');
                $finalsQuizOverScore = $this->calculateScoreSum($student->scores, 'quiz', 'final', 'over_score');
                
                $calculatePrelimQuiz = $prelimQuizOverScore != 0? (($prelimQuizScore/$prelimQuizOverScore)*40+60) * $CSweights['quiz'] / 100 : 60 * $CSweights['quiz'] / 100;
                $calculateMidtermQuiz = $midtermQuizOverScore != 0? (($midtermQuizScore/$midtermQuizOverScore)*40+60) * $CSweights['quiz'] / 100 : 60 * $CSweights['quiz'] / 100;
                $calculateFinalsQuiz = $finalsQuizOverScore != 0? (($finalsQuizScore/$finalsQuizOverScore)*40+60) * $CSweights['quiz'] / 100 : 60 * $CSweights['quiz'] / 100;
 
                // Calculate sums for recitations
                $prelimRecitationScore = $this->calculateScoreSum($student->scores, 'recitation', 'prelim', 'score'); 
                $midtermRecitationScore = $this->calculateScoreSum($student->scores, 'recitation', 'midterm', 'score');
                $finalsRecitationScore = $this->calculateScoreSum($student->scores, 'recitation', 'final', 'score');

                $prelimRecitationOverScore = $this->calculateScoreSum($student->scores, 'recitation', 'prelim', 'over_score');
                $midtermRecitationOverScore = $this->calculateScoreSum($student->scores, 'recitation', 'midterm', 'over_score');
                $finalsRecitationOverScore = $this->calculateScoreSum($student->scores, 'recitation', 'final', 'over_score');

                $calculatePrelimRecitation = $prelimRecitationOverScore != 0? (($prelimRecitationScore/$prelimRecitationOverScore)*40+60) * $CSweights['recitation'] / 100 : 60 * $CSweights['recitation'] / 100;
                $calculateMidtermRecitation = $midtermRecitationOverScore != 0? (($midtermRecitationScore/$midtermRecitationOverScore)*40+60) * $CSweights['recitation'] / 100 : 60 * $CSweights['recitation'] / 100;
                $calculateFinalsRecitation = $finalsRecitationOverScore != 0? (($finalsRecitationScore/$finalsRecitationOverScore)*40+60) * $CSweights['recitation'] / 100 : 60 * $CSweights['recitation'] / 100;
                
                // Calculate sums for lec
                $prelimLecScore = $this->calculateScoreSum($student->scores, 'lec', 'prelim', 'score');
                $midtermLecScore = $this->calculateScoreSum($student->scores, 'lec', 'midterm' , 'score');
                $finalsLecScore = $this->calculateScoreSum($student->scores, 'lec', 'final' , 'score'); 

                $prelimLecOverScore = $this->calculateScoreSum($student->scores, 'lec', 'prelim' , 'over_score');
                $midtermLecOverScore = $this->calculateScoreSum($student->scores, 'lec', 'midterm' , 'over_score');
                $finalsLecOverScore = $this->calculateScoreSum($student->scores, 'lec', 'final' , 'over_score');

                $calculatePrelimLec = $prelimLecOverScore != 0? (($prelimLecScore/$prelimLecOverScore)*40+60) * $Examweights['lec'] / 100 : 60 * $Examweights['lec'] / 100;
                $calculateMidtermLec = $midtermLecOverScore != 0? (($midtermLecScore/$midtermLecOverScore)*40+60) * $Examweights['lec'] / 100 : 60 * $Examweights['lec'] / 100;
                $calculateFinalsLec = $finalsLecOverScore != 0? (($finalsLecScore/$finalsLecOverScore)*40+60) * $Examweights['lec'] / 100 : 60 * $Examweights['lec'] / 100;

                // Calculate sums for lab
                $prelimLabScore = $this->calculateScoreSum($student->scores, 'lab', 'prelim' , 'score');
                $midtermLabScore = $this->calculateScoreSum($student->scores, 'lab', 'midterm' , 'score');
                $finalsLabScore = $this->calculateScoreSum($student->scores, 'lab', 'final' , 'score');

                $prelimLabOverScore = $this->calculateScoreSum($student->scores, 'lab', 'prelim' , 'over_score');
                $midtermLabOverScore = $this->calculateScoreSum($student->scores, 'lab', 'midterm' , 'over_score');
                $finalsLabOverScore = $this->calculateScoreSum($student->scores, 'lab', 'final' , 'over_score');

                $calculatePrelimLab = $prelimLabOverScore != 0? (($prelimLabScore/$prelimLabOverScore)*40+60) * $Examweights['lab'] / 100 : 60 * $Examweights['lab'] / 100;
                $calculateMidtermLab = $midtermLabOverScore != 0? (($midtermLabScore/$midtermLabOverScore)*40+60) * $Examweights['lab'] / 100 : 60 * $Examweights['lab'] / 100;
                $calculateFinalsLab = $finalsLabOverScore != 0? (($finalsLabScore/$finalsLabOverScore)*40+60) * $Examweights['lab'] / 100 : 60 * $Examweights['lab'] / 100;

                $prelimClassStanding = $calculatePrelimPerfomanceTask + $calculatePrelimQuiz + $calculatePrelimRecitation;

                $midtermClassStanding = $calculateMidtermPerformanceTask + $calculateMidtermQuiz + $calculateMidtermRecitation;

                $finalsClassStanding = $calculateFinalsPerformanceTask + $calculateFinalsQuiz + $calculateFinalsRecitation;

                $prelimExam = $calculatePrelimLec + $calculatePrelimLab;

                $midtermExam = $calculateMidtermLec + $calculateMidtermLab;

                $finalsExam = $calculateFinalsLec + $calculateFinalsLab;

                $prelimGrade = ($prelimClassStanding * 0.6) + ($prelimExam * 0.4);
                $midtermGrade = ($midtermClassStanding * 0.6) + ($midtermExam * 0.4);
                $finalsGrade = ($finalsClassStanding * 0.6) + ($finalsExam * 0.4);  

                $total = ($prelimGrade * 0.3) + ($midtermGrade * 0.3) + ($finalsGrade * 0.4); 

                return [
                    'ID' => $student->id,
                    'First Name' => $student->first_name,
                    'Middle Name' => $student->middle_name,
                    'Last Name' => $student->last_name,
                    'Section' => optional($student->section)->name,
                    'Prelim Class Standing' => number_format($prelimClassStanding, 2),
                    'Prelim Exam' => number_format($prelimExam, 2),
                    'Prelim Grade' => number_format($prelimGrade, 2),
                    'Midterm Class Standing' => number_format($midtermClassStanding, 2),
                    'Midterm Exam' => number_format($midtermExam, 2),
                    'Midterm Grade' => number_format($midtermGrade, 2),
                    'Final Class Standing' => number_format($finalsClassStanding, 2),
                    'Final Exam' => number_format($finalsExam, 2),
                    'Final Grade' => number_format($finalsGrade, 2),
                    'Total' => number_format($total, 2),
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
            'Prelim Class Standing',
            'Prelim Exam',
            'Prelim Grade',
            'Midterm Class Standing',
            'Midterm Exam',
            'Midterm Grade',
            'Final Class Standing',
            'Final Exam',
            'Final Grade',
            'Total',
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
