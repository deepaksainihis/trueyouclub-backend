<?php

namespace App\Exports;

use App\Models\Seminar;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithColumnWidths;

class SeminarDatatableExport implements FromQuery, WithHeadings,WithMapping, WithStyles, WithColumnWidths
{
    use Exportable;

    protected $filterStartDate, $filterEndDate, $search, $sortColumnName,$sortDirection;

    public function __construct($filterStartDate, $filterEndDate, $search,$sortColumnName,$sortDirection)
    {
        $this->filterStartDate = $filterStartDate;
        $this->filterEndDate = $filterEndDate;
        $this->search = $search;
        $this->sortColumnName = $sortColumnName;
        $this->sortDirection = $sortDirection;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 40,
            'B' => 20,
            'C' => 16,
            'D' => 10,
            'E' => 10,
            'F' => 13,
            'G' => 13,
        ];
    }

    public function headings(): array
    {
        return [
            'Title',
            'Venue',
            'Start Date',
            'Start Time',
            'End Time',
            'Total Quantity',
            'Ticket Price($)',
        ];

    }

    public function query()
    {
        $searchValue = $this->search;
        
        $startDate = $this->filterStartDate ? $this->filterStartDate->startOfDay() : null;
        $endDate = $this->filterEndDate ? $this->filterEndDate->endOfDay() : null;
        
        $allSeminar = Seminar::query()->select('*')
        ->selectRaw('(TIMESTAMPDIFF(SECOND, NOW(), CONCAT(start_date, " ", end_time))) AS time_diff_seconds')

        ->where(function ($query) use ($searchValue) {
            $query->where('title', 'like', '%' . $searchValue . '%')
                ->orWhere('venue', 'like', '%' . $searchValue . '%')
                ->orWhereRaw("DATE_FORMAT(start_date,  '" . config('constants.search_full_date_format') . "') = ?", [date(config('constants.full_date_format'), strtotime($searchValue))]);

            // Check for month name (e.g., January)
            $query->orWhereRaw('LOWER(DATE_FORMAT(start_date, "%M")) LIKE ?', ['%' . strtolower($searchValue) . '%']);

            // Check for day and month (e.g., 13 January)
            $query->orWhereRaw('LOWER(DATE_FORMAT(start_date, "%e %M")) LIKE ?', ['%' . strtolower($searchValue) . '%']);
        });

        if(!is_null($startDate) && !is_null($endDate)){
            $allSeminar = $allSeminar->whereBetween('start_date', [$startDate, $endDate]);
        }

        $allSeminar = $allSeminar->orderByRaw('
            CASE 
                WHEN CONCAT(start_date, " ", end_time) <= NOW() AND CONCAT(start_date, " ", end_time) >= NOW() THEN 0
                WHEN CONCAT(start_date, " ", end_time) > NOW() THEN 1
                ELSE 2
            END ASC,
            time_diff_seconds > 0 ASC, ABS(time_diff_seconds) ASC
        ');

        return $allSeminar;
    }

    public function map($row): array
    {
        return [
            ucwords($row->title),
            ucwords($row->venue),
            convertDateTimeFormat($row->start_date,'fulldate'),
            convertDateTimeFormat($row->start_time,'fulltime'),
            convertDateTimeFormat($row->end_time,'fulltime'),
            $row->total_ticket,
            $row->ticket_price,

        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Apply styles to the first row (headings) to make them bold
        $sheet->getStyle('A1:' . $sheet->getHighestColumn() . '1')->applyFromArray([
            'font' => [
                'bold' => true,
            ],
        ]);
    }
}
