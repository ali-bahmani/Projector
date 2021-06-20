<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Video;
use FFMpeg\FFProbe;
use Illuminate\Support\Facades\Storage;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg as FFmpeg;
use FFMpeg\Filters\Video\VideoFilters;

class ProcessVideo implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $video,$input;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Video $video,$input)
    {
        $this->input = $input;
        $this->video = $video;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if($this->input['type'] == 'res'){
            return $this->cRes($this->video,$this->input['data']);
        }
        elseif($this->input['type'] == 'format'){
            return $this->cFormat($this->video,$this->input['data']);
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
        if($video->save()){
            return true;
        }
        return false;

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
        if($video->save()){
            return true;
        }
        return false;
    }
}
