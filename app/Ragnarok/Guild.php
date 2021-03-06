<?php

namespace App\Ragnarok;

use App\Emblem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\File;

/**
 * @property int $guild_id
 * @property int $char_id
 * @property string $name
 * @property string $master
 * @property boolean $guild_lv
 * @property boolean $connect_member
 * @property boolean $max_member
 * @property integer $average_lv
 * @property integer $exp
 * @property int $next_exp
 * @property boolean $skill_point
 * @property string $mes1
 * @property string $mes2
 * @property int $emblem_len
 * @property int $emblem_id
 * @property string $emblem_data
 * @property string $emblem
 * @property string $last_master_change
 */
class Guild extends Model
{
     /**
     * The connection name for the model.
     *
     * @var string|null
     */
    protected $connection = 'main';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'guild_id';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'guild';

    /**
     * @var array
     */
    protected $fillable = ['name', 'master', 'guild_lv', 'connect_member', 'max_member', 'average_lv', 'exp', 'next_exp', 'skill_point', 'mes1', 'mes2', 'emblem_len', 'emblem_id', 'emblem_data', 'last_master_change'];

    /**
     * Guild can havbe many castles.
     */
    public function Castles()
    {
        return $this->hasMany(GuildCastle::class, 'guild_id', 'guild_id');
    }

    public function hasEmblem()
    {
        return $this->emblem_data && $this->emblem_len && $this->guild_id;
    }

    public function getEmblemAttribute()
    {
        return Cache::remember("emblem.{$this->guild_id}", now()->addMinutes(1), function ()
        {
            $asset = "assets/emblems/{$this->guild_id}.png";

            $data = @gzuncompress(pack('H*', $this->emblem_data));

            header("Content-Type: image/png");

            imagepng(Emblem::create($data), $asset);

            return url($asset);
        });
    }

    public function Members()
    {
        return $this->hasMany(GuildMember::class, 'guild_id', 'guild_id');
    }
}
