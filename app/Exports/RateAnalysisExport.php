<?php

namespace App\Exports;

use App\Exports\Sheets\RateAnalysisSheet;
use App\Exports\Sheets\ResourcesSheet;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class RateAnalysisExport implements WithMultipleSheets
{
    protected $items;
    protected $resources;
    protected $rateCard;
    protected $date;

    public function __construct(array $items, array $resources, $rateCard, $date)
    {
        $this->items = $items;
        $this->resources = $resources;
        $this->rateCard = $rateCard;
        $this->date = $date;
    }

    /**
     * The list of sheets to be created.
     */
    public function sheets(): array
    {
        $sheets = [];
        
        // Pass the ordered items to the analysis sheet
        $sheets[] = new RateAnalysisSheet($this->items, $this->rateCard, $this->date);

        // Pass the unique resources to the resources sheet
        $sheets[] = new ResourcesSheet($this->resources, $this->rateCard, $this->date);

        return $sheets;
    }
}
