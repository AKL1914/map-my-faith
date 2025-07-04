<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Photo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PhotoController extends Controller
{
    public function manage()
    {
        return view('admin.photos.index');
    }

    public function index()
    {
        return Photo::all(); // Now includes 'url' attribute
    }

    public function store(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|max:5120',
            'visible' => 'required|boolean',
        ]);

        $path = $request->file('photo')->store('photos', 'public');
        $photo = Photo::create([
            'filename' => $path,
            'visible' => $request->visible,
        ]);
        return response()->json($photo, 201);
    }

    public function update(Request $request, $id)
    {
        $photo = Photo::findOrFail($id);
        $request->validate([
            'visible' => 'required|boolean',
        ]);
        $photo->update(['visible' => $request->visible]);
        return response()->json($photo);
    }

    public function destroy($id)
    {
        $photo = Photo::findOrFail($id);
        Storage::disk('public')->delete($photo->filename);
        $photo->delete();
        return response()->json(['message' => 'Photo deleted']);
    }
}
