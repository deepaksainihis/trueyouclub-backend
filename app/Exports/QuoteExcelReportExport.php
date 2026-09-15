<?php

namespace App\Exports;

use DB;
use App\Models\Quote;
use App\Models\User;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithColumnWidths;

use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\FromCollection;

class QuoteExcelReportExport implements  WithHeadings,WithMapping, WithStyles, /*ShouldAutoSize,*/ WithColumnWidths, FromCollection
{
    private $rowNumber = 1;

    protected  $quoteReport, $quote_id, $quote_message, $quote_date, $total_users, $total_user_completed, $total_user_skipped, $total_leave_users;

    public function __construct($quote_id)
    {
        $this->quote_id = $quote_id;
        $this->quoteReport = Quote::query()->where('id',$this->quote_id)->first();

        $this->quote_message = $this->quoteReport->message;
        $this->quote_date = convertDateTimeFormat($this->quoteReport->created_at,'fulldate');

        $userIds = DB::table('quote_user')->where('quote_id',$this->quote_id)->pluck('user_id')->toArray();

        $this->total_leave_users = User::whereNotIn('id',$userIds)->whereHas('roles', function ($query) {
                $query->where('id', config('constants.role.user'));
            })->count();

        $this->total_users = User::whereHas('roles', function ($query) {
                $query->where('id', config('constants.role.user'));
            })->count();

        $this->total_user_completed = DB::table('quote_user')->where('quote_id',$this->quote_id)->where('status','completed')->count();

        $this->total_user_skipped = DB::table('quote_user')->where('quote_id',$this->quote_id)->where('status','skipped')->count();
    }

    public function columnWidths(): array
    {
        return [
            'A' => 10,
            'B' => 16,
            'C' => 11,
            'D' => 10,
        ];
    }


    public function headings(): array
    {

        return [
            ['Quote', $this->quote_message],
            ['Date', $this->quote_date],
            [],
            [
                trans('cruds.mis_reports.fields.total_user'),
                trans('cruds.mis_reports.fields.total_completed'),
                trans('cruds.mis_reports.fields.total_skipped'),
                trans('cruds.mis_reports.fields.total_leave'),
            ],
            [
                !empty($this->total_users) ? $this->total_users : '0',
                !empty($this->total_user_completed) ? $this->total_user_completed : '0',
                !empty($this->total_user_skipped) ? $this->total_user_skipped : '0',
                !empty($this->total_leave_users) ? $this->total_leave_users : '0',
            ],
            [],
            ['Sno.','User Name', 'Status'],
        ];

    }

    public function collection()
    {
       
        $allReports = $this->quoteReport->users()->get();

        return $allReports;
    }


    public function map($row): array
    {
        return [
            [$this->rowNumber++, ucwords($row->name), ucwords($row->pivot->status)],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        
        $sheet->getStyle('A1')->applyFromArray([
            'font' => [
                'bold' => true,
            ],
        ]);

        $sheet->getStyle('A2')->applyFromArray([
            'font' => [
                'bold' => true,
            ],
        ]);

        $sheet->getStyle('A4:D4')->applyFromArray([
            'font' => [
                'bold' => true,
            ],
        ]);

        $sheet->getStyle('A7:C7')->applyFromArray([
            'font' => [
                'bold' => true,
            ],
        ]);

       
    }
}
