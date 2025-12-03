<?php

namespace App\Exports\Sheets;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\NamedRange;

class RateAnalysisSheetsor implements FromView, WithTitle, WithEvents
{
    protected $itemsAnalysis;
    protected $data;

    public function __construct(array $itemsAnalysis, array $data)
    {
        $this->itemsAnalysis = $itemsAnalysis;
        $this->data = $data;
    }

    public function title(): string
    {
        return 'Rate Analysis';
    }

    public function view(): View
    {
        return view('exports.sheets.rate_analysis', ['items_analysis' => $this->itemsAnalysis]);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                // We need to find the cell where the final rate is for each item to create the named range.
                // Since the view renders dynamic tables, we can't easily know the row number beforehand.
                // However, we can use a trick:
                // 1. In the view, we can add a hidden column or metadata, or
                // 2. We can calculate the row usage if we know the height of each block.
                // 3. OR, simpler: We can iterate through the rows after generation to find markers? No, slow.
                
                // Better approach:
                // The view will render items sequentially.
                // We can calculate the row index if we know exactly how many rows each item takes.
                // Each item has:
                // Header (1 row)
                // Resources (N rows)
                // Subitems (M rows)
                // Overheads (K rows)
                // Totals/Footer (X rows)
                // Spacing (1 row)
                
                $currentRow = 1;
                
                foreach ($this->itemsAnalysis as $itemAnalysis) {
                    // Header
                    $currentRow++; 
                    
                    // Resources
                    $currentRow += count($itemAnalysis['resources']);
                    
                    // Subitems
                    $currentRow += count($itemAnalysis['sub_items']);
                    
                    // Overheads
                    $currentRow += count($itemAnalysis['overheads']);
                    
                    // Totals/Footer
                    // Let's assume the footer takes specific rows.
                    // Based on the view we will create:
                    // Resource Total (1)
                    // Subitem Total (1)
                    // Overhead Total (1)
                    // Grand Total (1)
                    // Turnout (1)
                    // Final Rate (1)
                    $currentRow += 6; 
                    
                    // The Final Rate is in the last row of this block.
                    $rateCell = 'D' . $currentRow; // Assume Rate is in Column D
                    
                    $itemCode = preg_replace('/[^a-zA-Z0-9_]/', '_', $itemAnalysis['item_code']);
                    $namedRangeName = 'ra_' . $itemCode;
                    
                    $event->sheet->getParent()->addNamedRange(
                        new NamedRange($namedRangeName, $event->sheet->getDelegate(), "'Rate Analysis'!" . $rateCell)
                    );
                    
                    // Spacing
                    $currentRow += 2; // <br/> + next start
                }
            }
        ];
    }
}
