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

        if (Storage::disk('public')->exists($image->path)) {
            Storage::disk('public')->delete($image->path);
        }

        $image->delete();

        return redirect()->back()->with('success', 'Archivo eliminado correctamente.');
    }
}
