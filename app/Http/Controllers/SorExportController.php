<?php

namespace App\Http\Controllers;

use App\Models\Sor;
use App\Models\RateCard;
use App\Models\File;
use App\Exports\SorExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Storage;
use App\Services\RateCalculationService;
use App\Exports\SorRateAnalysisExport;

class SorExportController extends Controller
{
    protected $rateCalculator;

    public function __construct(RateCalculationService $rateCalculator)
    {
        $this->rateCalculator = $rateCalculator;
    }

    public function index()
    {
        $sors = Sor::all();
        $rateCards = RateCard::all();
        return view('sors.export', compact('sors', 'rateCards'));
    }

    public function list()
    {
        $files = File::where('document_type', 'SOR Report')
                     ->with(['sor', 'rateCard', 'createdBy'])
                     ->orderBy('created_at', 'desc')
                     ->get();
        
        // Add S3 URL to each file
        $files->transform(function ($file) {
            $file->url = Storage::disk('s3')->url($file->filename);
            return $file;
        });

        return response()->json($files);
    }

    public function export(Request $request, Sor $sor, RateCard $rateCard, $format = 'xlsx')
    {
        $extension = strtolower($format);
        $type = $request->query('type', 'standard');
        $isDetailed = $type === 'detailed';
        
        $fileName = "reports/SOR_{$sor->id}_{$rateCard->id}_" . date('Y-m-d_H-i-s');
        if ($isDetailed) {
            $fileName .= "_detailed";
        }
        $fileName .= ".{$extension}";
        
        // Determine writer type
        $writerType = match($extension) {
            'pdf' => \Maatwebsite\Excel\Excel::DOMPDF,
            'csv' => \Maatwebsite\Excel\Excel::CSV,
            default => \Maatwebsite\Excel\Excel::XLSX,
        };

        // Store to S3
        Excel::store(new SorExport($sor, $rateCard, $isDetailed), $fileName, 's3', $writerType);

        // Log the file generation
        $title = "SOR - {$sor->name} - {$rateCard->name}";
        if ($isDetailed) {
            $title .= " (Detailed)";
        }

        $file = File::create([
            'title' => $title,
            'filename' => $fileName,
            'status' => 'active',
            'document_type' => 'SOR Report',
            'rate_card_id' => $rateCard->id,
            'sor_id' => $sor->id,
            'created_by' => Auth::id(),
        ]);

        return response()->json([
            'message' => 'Export generated successfully',
            'file' => $file,
            'url' => Storage::disk('s3')->url($fileName)
        ]);
    }

    public function delete(File $file)
    {
        if ($file->document_type !== 'SOR Report') {
            return response()->json(['message' => 'Invalid file type'], 403);
        }

        try {
            // Delete from S3
            if (Storage::disk('s3')->exists($file->filename)) {
                Storage::disk('s3')->delete($file->filename);
            }

            // Delete from DB
            $file->delete();

            return response()->json(['message' => 'File deleted successfully']);
        } catch (\Exception $e) {
            \Log::error('SOR Export Delete Error: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to delete file: ' . $e->getMessage()], 500);
        }
    }

    public function exportSorRateAnalysis(Sor $sor, RateCard $rateCard)
    {
        $date = now()->toDateString();

        // 1. Let the service do all the heavy lifting of data preparation
        $exportData = $this->rateCalculator->getFullSorAnalysisData($sor, $rateCard, $date);

        $fileName = "reports/Rate_Analysis_{$sor->id}_{$rateCard->id}_" . date('Y-m-d_H-i-s') . ".xlsx";

        // 2. Pass the prepared data array to the main export class and store to S3
        Excel::store(new SorRateAnalysisExport($exportData), $fileName, 's3');

        // Log the file generation
        $file = File::create([
            'title' => "Rate Analysis - {$sor->name} - {$rateCard->name}",
            'filename' => $fileName,
            'status' => 'active',
            'document_type' => 'SOR Report',
            'rate_card_id' => $rateCard->id,
            'sor_id' => $sor->id,
            'created_by' => Auth::id(),
        ]);

        return response()->json([
            'message' => 'Rate Analysis generated successfully',
            'file' => $file,
            'url' => Storage::disk('s3')->url($fileName)
        ]);
    }
}
