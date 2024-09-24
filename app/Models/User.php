<?php

namespace App\Models;

use App\Helpers\LanguageEnumHelper;
use App\Models\Enums\UserRole;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * @property integer $id
 * @property string $name
 * @property string $lname
 * @property string $telegram_username
 * @property string $telegram_chat_id
 * @property LanguageEnumHelper $language_code
 * @property boolean $is_premium
 * @property boolean $is_bot
 * @property UserRole $role_const
 * @property int $parent_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * @var string
     */
    public $table = 'users';

    /**
     * @var string
     */
    protected $dateFormat = 'U';

    public $fillable = [
        'name',
        'telegram_username',
        'telegram_chat_id',
        'role_const',
        'lname',
        'language_code',
        'is_premium',
        'is_bot',
        'created_at',
        'updated_at',
    ];

    /**
     * @inheritdoc
     */
    public $attributes = [

    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'name' => 'string',
        'telegram_username' => 'string',
        'telegram_chat_id' => 'string',
        'role_const' => UserRole::class,
        'lname' => 'string',
        'language_code' => LanguageEnumHelper::class,
        'is_premium' => 'boolean',
        'is_bot' => 'boolean',
        'parent_id' => 'integer',
        'updated_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    /**
     * @return array
     */
    public static function attributeLabels(): array
    {
        return [
            'id' => trans('ID'),
            'name' => trans('Имя'),
            'telegram_username' => trans('telegram username'),
            'telegram_chat_id' => trans('telegram chat id'),
            'telegram_process' => trans('telegram process'),
            'role_const' => trans('Роль'),
            'lname' => trans('Фамилия'),
            'language_code' => trans('Код языка'),
            'is_premium' => trans('Премиум'),
            'is_bot' => trans('Это бот'),
            'parent_id' => trans('Реферрер'),
            'created_at' => trans('Создано'),
            'updated_at' => trans('Обновлено'),

        ];
    }

    public function changeTelegramProcess(UserTelegramProcess $telegram_process)
    {
        $this->update(['telegram_process' => $telegram_process->value]);
    }

    public static function findByTelegramId(int $telegramChatId): User
    {
        return User::query()->where('telegram_chat_id', $telegramChatId)->first();
    }

    public function getUsenameForExternal(): string
    {
        return $this->id . '_' . $this->telegram_chat_id;
    }


    // Связь "один ко многим" (один родитель - много детей)
    public function children()
    {
        return $this->hasMany(User::class, 'parent_id');
    }
}
