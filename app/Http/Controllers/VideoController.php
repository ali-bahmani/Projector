<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Video;
use FFMpeg\FFProbe;
use Illuminate\Support\Facades\Storage;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg as FFmpeg;
use FFMpeg\Filters\Video\VideoFilters;
class VideoController extends Controller
{
    protected $size;
    public function uploadVideo(Request $request){

        $video = $request->validate([
            'video' => 'required|mimes:mp4,avi,mkv,mov,ogg|max:100000',
        ]);

        
        $name = now()->format('Y-m-d').'-'.$request->file('video')->getClientOriginalName();

        $video = new Video;

        $video->path = Storage::disk('public')->putFileAs('video',$request->file('video'),$name);
        $video->disk = 'public';
        $video->video_default_format = $request->file('video')->getClientOriginalExtension();

        $ffprob = FFProbe::create([
            'ffmpeg.binaries'  => 'C:/ffmpeg/bin/ffmpeg.exe',
            'ffprobe.binaries' => 'C:/ffmpeg/bin/ffprobe.exe'
            
        ]);
        $video_dimension = $ffprob
            ->streams(public_path('storage').'/'.$video->path)
            ->videos()
            ->first()
            ->getDimensions();
        $witdh = $video_dimension->getWidth();
        $height = $video_dimension->getHeight();

        $video->video_default_res = $witdh.'.'.$height;
        
        if($video->save()){
            if($request->type == 'cRes'){
                return $this->cRes($video,$request->resolution);
            }
            if($request->type == 'cFormat'){
                return $this->cFormat($video,$request->format);
            }
            if($request->type == 'cThumbnail'){
                return $this->cThumbnail($video,$request->second);
            }
        }else{
            return 'erore in saving';
        } 
        
    }

    public function cRes(Video $video,$res)
    {
        $video->converted_at = now()->format('Y-m-d');
        $path = 'Resolutions/'.now()->format('Y-m-d').$video->id.'.'.$video->video_default_format;
        $this->size = explode(".",$res);
        FFMpeg::fromDisk($video->disk)
            ->open($video->path)
            ->addFilter(function (VideoFilters $filters) {
                $filters->resize(new \FFMpeg\Coordinate\Dimension($this->size[0], $this->size[1]));
            })
            ->export()
            ->toDisk($video->disk)
            ->inFormat(new \FFMpeg\Format\Video\X264)
            ->save($path);
        $video->url = Storage::disk($video->disk)->url($path);
        $video->save();

        return back()->with('link',$video->url);
    }

    public function cFormat(Video $video,$format)
    {
        $video->converted_at = now()->format('Y-m-d');
        $path = 'Formats/'.now()->format('Y-m-d').$video->id.'.'.$format;

        FFMpeg::fromDisk($video->disk)
            ->open($video->path)
            ->export()
            ->inFormat($format == 'mp3' ? new \FFMpeg\Format\Audio\Mp3() : new \FFMpeg\Format\Video\X264('libmp3lame', 'libx264'))
            ->save($path);

        $video->url = Storage::disk($video->disk)->url($path);
        $video->save();

        return back()->with('link',$video->url);
    }

    public function cThumbnail(Video $video,$second)
    {
        $video->converted_at = now()->format('Y-m-d');

        $path = 'Thumbnail/'.now()->format('Y-m-d').$video->id.'png';

        FFMpeg::fromDisk($video->disk)
            ->open($video->path)
            ->getFrameFromSeconds($second)
            ->export()
            ->toDisk($video->disk)
            ->save($path);

        $video->url = Storage::disk($video->disk)->url($path);
        $video->save();

        return back()->with('link',$video->url);
    }
}
