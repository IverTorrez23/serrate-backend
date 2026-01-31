<?php

namespace App\Services;

use App\Constants\Estado;
use App\Models\Video;
use Illuminate\Http\Request;

class VideoService
{
    public function store($data)
    {
        $video = Video::create([
            'link' => $data['link'],
            'titulo' => $data['titulo'],
            'descripcion' => $data['descripcion'],
            'tipo' => $data['tipo'],
            'estado' => Estado::ACTIVO,
            'es_eliminado' => 0,

        ]);
        return $video;
    }
    public function update($data, $videoId)
    {
        $video = Video::findOrFail($videoId);
        $video->update($data);
        return $video;
    }
    public function listarActivosPublico()
    {
        $videos = Video::where('estado', Estado::ACTIVO)
            ->where('es_eliminado', 0)
            ->where('tipo', 'PUBLICO')
            ->get();
        return $videos;
    }
    public function listarActivosProcurador()
    {
        $videos = Video::where('estado', Estado::ACTIVO)
            ->where('es_eliminado', 0)
            ->where('tipo', 'PROCURADOR')
            ->get();
        return $videos;
    }
    public function listarActivosAbogado()
    {
        $videos = Video::where('estado', Estado::ACTIVO)
            ->where('es_eliminado', 0)
            ->where('tipo', 'ABOGADO')
            ->get();
        return $videos;
    }
    public function destroy($videoId)
    {
        $video = Video::findOrFail($videoId);
        $video->es_eliminado = 1;
        $video->save();
        return $video;
    }
    public function obtenerUno($videoId)
    {
        $video = Video::findOrFail($videoId);
        return $video;
    }
    public function transformarUrlYoutube($url)
    {
        if (preg_match('/v=([^&]+)/', $url, $matches)) {
            $videoId = $matches[1];
            $embedUrl = "https://www.youtube.com/embed/" . $videoId;
            return $embedUrl;
        }
        return $url;
    }
}
