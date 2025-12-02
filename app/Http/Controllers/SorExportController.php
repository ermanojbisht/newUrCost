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

class SorExportController extends Controller
{
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
        $fileName = "reports/SOR_{$sor->id}_{$rateCard->id}_" . date('Y-m-d_H-i-s') . ".{$extension}";
        
        // Determine writer type
        $writerType = match($extension) {
            'pdf' => \Maatwebsite\Excel\Excel::DOMPDF,
            'csv' => \Maatwebsite\Excel\Excel::CSV,
            default => \Maatwebsite\Excel\Excel::XLSX,
        };

        // Store to S3
        Excel::store(new SorExport($sor, $rateCard), $fileName, 's3', $writerType);

        // Log the file generation
        $file = File::create([
            'title' => "SOR Export - {$sor->name} - {$rateCard->reference_desc}",
            'filename' => $fileName,
            'status' => 'Generated',
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
}
