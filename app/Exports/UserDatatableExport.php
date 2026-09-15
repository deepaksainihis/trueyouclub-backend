<?php

namespace App\Exports;

use App\Models\User;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithColumnWidths;


class UserDatatableExport implements FromQuery, WithHeadings,WithMapping, WithStyles, WithColumnWidths
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
            'C' => 40,
            'D' => 20,
        ];
    }

    public function headings(): array
    {
        return [
            'Full Name',
            'Phone Number',
            'Email',
            'Created Date',
        ];

    }

    public function query()
    {
        $statusSearch = null;
        $searchValue = $this->search;
        if (Str::contains('break', strtolower($searchValue))) {
            $statusSearch = 1;
        } else if (Str::contains('continue', strtolower($searchValue))) {
            $statusSearch = 0;
        }

        $starNumber = null;
        if(in_array($searchValue,config('constants.ratings'))){
            $starNumber = $searchValue;
        }
      
        $startDate = $this->filterStartDate ? $this->filterStartDate->startOfDay() : null;
        $endDate = $this->filterEndDate ? $this->filterEndDate->endOfDay() : null;

        $allUsers = User::query()->where(function ($query) use ($searchValue, $statusSearch, $starNumber) {
            $query->where('name', 'like', '%' . $searchValue . '%')
                ->orWhere('phone', 'like', '%' . $searchValue . '%')
                ->orWhere('star_no', $starNumber)
                ->orWhere('is_active', $statusSearch)
                ->orWhereRaw("date_format(created_at, '" . config('constants.search_full_date_format') . "') like ?", ['%' . $searchValue . '%']);
        })->whereHas('roles', function ($query) {
            $query->where('id', 2);
        });

        if(!is_null($startDate) && !is_null($endDate)){
            $allUsers = $allUsers->whereBetween('created_at', [$startDate, $endDate]);
        }

        $allUsers = $allUsers->orderBy($this->sortColumnName, $this->sortDirection);
     
        return $allUsers;
    }

    public function map($row): array
    {
        return [
            $row->name,
            $row->phone,
            $row->email,
            convertDateTimeFormat($row->created_at,'fulldate'),
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
