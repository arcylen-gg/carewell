<?php
namespace App\Globals;

use App\Models\Document;
use Aws\S3\S3Client;
use League\Flysystem\AwsS3v3\AwsS3Adapter;
use League\Flysystem\Filesystem;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use Carbon\Carbon;

class DocumentService
{
    public static function upload($parent = 0)
    {
        $image_ids      = [];
        $extensions     = request()->get('extensions');
        $sizes          = request()->get('sizes');
        $names          = request()->get('names');
        $is_uploaded    = request()->get('is_uploaded');
        $image_types    = ["PNG", "JPEG", "JPG", "GIF", "BMP"];
        $path_prefix    = "https://digima.sgp1.digitaloceanspaces.com/";

        foreach (request()->file('files') as $key => $value)
        {
            if(!$is_uploaded[$key])
            {
                $timestamp_file = substr(str_shuffle(MD5(microtime())), 0, 7) ."_".$names[$key];
                $file_suffix = Storage::disk('s3')->put(request()->get('folder'), $value, "public");
                
                $id = Document::insertGetId([
                    'name'              => $timestamp_file,
                    'source'            => $path_prefix . $file_suffix,
                    'extension'         => $extensions[$key],
                    'image'             => in_array($extensions[$key], $image_types) ? 1 : 0,
                    'size'              => $sizes[$key],
                    'sourceable_type'   => request()->get('folder'),
                    'sourceable_id'     => $parent,
                    'created_at'        => Carbon::now(),
                    'updated_at'        => Carbon::now()
                ]);
            }
        }
    }
    
}