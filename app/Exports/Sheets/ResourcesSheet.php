<?php

namespace App\Exports\Sheets;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\NamedRange;
use App\Services\RateAnalysisService; // To get rates if needed, or passed in

class ResourcesSheet implements FromView, WithTitle, WithEvents
{
    protected $resources;
    protected $rateCard;
    protected $date;

    public function __construct($resources, $rateCard, $date)
    {
        $this->resources = $resources;
        $this->rateCard = $rateCard;
        $this->date = $date;
    }

    public function view(): View
    {
        // We need to pass the calculated rate for each resource.
        // Ideally, this should have been pre-calculated or we calculate it here.
        // Let's assume the controller/service passed resources with their rates 
        // OR we inject the service to calculate them.
        
        // For simplicity, let's assume we need to calculate them here or in the view.
        // But the view shouldn't have logic.
        // Let's resolve the rates in the Controller and pass them attached to resources?
        // Or inject the service here.
        
        $rateAnalysisService = app(RateAnalysisService::class);
        
        foreach ($this->resources as $resource) {
            // Calculate rate using the service
            // We use getResourceRateWithUnit as per recent changes
            $rateData = $rateAnalysisService->getResourceRateWithUnit($resource, $this->rateCard, $this->date);
            $resource->current_rate = $rateData['total_rate'];
            $resource->rate_unit_id = $rateData['unit_id'];
        }

        return view('exports.resources', [
            'resources' => $this->resources,
            'rateCard' => $this->rateCard,
            'date' => $this->date
        ]);
    }

    public function title(): string
    {
        return 'Resources';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                foreach ($this->resources as $index => $resource) {
                    $rowNum = $index + 2; // Assuming headers in row 1
                    // Rate is in Column E (5th column) - Adjust based on View
                    $rateCell = '$E$' . $rowNum;
                    
                    // Sanitize resource code
                    // Assuming secondary_code is the unique identifier used in formulas
                    // If secondary_code is empty, fallback to ID?
                    $code = $resource->secondary_code ?: 'RES_' . $resource->id;
                    $safeCode = preg_replace('/[^a-zA-Z0-9_]/', '_', $code);
                    
                    $namedRangeName = 'res_' . $safeCode;

                    $event->sheet->getParent()->addNamedRange(
                        new NamedRange($namedRangeName, $event->sheet->getDelegate(), $rateCell)
                    );
                }
            }
        ];
    }
}
