<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Video;
use FFMpeg\FFProbe;
use Illuminate\Support\Facades\Storage;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg as FFmpeg;
use FFMpeg\Filters\Video\VideoFilters;
use App\Jobs\ProcessVideo;

class VideoController extends Controller
{
    protected $size;
    public function uploadVideo(Request $request){

        // video validation
        $video = $request->validate([
            'video' => 'required|mimetypes:video/x-ms-asf,video/x-flv,video/mp4,application/x-mpegURL,video/MP2T,video/3gpp,video/quicktime,video/x-msvideo,video/x-ms-wmv,video/avi|max:100000',
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

        // get default witdh & height video
        $video_dimension = $ffprob
            ->streams(public_path('storage').'/'.$video->path)
            ->videos()
            ->first()
            ->getDimensions();
        $witdh = $video_dimension->getWidth();
        $height = $video_dimension->getHeight();

        $video->video_default_res = $witdh.'.'.$height;
        
        if($video->save()){
            switch ($request->type) {
                case 'cRes':
                    $input = ['type'=>'res','data'=>$request->resolution];
                    ProcessVideo::dispatch($video,$input);
                    return back()->with('link',$video->url);

                    break;
                
                case 'cFormat':
                    $input = ['type'=>'format','data'=>$request->format];
                    ProcessVideo::dispatch($video,$input);
                    return back()->with('link',$video->url);
                    
                    break;
                
                case 'cThumbnail':
                    return $this->cThumbnail($video,$request->second);
                    
                    break;
            }

        }else{
            return 'erore in saving';
        } 
        
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
