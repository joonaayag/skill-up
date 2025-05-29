<?php

namespace App\Http\Controllers;

use App\Models\ProjectImage;
use Illuminate\Http\Request;
use Storage;

class ImageController extends Controller
{
    public function destroy($id)
    {
        $image = ProjectImage::findOrFail($id);

        if (Storage::disk('s3')->exists($image->path)) {
            Storage::disk('s3')->delete($image->path);
        }

        $image->delete();

        return redirect()->back()->with('success', 'Archivo eliminado correctamente.');
    }
}
