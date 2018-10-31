<?php

namespace Insense\LaravelTelescopePruning\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class EntryModel extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'telescope_entries';

    /**
     * The name of the "updated at" column.
     *
     * @var string
     */
    const UPDATED_AT = null;

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'content' => 'json',
    ];

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'uuid';

    /**
     * The "type" of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Prevent Eloquent from overriding uuid with `lastInsertId`.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * Get the current connection name for the model.
     *
     * @return string
     */
    public function getConnectionName()
    {
        return config('telescope.storage.database.connection');
    }

    /**
     * Scope the query to exclude entries with monitored tags.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNotMonitored($query)
    {
        return $query->whereDoesntHave('tags', function ($query) {
            $query->whereIn('tag', function ($query) {
                $query->select('tag')->from('telescope_monitoring');
            });
        });
    }

    /**
     * Scope the query to include entries with monitored tags.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeMonitored($query)
    {
        return $query->whereHas('tags', function ($query) {
            $query->whereIn('tag', function ($query) {
                $query->select('tag')->from('telescope_monitoring');
            });
        });
    }

    /**
     * Scope the query to exclude entries with specific whitelisted tags.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeExcludeSpecificWhitelisted($query)
    {
        return $query->whereDoesntHave('tags', function ($query) {
            $query->whereIn('tag', config('telescope-pruning.specific_tags', []));
        });
    }

    /**
     * Scope the query to include entries with specific whitelisted tags.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeIncludeSpecificWhitelisted($query)
    {
        return $query->whereHas('tags', function ($query) {
            $query->whereIn('tag', config('telescope-pruning.specific_tags', []));
        });
    }

    /**
     * Scope the query to exclude entries with whitelisted tags.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNotWhitelisted($query)
    {
        return $query->when(
            config('telescope-pruning.skip_monitored_tags', true),
            function ($query) {
                $query->notMonitored();
            }
        )->excludeSpecificWhitelisted();
    }

    /**
     * Scope the query to include entries with whitelisted tags.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWhitelisted($query)
    {
        return $query->where(function ($query) {
            $query->when(!empty(config('telescope-pruning.specific_tags')), function ($query) {
                $query->includeSpecificWhitelisted();
            })->orWhere(function ($query) {
                $query->monitored();
            });
        });
    }

    /**
     * Scope the query to include prunable non-whitelisted entries.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePrunableNonWhitelisted($query)
    {
        return $query->whereNotIn(
            'batch_id',
            static::whitelisted()->select('batch_id')->groupBy('batch_id')
        );
    }

    /**
     * Scope the query to include prunable non-whitelisted entries.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePrunableWhitelisted($query)
    {
        return $query->whereIn(
            'batch_id',
            static::whitelisted()->select('batch_id')->groupBy('batch_id')
        );
    }

    /**
     * Prune the entries based on the limit and whitelisted tags.
     *
     * @param int $limit
     *
     * @return int
     */
    public static function prune($limit = 0, $whitelistedLimit = null)
    {
        return static::pruneNonWhitelisted($limit) + static::pruneWhitelisted($whitelistedLimit);
    }

    /**
     * Prune the non-whitelisted entries based on the limit.
     *
     * @param int $limit
     *
     * @return int
     */
    public static function pruneNonWhitelisted($limit = 0)
    {
        if (is_null($limit)) {
            // Pruning is disabled
            return 0;
        }

        $subQuery = static::prunableNonWhitelisted()->select('batch_id')
            ->groupBy('batch_id');
        $prunableBatchCount = DB::connection(config('telescope.storage.database.connection'))
            ->table(DB::raw("({$subQuery->toSql()}) as sub"))->mergeBindings($subQuery->getQuery())->count();

        if ($prunableBatchCount <= $limit) {
            // Prunable batch count is within the limit, so don't prune any entries.
            return 0;
        }

        $numPrunes = $prunableBatchCount - $limit;

        return static::whereIn(
            'batch_id',
            function ($query) use ($numPrunes) {
                $query->select('batch_id')->fromSub(
                    static::prunableNonWhitelisted()
                        ->select('batch_id')->selectRaw('max(sequence) as seq')
                        ->groupBy('batch_id')->orderBy('seq')
                        ->limit($numPrunes)->toBase(),
                    'prunable_nonwhitelisted'
                );
            }
        )->delete();
    }

    /**
     * Prune the whitelisted entries based on the limit.
     *
     * @param int $limit
     *
     * @return int
     */
    public static function pruneWhitelisted($limit = null)
    {
        if (is_null($limit)) {
            // Pruning is disabled
            return 0;
        }

        $subQuery = static::whitelisted()->select('batch_id')
            ->groupBy('batch_id');
        $prunableBatchCount = DB::connection(config('telescope.storage.database.connection'))
            ->table(DB::raw("({$subQuery->toSql()}) as sub"))->mergeBindings($subQuery->getQuery())->count();

        if ($prunableBatchCount <= $limit) {
            // Prunable batch count is within the limit, so don't prune any entries.
            return 0;
        }

        $numPrunes = $prunableBatchCount - $limit;

        return static::whereIn(
            'batch_id',
            function ($query) use ($numPrunes) {
                $query->select('batch_id')->fromSub(
                    static::prunableWhitelisted()
                        ->select('batch_id')->selectRaw('max(sequence) as seq')
                        ->groupBy('batch_id')->orderBy('seq')
                        ->limit($numPrunes)->toBase(),
                    'prunable_whitelisted'
                );
            }
        )->delete();
    }

    /**
     * The tags related to the Telescope entries.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tags()
    {
        return $this->hasMany(EntryTagModel::class, 'entry_uuid');
    }
}
