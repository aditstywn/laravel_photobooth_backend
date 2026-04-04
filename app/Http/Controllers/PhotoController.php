<?php

namespace App\Http\Controllers;

use App\Jobs\DeletePhotoJob;
use App\Models\Photo;
use App\Models\PhotoResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use ZipArchive;

class PhotoController extends Controller
{
    protected function buildDownloadItems(Photo $photo): array
    {
        $items = [];

        if ($photo->photo_template) {
            $items[] = [
                'key' => 'template',
                'type' => 'template',
                'label' => 'Template Foto',
                'name' => basename($photo->photo_template),
                'url' => $photo->photo_template_url,
                'download_name' => basename($photo->photo_template),
                'download_url' => '/download/' . $photo->download_token . '/item/template',
            ];
        }

        foreach ($photo->photoResults as $result) {
            if (!$result->photo_ori) {
                continue;
            }

            $items[] = [
                'key' => 'photo-' . $result->id,
                'type' => 'photo',
                'label' => 'Foto Hasil ' . $result->photo_order,
                'name' => basename($result->photo_ori),
                'url' => $result->photo_ori_url,
                'download_name' => basename($result->photo_ori),
                'download_url' => '/download/' . $photo->download_token . '/item/photo/' . $result->id,
            ];
        }

        if ($photo->gif_vidio) {
            $items[] = [
                'key' => 'video',
                'type' => 'video',
                'label' => 'Video / GIF',
                'name' => basename($photo->gif_vidio),
                'url' => $photo->gif_vidio_url,
                'download_name' => basename($photo->gif_vidio),
                'download_url' => '/download/' . $photo->download_token . '/item/video',
            ];
        }

        return $items;
    }

