<?php

namespace App\Exports;

use App\Models\Contact;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithColumnWidths;

class ContactDataTableExport implements FromQuery, WithHeadings,WithMapping, WithStyles, WithColumnWidths
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
            'C' => 20,
        ];
    }

    public function headings(): array
    {
        return [
            'Full Name',
            'Phone Number',
            'Created Date',
        ];

    }

    public function query()
    {
        $searchValue = $this->search;
      
        $startDate = $this->filterStartDate ? $this->filterStartDate->startOfDay() : null;
        $endDate = $this->filterEndDate ? $this->filterEndDate->endOfDay() : null;

        $allContacts = Contact::query()->where(function ($query) use ($searchValue,$startDate,$endDate) {
            $query
                ->whereRaw("CONCAT(first_name, ' ', last_name) like ?", ['%' . $searchValue . '%'])
                ->orWhere('email', 'like', '%' . $searchValue . '%')
                ->orWhere('phone_number', 'like', '%' . $searchValue . '%')
                ->orWhereRaw("date_format(created_at, '" . config('constants.search_full_date_format') . "') like ?", ['%' . $searchValue . '%']);
        });

        if(!is_null($startDate) && !is_null($endDate)){
            $allContacts = $allContacts->whereBetween('created_at', [$startDate, $endDate]);
        }

        if($this->sortColumnName == 'full_name'){
            $allContacts = $allContacts->orderByRaw("CONCAT(first_name, ' ', last_name) ".$this->sortDirection);
        }else{
            $allContacts = $allContacts->orderBy($this->sortColumnName, $this->sortDirection);
        }

        return $allContacts;
    }

    public function map($row): array
    {
        return [
            $row->full_name,
            $row->phone_number,
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
