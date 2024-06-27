<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Pion\Laravel\ChunkUpload\Receiver\FileReceiver;
use Pion\Laravel\ChunkUpload\Handler\HandlerFactory;
use Storage;
use File;

class CommonController extends Controller
{
    
    public function uploadFile(Request $request){
        
        $receiver = new FileReceiver('file', $request, HandlerFactory::classFromRequest($request));
        if (!$receiver->isUploaded()) {
            // file not uploaded
            return errorMsgResponse('Please try again, Video not uploaded');
        }
        $fileReceived = $receiver->receive(); // receive file
        
        if ($fileReceived->isFinished()) { // file uploading is complete / all chunks are uploaded
            $file = $fileReceived->getFile(); // get file
            $extension = $file->getClientOriginalExtension();
            $fileName = str_replace('.'.$extension, '', str_replace(" ","-",$file->getClientOriginalName())); //file name without extenstion
            $fileName .= '_' . md5(time()) . '.' . $extension; // a unique file name

            $disk = Storage::disk(config('filesystems.disk.public'));
            //$path = $disk->putFileAs('videos', $file, $fileName);

            $path = 'uploads/instructional/videos';
            if(!File::exists(public_path($path))) File::makeDirectory(public_path($path), 0777,true);
            if($file->move(public_path($path),$fileName)){
                // delete chunked file
                @unlink($file->getPathname());
                return response()->json([
                    'path' => 'public/'.$path.'/'.$fileName,
                    'filename' => $fileName,
                    'status'=>true
                ]);
            }

            return errorMsgResponse('Please try again, something is wrong');
            
        }

        // otherwise return percentage information
        $handler = $fileReceived->handler();
        return [
            'done' => $handler->getPercentageDone(),
            'status' => true
        ];
    }

    public function pageContent($slug){
        if($slug=='privacy-policy'){
            return view('page.privacy');
        }else if($slug=='terms-conditions'){
            return view('page.terms');
        }else if($slug=='delete-account'){
            return view('page.delete_account');
        }
        abort(404);
    }
}
