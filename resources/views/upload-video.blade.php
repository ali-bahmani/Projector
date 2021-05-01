<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    {{--    jquery--}}
        <script
            src="https://code.jquery.com/jquery-3.4.1.min.js"
            integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
            crossorigin="anonymous">
        </script>
    {{--end jquery--}}

    {{--    bootstrap--}}
        {{--    css bootstrap--}}
            <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
        {{--    js bootstrap--}}
            <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
            <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    {{--end bootstrap--}}



</head>
<body>
    <div class="container">
        <div class="row justify-content-center mt-5">
        

            <div class="card" style="width: 800px;">
                <div class="card-header">
                    Projector (Video Vonverter)
                </div>
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item" role="presentation"><a class="nav-link active" data-toggle="tab" href="#resolution" >Resolution</a></li>
                    <li class="nav-item" role="presentation"><a class="nav-link" data-toggle="tab" href="#format">Format</a></li>
                    <li class="nav-item" role="presentation"><a class="nav-link" data-toggle="tab" href="#thumbnail">Thumbnail</a></li>
                </ul>
                <!-- tabs -->



                <!-- panels -->
                <div class="tab-content">

                    <div class="card-body tab-pane fade show active" id="resolution" role="tabpanel" aria-labelledby="resolution-tab">
                        <div>
                            <form method="post" action="{{route('upload-video')}}" enctype="multipart/form-data">
                                @csrf

                                <input type="hidden" name="type" value="cRes">

                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="video" name="video" accept="video/*" >
                                    <label for="video" class="custom-file-label">You're video</label>
                                </div>

                                <div class="form-group mt-3">
                                    <label for="resolution">Resolution: </label>
                                    <select id="resolution" name="resolution" class="form-control">
                                        <option selected value="mkv">1920×1080 (1080p)</option>
                                        <option value="1280.720">1280×720 (720)</option>
                                        <option value="640.480">640×480 (480)</option>
                                        <option value="640.360">640×360 (360p)</option>
                                        <option value="320.240">320×240 (240p)</option>
                                    </select>
                                </div>

                                <div class="form-group mt-1">
                                    <button type="submit" class="btn btn-danger btn-block">Submit</button>
                                </div>
                            </form>
                        </div>


                    </div>

                    <div class="card-body tab-pane fade" id="format" role="tabpanel" aria-labelledby="format-tab">
                        <div>
                            <form method="post" action="{{route('upload-video')}}" enctype="multipart/form-data">
                                @csrf
                                
                                <input type="hidden" name="type" value="cFormat">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="video" name="video" >
                                    <label for="video" class="custom-file-label">You're video</label>
                                </div>

                                <div class="form-group mt-3">
                                    <label for="format">Format: </label>
                                    <select id="format" name="format" class="form-control">
                                        <option selected value="mp4">mp4</option>
                                        <option value="mkv">mkv</option>
                                        <option value="avi">avi</option>
                                        <option value="mov">mov</option>
                                        <option value="mp3">mp3</option>
                                    </select>
                                </div>

                                <div class="form-group mt-1">
                                    <button type="submit" class="btn btn-warning btn-block">Submit</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="card-body tab-pane fade" id="thumbnail" role="tabpanel" aria-labelledby="thumbnail-tab">
                        <div>
                            <form method="post" action="{{route('upload-video')}}" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="type" value="cThumbnail">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="video" name="video" accept="video/*" >
                                    <label for="video" class="custom-file-label">You're video</label>
                                </div>

                                <div class="form-group mt-3">
                                    <label for="second">second: </label>
                                    <input type="text" class="form-control" id="second" name="second" required>
                                </div>

                                <div class="form-group mt-1">
                                    <button type="submit" class="btn btn-success btn-block">Submit</button>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>

</body>
</html>
