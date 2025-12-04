<?php

namespace App\Exports\Sheets;

use App\Services\ItemSkeletonService;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\NamedRange;

class RateAnalysisSheet implements FromView, WithTitle, WithEvents
{
    protected $items;
    protected $rateCard;
    protected $date;
    protected $itemSkeletonService;

    public function __construct($items, $rateCard, $date)
    {
        $this->items = $items;
        $this->rateCard = $rateCard;
        $this->date = $date;
        $this->itemSkeletonService = app(ItemSkeletonService::class);
    }

    public function view(): View
    {
        // Calculate rate analysis data for each item using ItemSkeletonService
        $itemsWithAnalysis = [];

        foreach ($this->items as $item) {
            $analysis = $this->itemSkeletonService->calculateRate(
                $item,
                $this->rateCard->id,
                $this->date
            );

            $itemsWithAnalysis[] = [
                'item' => $item,
                'analysis' => $analysis
            ];
        }

        return view('exports.rate_analysis', [
            'itemsWithAnalysis' => $itemsWithAnalysis,
            'rateCard' => $this->rateCard,
            'date' => $this->date
        ]);
    }

    public function title(): string
    {
        return 'Rate Analysis';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Calculate row positions for named ranges
                $currentRow = 1; // Start from row 1

                foreach ($this->items as $item) {
                    // Get analysis data for this item
                    $analysis = $this->itemSkeletonService->calculateRate(
                        $item,
                        $this->rateCard->id,
                        $this->date
                    );

                    // Header rows (Item header + blank line)
                    $currentRow += 2;

                    // Resources section header
                    $currentRow++;

                    // Resources data rows
                    $resourceCount = count($analysis['resources']);
                    $currentRow += $resourceCount;

                    // Subitems section header (if any subitems)
                    if (count($analysis['subitems']) > 0) {
                        $currentRow++;
                        $currentRow += count($analysis['subitems']);
                    }

                    // Overheads section header (if any overheads)
                    if (count($analysis['overheads']) > 0) {
                        $currentRow++;
                        $currentRow += count($analysis['overheads']);
                    }

                    // Summary section (8 rows: Material, Labor, Machine, Resources, Subitems, Overheads, Total, Final Rate)
                    $currentRow += 8;

                    // The final rate is in the last row of the summary
                    $finalRateRow = $currentRow;
                    $rateCell = '$B$' . $finalRateRow; // Assuming final rate is in column B

                    // Create Named Range for final rate
                    $safeCode = preg_replace('/[^a-zA-Z0-9_]/', '_', $item->item_code);
                    $namedRangeName = 'ra_' . $safeCode;

                    try {
                        $event->sheet->getParent()->addNamedRange(
                            new NamedRange($namedRangeName, $event->sheet->getDelegate(), $rateCell)
                        );
                    } catch (\Exception $e) {
                        // If named range already exists or other error, continue
                    }

                    // Blank line between items
                    $currentRow += 2;
                }
            }
        ];
    }
}
