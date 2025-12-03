<?php

namespace App\Exports\Sheets;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\NamedRange;

class ResourcesSheetsor implements FromView, WithTitle, WithEvents
{
    protected $resources;
    protected $rateCard;

    public function __construct(array $resources, $rateCard)
    {
        $this->resources = $resources;
        $this->rateCard = $rateCard;
    }

    public function title(): string
    {
        return 'Resources';
    }

    public function view(): View
    {
        return view('exports.sheets.resources', ['resources' => $this->resources]);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                // After the sheet is created, add the named ranges
                foreach ($this->resources as $index => $resource) {
                    $rowNum = $index + 2; // +1 for header, +1 for 0-index
                    $rateCell = 'D' . $rowNum; // Assume Rate is in Column D
                    
                    // Sanitize resCode for named range (remove spaces, special chars)
                    $resCode = preg_replace('/[^a-zA-Z0-9_]/', '_', $resource['resCode']);
                    $namedRangeName = 'res_' . $resCode;

                    $event->sheet->getParent()->addNamedRange(
                        new NamedRange($namedRangeName, $event->sheet->getDelegate(), "'Resources'!" . $rateCell)
                    );
                }
            }
        ];
    }
}
