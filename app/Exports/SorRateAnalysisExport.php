<?php

namespace App\Exports;

use App\Exports\Sheets\RateAnalysisSheetsor;
use App\Exports\Sheets\ResourcesSheetsor;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class SorRateAnalysisExport implements WithMultipleSheets
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function sheets(): array
    {
        return [
            new ResourcesSheetsor($this->data['unique_resources'], $this->data['rateCard']),
            new RateAnalysisSheetsor($this->data['ordered_items_analysis'], $this->data),
        ];
    }
}
