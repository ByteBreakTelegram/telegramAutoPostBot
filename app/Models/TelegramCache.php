<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class TelegramCache
 * 
 * @property int $id
 * @property int $user_id
 * @property string $key
 * @property array $value
 * @property int $created_at
 * @property int $updated_at
 * 
 * @property User $user
 *
 * @package App\Models
 */
class TelegramCache extends Model
{
	protected $table = 'telegram_cache';

    /**
     * @var string
     */
    protected $dateFormat = 'U';
	protected $casts = [
		'user_id' => 'int',
		'key' => 'string',
		'value' => 'array',
	];

	protected $fillable = [
		'user_id',
		'key',
		'value'
	];

	public function user()
	{
		return $this->belongsTo(User::class);
	}

    public static function setValue(int $userId, string $key, array $value): string
    {
        $model = TelegramCache::query()->where('user_id', $userId)->where('key', $key)->first();
        if (!$model) {
            $model = new TelegramCache();
            $model->user_id = $userId;
            $model->key = $key;
        }
        $model->value = $value;
        $model->save();
        return $key;
    }

    public static function getValue(int $userId, string $key): ?array
    {
        return TelegramCache::query()->where('user_id', $userId)->where('key', $key)->value('value');
    }

    public static function deleteValue(int $userId, string $key): void
    {
        TelegramCache::query()->where('user_id', $userId)->where('key', $key)->delete();
    }
}
