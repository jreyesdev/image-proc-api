<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ResizeImageRequest;
use App\Http\Resources\V1\ImageManipulationResource;
use App\Models\Album;
use App\Models\ImageManipulation;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Intervention\Image\ImageManagerStatic as Image;

class ImageManipulationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return ImageManipulationResource::collection(ImageManipulation::paginate());
    }

    /**
     * Display a listing of the images by album
     * @return \Illuminate\Http\Response
     */
    public function byAlbum(Album $album)
    {
        return ImageManipulationResource::collection(ImageManipulation::whereAlbumId($album->id)->paginate());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreImageManipulationRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function resize(ResizeImageRequest $request)
    {
        $all = $request->all();

        /** @var UploadedFile|string */
        $image = $all['image'];

        $data = [
            'type' => ImageManipulation::TYPE_RESIZE,
            'data' => json_encode($all),
            'user_id' => null,
        ];

        if (isset($all['album_id'])) {
            $data['album_id'] = $all['album_id'];
        }

        $dir = 'images/' . Str::random() . '/';
        $absPath = public_path($dir);
        File::makeDirectory($absPath);

        if ($image instanceof UploadedFile) {
            $data['name'] = $image->getClientOriginalName();
            // test.jpg -> test-resized.jpg
            $file = pathinfo($data['name'], PATHINFO_FILENAME);
            $ext = $image->guessClientExtension();
            $originalPath = $absPath . $data['name'];
            $image->move($absPath, $data['name']);
        } else {
            $data['name'] = pathinfo($image, PATHINFO_FILENAME);
            $file = $data['name'];
            $ext = pathinfo($image, PATHINFO_EXTENSION);
            $originalPath = $absPath . $data['name'];
            copy($image, $originalPath);
        }

        $data['path'] = $dir . $data['name'];

        $w = $all['w'];
        $h = $all['h'] ?? false;

        list($width, $height, $img) = $this->getDimensionsImage($w, $h, $originalPath);

        $resizedFileName = $file . '-resized.' . $ext;
        $img->resize($width, $height)->save($absPath . $resizedFileName);

        $data['output_path'] = $dir . $resizedFileName;

        $imageManipulation = ImageManipulation::create($data);

        return ImageManipulationResource::make($imageManipulation);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ImageManipulation  $imageManipulation
     * @return \Illuminate\Http\Response
     */
    public function show(ImageManipulation $image)
    {
        return ImageManipulationResource::make($image);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ImageManipulation  $imageManipulation
     * @return \Illuminate\Http\Response
     */
    public function destroy(ImageManipulation $image)
    {
        $image->delete();
        return response('', 204);
    }

    /**
     * Retreive dimensions image
     */
    protected function getDimensionsImage($w, $h, string $originalPath)
    {
        $image = Image::make($originalPath);
        $originalWidth = $image->width();
        $originalheigth = $image->height();

        if (str_ends_with($w, '%')) {
            $ratioW = (float) str_replace('%', '', $w);
            $ratioH = $h ? (float) str_replace('%', '', $h) : $ratioW;
            $newWidth = $originalWidth * $ratioW / 100;
            $newHeigth = $originalheigth * $ratioH / 100;
        } else {
            $newWidth = (float) $w;
            $newHeigth = $h ? (float) $h : $originalheigth * $newWidth / $originalWidth;
        }

        return [$newWidth, $newHeigth, $image];
    }
}
