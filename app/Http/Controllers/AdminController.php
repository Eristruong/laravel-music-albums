<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Album;
use App\Models\Music;
use App\Models\User;
use Illuminate\Support\Str;
use Intervention\Image\ImageManagerStatic as Image;

class AdminController extends Controller
{
    public function index()
    {
       
        $musics = Music::orderBy('id', 'DESC')->take(1)->get();
   
        return view('admin.dashboard', compact('musics'));
    }

    public function music()
    {
        
        $musics = Music::orderBy('id', 'DESC')->get();
        return view('admin.music', compact('musics'));
    }
    public function deletemusic(Request $request)
    {
        $music = Music::find($request->input('id'));
        //Deleting the old music
        if (file_exists(public_path('images/albumart/' . $music->image)) && file_exists(public_path('images/thumbnails/' . $music->image)) && file_exists(public_path('songs/' . $music->song))) {
            $oldimage = public_path('images/albumart/' . $music->image);
            unlink($oldimage);
            $oldmusic = public_path('songs/' . $music->song);
            unlink($oldmusic);
            unlink(public_path('images/thumbnails/' . $music->image));
        }

        $music->delete();
        return ['message' => 'success'];
    }

    public function editmusic(Request $request)
    {
        $this->validate($request, [
            'title' => 'required',
            'song' => 'required',
            'album' => 'required',
            'year' => 'required',
            'id' => 'required',
            'content' => 'required',
            'song' => 'sometimes|min:1000|mimes:mp3,mpga',
            'image' => 'sometimes|image|max:2000',
        ]);
        $music = Music::find($request->input('id'));


        //Uploading music album art
        if ($request->hasFile('image')) {

            if (file_exists(public_path('images/albumart/' . $music->image)) && file_exists(public_path('images/thumbnails/' . $music->image))) {
                unlink(public_path('images/albumart/' . $music->image));
                unlink(public_path('images/thumbnails/' . $music->image));
            }


            $file = $request->file('image');
            $ext = $file->getClientOriginalExtension();
            $img = 'art' . time() . '.' . $ext;
            $destination = public_path("images/albumart");
            $file->move($destination, $img);

            //Resizing Image
            $image_resize = Image::make($destination . '/' . $img);
            $image_resize->resize(300, 300);
            $image_resize->save(public_path('images/thumbnails/' . $img));
        }

        //Uploading music album art
        if ($request->hasFile('song')) {
            if (file_exists(public_path('songs/' . $music->song))) {
                unlink(public_path('songs/' . $music->song));
            }

            $file = $request->file('song');
            $ext = $file->getClientOriginalExtension();
            $songname = Str::slug($request->input('title'));
            $filename = $songname . '.' . $ext;
            $destination = public_path("songs");
            $file->move($destination, $filename);
        }

        $music->title = $request->input('title');
        $music->content = $request->input('content');
        if ($request->hasFile('image')) {
            $music->image = $img;
        }
        $music->artist = $request->input('artist');
        if ($request->hasFile('song')) {
            $music->song = $filename;
        }
//        $music->slug = getslug($request->input('title'));
        $music->year = $request->input('year');
        $music->album_id = $request->input('album');
        $music->save();
        return ['message' => 'success'];
    }

    public function addmusic(Request $request)
    {
        $this->validate($request, [
            'title' => 'required',
            'song' => 'required',
            'album' => 'required',
            'year' => 'required',
            'content' => 'required',
            'song' => 'required|min:1000|mimes:mp3,mpga',
            'image' => 'required|image|max:2000',
        ]);

        //Uploading music album art
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $ext = $file->getClientOriginalExtension();
            $img = 'art' . time() . '.' . $ext;
            $destination = public_path("images/albumart");
            $file->move($destination, $img);

            //Resizing Image
            $image_resize = Image::make($destination . '/' . $img);
            $image_resize->resize(300, 300);
            $image_resize->save(public_path('images/thumbnails/' . $img));
        }

        //Uploading music album art
        if ($request->hasFile('song')) {
            $file = $request->file('song');
            $ext = $file->getClientOriginalExtension();
            $songname = Str::slug($request->input('title'));
            $filename = $songname . '.' . $ext;
            $destination = public_path("songs");
            $file->move($destination, $filename);
        }
   
        $music = new Music;
        $music->title = $request->input('title');
        $music->content = $request->input('content');
        $music->image = $img;
        $music->artist = $request->input('artist');
        $music->song = $filename;
        $music->year = $request->input('year');
        $music->slug = Str::slug($request->input('title'));
        $music->album_id = $request->input('album');
        $music->save();
        return ['message' => 'success'];
    }
}
