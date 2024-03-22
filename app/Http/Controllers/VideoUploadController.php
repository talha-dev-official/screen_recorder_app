<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Video;

class VideoUploadController extends Controller
{
    public function index()
    {
    $videos = Video::all();
    return response()->json($videos);
    }
    public function store(Request $request)
    {
    if ($request->hasFile('video')) {
    if ($request->file('video')->isValid()) {
        
    $path = $request->file('video')->store('videos', 'public');

    $video = new Video;
    $video->name = $request->file('video')->getClientOriginalName();
    $video->path = $path;
    $video->save();

    return response()->json(['message' => 'Video uploaded successfully!', 'path' => $path], 200);
    } else {
    return response()->json(['message' => 'Upload error'], 500);
    }
    }

    return response()->json(['message' => 'No video uploaded'], 400);
    }
}