    protected function formatBytes(int $bytes): string
    {
        if ($bytes < 1024) {
            return $bytes.' B';
        }

        if ($bytes < 1048576) {
            return round($bytes / 1024, 2).' KB';
        }

        if ($bytes < 1073741824) {
            return round($bytes / 1048576, 2).' MB';
        }

        return round($bytes / 1073741824, 2).' GB';
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            $results = Photo::with('photoResults')->latest()->get();

            return response()->json([
                'message' => 'data berhasil diambil',
                'data' => $results
            ], 200);
        }catch(\Exception $e){
            return response()->json([
                'message' => 'data gagal diambil',
                'error' => $e->getMessage()
            ], 500);
        }
    }





    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try{

        $request->validate([
            'photo_template' => 'required|image|mimes:png,jpg,jpeg,bmp,heic,heif',
            'photo_ori' => 'required|array',
            'photo_ori.*' => 'required|image|mimes:png,jpg,jpeg,bmp,heic,heif',
        ]);

        DB::beginTransaction();

        // upload template
        $templatePath = $request->file('photo_template')
            ->store('photobooth/template', 'public');


        // simpan photo
        $photo = Photo::create([
            'photo_template' => $templatePath,
            'gif_vidio' => null,
            'download_token' => Str::random(10),
        ]);

        // upload hasil foto
        if ($request->hasFile('photo_ori')) {

            foreach ($request->file('photo_ori') as $index => $file) {

                $path = $file->store('photobooth/ori', 'public');

                PhotoResult::create([
                    'photo_id' => $photo->id,
                    'photo_ori' => $path,
                    'photo_order' => $index + 1
                ]);
            }
        }

        DB::commit();

        $photo ->load('photoResults');

        return response()->json([
            'message' => 'Data berhasil disimpan',
            'data' => [
                'id' => $photo->id,
                'token' => $photo->download_token,
            ]
        ]);

        }catch(\Exception $e){
            return response()->json([
                'message' => 'data gagal disimpan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function uploadVideo(Request $request, Photo $photo)
    {
        try {
            $request->validate([
                'gif_vidio' => 'required|file',
            ]);

            $oldPath = $photo->gif_vidio;

            $videoPath = $request->file('gif_vidio')
                ->store('photobooth/video', 'public');

            $photo->update([
                'gif_vidio' => $videoPath,
            ]);

            if ($oldPath && Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }

            return response()->json([
                'message' => 'video berhasil diupload',
                'data' => [
                    'id' => $photo->id,
                    'token' => $photo->download_token,
                    'gif_vidio' => $photo->gif_vidio,
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'video gagal diupload',
                'error' => $e->getMessage(),
            ], 500);
        }
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Photo $photo)
    {
        try {
            $photo->load('photoResults');

            $filePaths = collect([
                $photo->photo_template,
                $photo->gif_vidio,
            ])
                ->merge($photo->photoResults->pluck('photo_ori'))
                ->filter()
                ->unique()
                ->values()
                ->all();

            DB::beginTransaction();

            $photo->delete();

            DB::commit();

            if (!empty($filePaths)) {
                Storage::disk('public')->delete($filePaths);
            }

            return response()->json([
                'message' => 'data berhasil dihapus',
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'data gagal dihapus',
                'error' => $e->getMessage(),
            ], 500);
        }

    }

    public function destroyAll()
    {
        try {
            $photos = Photo::with('photoResults')->get();

            $filePaths = $photos->flatMap(function ($photo) {
                return collect([
                    $photo->photo_template,
                    $photo->gif_vidio,
                ])->merge($photo->photoResults->pluck('photo_ori'));
            })
                ->filter()
                ->unique()
                ->values()
                ->all();

            DB::beginTransaction();

            PhotoResult::query()->delete();
            Photo::query()->delete();

            DB::commit();

            if (!empty($filePaths)) {
                Storage::disk('public')->delete($filePaths);
            }

            return response()->json([
                'message' => 'semua data berhasil dihapus',
                'total_deleted_files' => count($filePaths),
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'gagal menghapus semua data',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function storageStats()
    {
        try {
            $photos = Photo::with('photoResults')->get();

            $filePaths = $photos->flatMap(function ($photo) {
                return collect([
                    $photo->photo_template,
                    $photo->gif_vidio,
                ])->merge($photo->photoResults->pluck('photo_ori'));
            })
                ->filter()
                ->unique()
                ->values();

            $existingFiles = $filePaths->filter(fn ($path) => Storage::disk('public')->exists($path));

            $totalSizeBytes = $existingFiles->sum(fn ($path) => Storage::disk('public')->size($path));

            return response()->json([
                'message' => 'statistik file berhasil diambil',
                'data' => [
                    'total_files' => $existingFiles->count(),
                    'total_size_bytes' => $totalSizeBytes,
                    'total_size' => $this->formatBytes($totalSizeBytes),
                    'missing_files' => $filePaths->count() - $existingFiles->count(),
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'statistik file gagal diambil',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function downloadPortal($token)
    {
        $photo = Photo::with('photoResults')
            ->where('download_token', $token)
            ->firstOrFail();

        if ($photo->expired_at && now()->greaterThan($photo->expired_at)) {
            abort(404, 'QR sudah expired');
        }


        return response()->view('downloads.portal', [
            'photo' => $photo,
            'downloadItems' => $this->buildDownloadItems($photo),
            'archiveUrl' => '/api/download/' . $token . '/zip',
        ]);
    }

    public function downloadArchive($token)
    {


        $photo = Photo::with('photoResults')
            ->where('download_token', $token)
            ->firstOrFail();


        if ($photo->expired_at && now()->greaterThan($photo->expired_at)) {
            abort(404, 'QR sudah expired');
        }

        $zipFileName = 'boothera'.'.zip';
        $zipPath = storage_path('app/public/'.$zipFileName);

        $zip = new ZipArchive;

        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {

            // masukkan template
            if ($photo->photo_template && Storage::disk('public')->exists($photo->photo_template)) {
                $zip->addFile(
                    storage_path('app/public/'.$photo->photo_template),
                    'template/'.basename($photo->photo_template)
                );
            }

            // masukkan semua foto hasil
            foreach ($photo->photoResults as $result) {

                if (Storage::disk('public')->exists($result->photo_ori)) {

                    $zip->addFile(
                        storage_path('app/public/'.$result->photo_ori),
                        'photos/'.basename($result->photo_ori)
                    );
                }
            }

            $zip->close();
        }

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }

    public function downloadItem(string $token, string $type, ?PhotoResult $photoResult = null)
    {
        $photo = Photo::with('photoResults')
            ->where('download_token', $token)
            ->firstOrFail();

        if ($photo->expired_at && now()->greaterThan($photo->expired_at)) {
            abort(404, 'QR sudah expired');
        }

        $disk = Storage::disk('public');

        $path = match ($type) {
            'template' => $photo->photo_template,
            'video' => $photo->gif_vidio,
            'photo' => $photoResult?->photo_id === $photo->id ? $photoResult->photo_ori : null,
            default => null,
        };

        if (!$path || !$disk->exists($path)) {
            return response()->json([
                'message' => 'file tidak ditemukan',
            ], 404);
        }

        return response()->download($disk->path($path), basename($path));
    }

    public function downloadVideo($token)
    {
        $photo = Photo::where('download_token', $token)->firstOrFail();

        if ($photo->expired_at && now()->greaterThan($photo->expired_at)) {
            abort(404, 'QR sudah expired');
        }

        if (!$photo->gif_vidio || !Storage::disk('public')->exists($photo->gif_vidio)) {
            return response()->json([
                'message' => 'video tidak ditemukan',
            ], 404);
        }

        $videoPath = storage_path('app/public/'.$photo->gif_vidio);

        return response()->download($videoPath, basename($photo->gif_vidio));
    }

    public function qr($token)
    {

        $photo = Photo::where('download_token', $token)->firstOrFail();

        if (!$photo->expired_at) {

            $expired = now()->addHours(1);

            $photo->update([
                'expired_at' => $expired
            ]);

            DeletePhotoJob::dispatch($photo->id)->delay($expired);
        }

        $url = url('/download/' . $token);

        return response()->json([
            'message' => 'QR foto berhasil dibuat',
            'expired_at' => optional($photo->expired_at)
                ?->timezone('Asia/Jakarta')
                ?->format('Y-m-d H:i:s'),
            'download_url' => $url,
            'qr_image_url' => url('/api/download/'.$token.'/qr-image'),
        ]);
    }

    public function qrImage($token)
    {
        $url = url('/download/' . $token);

        $qr = QrCode::format('svg')
            ->size(400)
            ->generate($url);

        return response($qr)->header('Content-Type', 'image/svg+xml');
    }

    public function qrVideo($token)
    {
        $photo = Photo::where('download_token', $token)->firstOrFail();

        if (!$photo->gif_vidio || !Storage::disk('public')->exists($photo->gif_vidio)) {
            return response()->json([
                'message' => 'video tidak ditemukan',
            ], 404);
        }

        $url = url('/download/' . $token);

        return response()->json([
            'message' => 'QR video berhasil dibuat',
            'download_url' => $url,
            'qr_image_url' => url('/api/download/'.$token.'/video/qr-image'),
        ]);
    }

    public function qrImageVideo($token)
    {
        $photo = Photo::where('download_token', $token)->firstOrFail();

        if (!$photo->gif_vidio || !Storage::disk('public')->exists($photo->gif_vidio)) {
            return response()->json([
                'message' => 'video tidak ditemukan',
            ], 404);
        }

        $url = url('/api/download/'.$token.'/video');

        $qr = QrCode::format('svg')
            ->size(400)
            ->generate($url);

        return response($qr)->header('Content-Type', 'image/svg+xml');
    }
}
