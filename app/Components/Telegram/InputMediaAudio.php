<?php

declare(strict_types=1);


namespace App\Components\Telegram;


use TelegramBot\Api\Types\InputMedia\InputMedia;

/**
 * Class InputMediaAudio
 * Represents an audio to be sent.
 *
 * @package TelegramBot\Api
 */
class InputMediaAudio extends InputMedia
{
    /**
     * {@inheritdoc}
     *
     * @var array
     */
    protected static $map = [
        'type' => true,
        'media' => true,
        'caption' => true,
        'parse_mode' => true,
        'duration' => true,
        'performer' => true,
        'title' => true,
    ];

    /**
     * Optional. Audio duration.
     *
     * @var int|null
     */
    protected $duration;

    /**
     * Optional. Performer of the audio.
     *
     * @var string|null
     */
    protected $performer;

    /**
     * Optional. Title of the audio.
     *
     * @var string|null
     */
    protected $title;

    /**
     * InputMediaAudio constructor.
     *
     * @param string $media
     * @param string|null $caption
     * @param string|null $parseMode
     * @param int|null $duration
     * @param string|null $performer
     * @param string|null $title
     */
    public function __construct(
        $media,
        $caption = null,
        $parseMode = null,
        $duration = null,
        $performer = null,
        $title = null
    ) {
        $this->type = 'audio';
        $this->media = $media;
        $this->caption = $caption;
        $this->parseMode = $parseMode;
        $this->duration = $duration;
        $this->performer = $performer;
        $this->title = $title;
    }

    /**
     * @return int|null
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * @param int|null $duration
     *
     * @return void
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;
    }

    /**
     * @return string|null
     */
    public function getPerformer()
    {
        return $this->performer;
    }

    /**
     * @param string|null $performer
     *
     * @return void
     */
    public function setPerformer($performer)
    {
        $this->performer = $performer;
    }

    /**
     * @return string|null
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string|null $title
     *
     * @return void
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }
}