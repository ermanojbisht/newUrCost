<?php

namespace App\Exports;

use App\Models\Sor;
use App\Models\Item;
use App\Models\RateCard;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Log;

class SorExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    protected $sor;
    protected $rateCard;
    protected $isDetailed;

    public function __construct(Sor $sor, RateCard $rateCard, $isDetailed = false)
    {
        $this->sor = $sor;
        $this->rateCard = $rateCard;
        $this->isDetailed = $isDetailed;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        // Fetch items for the SOR, eager loading the specific rate for the given rate card
        // Ordering by 'lft' (nested set model) to maintain hierarchy
        return Item::where('sor_id', $this->sor->id)
            ->with(['itemRates' => function($q) {
                $q->where('rate_card_id', $this->rateCard->id);
            }, 'unit', 'parent'])
            ->orderBy('lft')
            ->get();
    }

    public function headings(): array
    {
        $headings = ['S.No', 'Chapter/Item No', 'Particulars of Item'];
        
        if ($this->isDetailed) {
            $headings = array_merge($headings, ['Labor Cost', 'Material Cost', 'Machine Cost', 'Overhead Cost']);
        }

        return array_merge($headings, ['Rate', 'Unit']);
    }

    public function map($item): array
    {
        static $sno = 0;
        $sno++;
        //Log::info('item = ' . print_r($item->toArray(), true));

        $rate = '';
        $unit = '';
        $laborCost = '';
        $materialCost = '';
        $machineCost = '';
        $overheadCost = '';

        // Item type 3 = measurable item
        if ($item->item_type == 3) {
            $unit = optional($item->unit)->name;

            // If referenced item
            if ($item->reference_from > 0) {
                $referencedItemNo = Item::where('id', $item->reference_from)->value('item_number') ?? 'Unknown';
                $rate = "As per item no {$referencedItemNo}";
                $unit = ''; // Unit suppressed for reference items
            } else {
                // Get first rate (itemRates already eager loaded & filtered)
                $rateRecord = $item->itemRates->first();
                $rate = optional($rateRecord)->rate ?? '';
                
                if ($this->isDetailed && $rateRecord) {
                    $laborCost = $rateRecord->labor_cost;
                    $materialCost = $rateRecord->material_cost;
                    $machineCost = $rateRecord->machine_cost;
                    $overheadCost = $rateRecord->overhead_cost;
                }
            }
        }

        $data = [
            $sno,
            $item->item_number,
            $item->description,
        ];

        if ($this->isDetailed) {
            $data[] = $laborCost;
            $data[] = $materialCost;
            $data[] = $machineCost;
            $data[] = $overheadCost;
        }

        $data[] = $rate;
        $data[] = $unit;

        //Log::info('data = ' . print_r($data, true));

        return $data;
    }


    public function columnWidths(): array
    {
        $widths = [
            'A' => 8,   // S.No
            'B' => 15,  // Item No
            'C' => 60,  // Description
        ];

        if ($this->isDetailed) {
            $widths['D'] = 12; // Labor
            $widths['E'] = 12; // Material
            $widths['F'] = 12; // Machine
            $widths['G'] = 12; // Overhead
            $widths['H'] = 15; // Rate
            $widths['I'] = 10; // Unit
        } else {
            $widths['D'] = 15; // Rate
            $widths['E'] = 10; // Unit
        }

        return $widths;
    }

    public function styles(Worksheet $sheet)
    {
        $lastCol = $this->isDetailed ? 'I' : 'E';

        // Header Style
        $sheet->getStyle("A1:{$lastCol}1")->getFont()->setBold(true);
        $sheet->getStyle("A1:{$lastCol}1")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("A1:{$lastCol}1")->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFE0E0E0');

        // Iterate through rows to apply depth-based styling
        // Row 1 is header, data starts at Row 2
        $rowIterator = $sheet->getRowIterator();
        
        // We need to access the collection again or keep track of it to know the depth
        // Since map() doesn't pass the index easily in a way that syncs with rows perfectly if we filter,
        // but here we are not filtering in map, so row index corresponds to collection index + 2.
        
        $items = $this->collection();
        $rowIndex = 2;

        foreach ($items as $item) {
            $rowDimension = $sheet->getRowDimension($rowIndex);
            
            // Apply styles based on item type or depth
            // Assuming item_type 1 = Chapter, 2 = Sub-chapter, 3 = Item
            // Or using depth from nested set
            
            if ($item->item_type == 1) { // Chapter
                $sheet->getStyle("A$rowIndex:{$lastCol}$rowIndex")->getFont()->setBold(true)->setSize(14)->getColor()->setARGB('FF0000FF'); // Blue
            } elseif ($item->item_type == 2) { // Sub-chapter
                $sheet->getStyle("A$rowIndex:{$lastCol}$rowIndex")->getFont()->setBold(true)->setSize(12)->getColor()->setARGB('FF008000'); // Green
            }
            
            // Wrap text for description
            $sheet->getStyle("C$rowIndex")->getAlignment()->setWrapText(true);
            
            // Vertical alignment center
            $sheet->getStyle("A$rowIndex:{$lastCol}$rowIndex")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

            $rowIndex++;
        }
        
        return [];
    }
}
