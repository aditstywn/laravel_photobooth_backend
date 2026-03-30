<?php

namespace App\Jobs;

use App\Models\Photo;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class DeletePhotoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $photoId;



    /**
     * Create a new job instance.
     */
    public function __construct($photoId)
    {
        $this->photoId = $photoId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $photo = Photo::with('photoResults')->find($this->photoId);

        if (!$photo) {
            return;
        }

        $filePaths = collect([
            $photo->photo_template,
            $photo->gif_vidio,
        ])
        ->merge($photo->photoResults->pluck('photo_ori'))
        ->filter()
        ->unique()
        ->values()
        ->all();

        $photo->delete();

        if (!empty($filePaths)) {
            Storage::disk('public')->delete($filePaths);
        }
    }
}
