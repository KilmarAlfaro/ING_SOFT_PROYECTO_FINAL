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
        if ($doc && $doc->foto_perfil_blob) {
            $mime = $doc->foto_perfil_mime ?: 'image/jpeg';
            return response($doc->foto_perfil_blob, 200, [
                'Content-Type' => $mime,
                'Cache-Control' => 'public, max-age=604800',
            ]);
        }

        // fallback to disk if legacy file exists
        $disk = config('avatar.disk', 'public');
        $folder = config('avatar.folder', 'profile_pics');
        if ($doc && $doc->foto_perfil && Storage::disk($disk)->exists($folder.'/'.$doc->foto_perfil)) {
            // Stream the file (works for local and most remote disks)
            return Storage::disk($disk)->response($folder.'/'.$doc->foto_perfil);
        }

        return redirect(config('avatar.default_url'));
    }

    public function pacienteAvatar(int $id)
    {
        $pac = Paciente::find($id);
        if ($pac && $pac->foto_perfil_blob) {
            $mime = $pac->foto_perfil_mime ?: 'image/jpeg';
            return response($pac->foto_perfil_blob, 200, [
                'Content-Type' => $mime,
                'Cache-Control' => 'public, max-age=604800',
            ]);
        }

        $disk = config('avatar.disk', 'public');
        $folder = config('avatar.folder', 'profile_pics');
        if ($pac && $pac->foto_perfil && Storage::disk($disk)->exists($folder.'/'.$pac->foto_perfil)) {
            return Storage::disk($disk)->response($folder.'/'.$pac->foto_perfil);
        }

        return redirect(config('avatar.default_url'));
    }
}
