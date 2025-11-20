<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\Paciente;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    public function doctorAvatar(int $id)
    {
        $doc = Doctor::find($id);
        if ($doc && $doc->foto_perfil_blob && strlen($doc->foto_perfil_blob) > 100) {
            $mime = $doc->foto_perfil_mime ?: 'image/jpeg';
            $etag = 'W/"'.md5($doc->foto_perfil_blob).'"';
            if (request()->header('If-None-Match') === $etag) {
                return response('', 304, [
                    'ETag' => $etag,
                    'Cache-Control' => 'no-cache, must-revalidate',
                ]);
            }
            return response($doc->foto_perfil_blob, 200, [
                'Content-Type' => $mime,
                'ETag' => $etag,
                'Cache-Control' => 'no-cache, must-revalidate',
            ]);
        }

        // fallback to disk if legacy file exists
        $disk = config('avatar.disk', 'public');
        $folder = config('avatar.folder', 'profile_pics');
        if ($doc && $doc->foto_perfil && Storage::disk($disk)->exists($folder.'/'.$doc->foto_perfil)) {
            $bytes = Storage::disk($disk)->get($folder.'/'.$doc->foto_perfil);
            $mime = $doc->foto_perfil_mime ?: 'image/jpeg';
            $etag = 'W/"'.md5($bytes).'"';
            if (request()->header('If-None-Match') === $etag) {
                return response('', 304, [
                    'ETag' => $etag,
                    'Cache-Control' => 'no-cache, must-revalidate',
                ]);
            }
            return response($bytes, 200, [
                'Content-Type' => $mime,
                'ETag' => $etag,
                'Cache-Control' => 'no-cache, must-revalidate',
            ]);
        }

        return redirect(config('avatar.default_url'));
    }

    public function pacienteAvatar(int $id)
    {
        $pac = Paciente::find($id);
        if ($pac && $pac->foto_perfil_blob && strlen($pac->foto_perfil_blob) > 100) {
            $mime = $pac->foto_perfil_mime ?: 'image/jpeg';
            $etag = 'W/"'.md5($pac->foto_perfil_blob).'"';
            if (request()->header('If-None-Match') === $etag) {
                return response('', 304, [
                    'ETag' => $etag,
                    'Cache-Control' => 'no-cache, must-revalidate',
                ]);
            }
            return response($pac->foto_perfil_blob, 200, [
                'Content-Type' => $mime,
                'ETag' => $etag,
                'Cache-Control' => 'no-cache, must-revalidate',
            ]);
        }

        $disk = config('avatar.disk', 'public');
        $folder = config('avatar.folder', 'profile_pics');
        if ($pac && $pac->foto_perfil && Storage::disk($disk)->exists($folder.'/'.$pac->foto_perfil)) {
            $bytes = Storage::disk($disk)->get($folder.'/'.$pac->foto_perfil);
            $mime = $pac->foto_perfil_mime ?: 'image/jpeg';
            $etag = 'W/"'.md5($bytes).'"';
            if (request()->header('If-None-Match') === $etag) {
                return response('', 304, [
                    'ETag' => $etag,
                    'Cache-Control' => 'no-cache, must-revalidate',
                ]);
            }
            return response($bytes, 200, [
                'Content-Type' => $mime,
                'ETag' => $etag,
                'Cache-Control' => 'no-cache, must-revalidate',
            ]);
        }

        return redirect(config('avatar.default_url'));
    }
}
