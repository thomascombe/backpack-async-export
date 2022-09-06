<?php

namespace Thomascombe\BackpackAsyncExport\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Thomascombe\BackpackAsyncExport\Enums\ExportStatus;
use Thomascombe\BackpackAsyncExport\Exports\ExportWithName;
use Thomascombe\BackpackAsyncExport\Models\Interfaces\ImportExportInterface;

class ImportExport extends Model implements ImportExportInterface
{
    use CrudTrait;
    use SoftDeletes;

    public const COLUMN_USER_ID = 'user_id';
    public const COLUMN_ACTION_TYPE = 'action_type';
    public const COLUMN_EXPORT_TYPE = 'export_type';
    public const COLUMN_FILENAME = 'filename';
    public const COLUMN_DISK = 'disk';
    public const COLUMN_STATUS = 'status';
    public const COLUMN_ERROR = 'error';
    public const COLUMN_COMPLETED_AT = 'completed_at';

    protected $fillable = [
        self::COLUMN_USER_ID,
        self::COLUMN_ACTION_TYPE,
        self::COLUMN_EXPORT_TYPE,
        self::COLUMN_FILENAME,
        self::COLUMN_DISK,
        self::COLUMN_STATUS,
        self::COLUMN_ERROR,
        self::COLUMN_COMPLETED_AT,
    ];

    protected $casts = [
        self::COLUMN_USER_ID => 'int',
    ];

    protected $dates = [
        self::COLUMN_COMPLETED_AT,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(config('backpack-async-import-export.user_model'));
    }

    protected static function boot()
    {
        parent::boot();
        ImportExport::saving(function (ImportExport $export) {
            if (empty($export->attributes[self::COLUMN_DISK])) {
                $export->attributes[self::COLUMN_DISK] = config('backpack-async-import-export.disk', 'local');
            }
        });
    }

    public function getExportTypeNameAttribute(): string
    {
        $exportType = $this->{self::COLUMN_EXPORT_TYPE};
        if (is_subclass_of($exportType, ExportWithName::class)) {
            return $exportType::getName();
        }

        return $exportType;
    }

    public function getDiskAttribute(): string
    {
        return $this->attributes[self::COLUMN_DISK] ?? config('backpack-async-import-export.disk', 'local');
    }

    public function getStoragePathAttribute(): string
    {
        /**
         * @psalm-suppress UndefinedInterfaceMethod
         */
        return Storage::disk($this->disk)->path($this->{self::COLUMN_FILENAME});
    }

    public function getDownloadButton(): string
    {
        if ($this->isReady) {
            $url = route(
                config('backpack-async-import-export.admin_export_route') . '.download',
                [
                    'export' => $this->id,
                ]
            );

            return sprintf(
                '<a href="%s" class="btn btn-sm btn-link"> <span class="la la-download"></span>%s</a>',
                $url,
                __('backpack-async-export::export.buttons.download')
            );
        }

        return sprintf(
            '<button type="button" class="btn btn-sm btn-link" disabled="disabled"><span class="la la-download"></span> %s</button>',
            __('backpack-async-export::export.buttons.download')
        );
    }

    public function getIsReadyAttribute(): bool
    {
        return ExportStatus::Successful === $this->{ImportExport::COLUMN_STATUS}
            && Storage::disk($this->disk)->exists($this->{self::COLUMN_FILENAME});
    }
}
