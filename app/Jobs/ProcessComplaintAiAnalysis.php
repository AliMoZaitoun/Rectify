<?php

namespace App\Jobs;

use App\Models\Complaint\Complaint;
use App\Services\AI\ComplaintAiService;
use App\DAO\Complaint\CategoryDAO;
use App\Services\Complaint\ComplaintService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessComplaintAiAnalysis implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private readonly int $complaintId,
        private readonly string $title,
        private readonly string $description,
    ) {}

    public function handle(ComplaintAiService $aiService, CategoryDAO $categoryDAO): void
    {
        try {
            $availableCategories = $categoryDAO->all()->map(function ($cat) {
                return ['id' => $cat->id, 'name' => $cat->name];
            })->toArray();

            $aiAnalysis = $aiService->analyzeNewComplaint(
                $this->title,
                $this->description,
                $availableCategories
            );

            $complaint = Complaint::find($this->complaintId);

            if ($complaint) {
                $finalCategoryId = $complaint->category_id ?? $aiAnalysis['ai_suggested_category'];


                $complaint->update([
                    'priority'              => $aiAnalysis['priority'],
                    'ai_summary'            => $aiAnalysis['ai_summary'],
                    'ai_suggested_category' => $aiAnalysis['ai_suggested_category'],
                    'is_spam'               => $aiAnalysis['is_spam'],
                    'category_id'           => $finalCategoryId,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('AI Background Process Failed: ' . $e->getMessage());
        }
    }
}
