<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ClassRoom;


class Live extends Model
{
    use HasFactory;

protected $fillable = [
        'title',
        'class_id',
        'stream_url',
        'provider',
        'admin_id',
        'user_id',
        'live_date',
        'start_time',
        'end_time',
    ];




    // Relation vers la classe
    public function classRoom()
    {
        return $this->belongsTo(ClassRoom::class, 'class_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Les liens partagés par Teams personnel passent parfois par un lanceur
     * temporaire. Retourner directement l'URL de réunion évite les erreurs
     * d'ouverture lorsque ce lanceur n'est pas accessible.
     */
    public function getStreamUrlAttribute($value)
    {
        if (!$value || strtolower((string) parse_url($value, PHP_URL_HOST)) !== 'teams.live.com') {
            return $value;
        }

        if (parse_url($value, PHP_URL_PATH) !== '/dl/launcher/launcher.html') {
            return $value;
        }

        parse_str((string) parse_url($value, PHP_URL_QUERY), $query);
        $meetingPath = $query['url'] ?? null;

        if (!$meetingPath || !str_starts_with($meetingPath, '/_#/meet/')) {
            return $value;
        }

        return 'https://teams.live.com/' . ltrim(str_replace('/_#/', '/', $meetingPath), '/');
    }

    public function getTeamsAppUrlAttribute()
    {
        $url = $this->stream_url;
        $host = strtolower((string) parse_url($url, PHP_URL_HOST));

        if (!in_array($host, ['teams.microsoft.com', 'teams.live.com'], true)) {
            return null;
        }

        return preg_replace('#^https://#i', 'msteams://', $url);
    }
}
