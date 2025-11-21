<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use PDOException;

class AppServiceProvider extends ServiceProvider
{
    /**
     * register any application services
     */
    public function register(): void
    {
        //
    }

    /**
     * bootstrap any application services
     */
    public function boot(): void
    {
        // force HTTPS di production
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }

        // prevent lazy loading di development untuk menghindari N+1 queries
        Model::preventLazyLoading(!$this->app->isProduction());

        // prevent silently discarding attributes
        Model::preventSilentlyDiscardingAttributes(!$this->app->isProduction());

        // prevent accessing missing attributes
        Model::preventAccessingMissingAttributes(!$this->app->isProduction());

        // FIX: setup database reconnection logic untuk PostgreSQL
        // ini mengatasi error "prepared statement does not exist" di production
        $this->setupDatabaseReconnection();

        // customize pagination view
        // Paginator::useBootstrapFive(); // jika mau pakai bootstrap
        // Paginator::useTailwind(); // otomatis pakai tailwind di Laravel 11+
    }

    /**
     * setup database reconnection untuk mengatasi prepared statement error
     * khususnya di production dengan connection pooling (Railway, Heroku, dll)
     */
    protected function setupDatabaseReconnection(): void
    {
        // hanya aktifkan di production atau jika menggunakan PostgreSQL
        if (config('database.default') === 'pgsql') {
            DB::listen(function ($query) {
                // log slow queries di development
                if (!$this->app->isProduction() && $query->time > 1000) {
                    Log::warning('Slow query detected', [
                        'sql' => $query->sql,
                        'bindings' => $query->bindings,
                        'time' => $query->time,
                    ]);
                }
            });

            // handle database reconnection untuk PostgreSQL errors
            DB::beforeExecuting(function ($query, $bindings, $connection) {
                try {
                    // test connection dengan ping
                    $connection->getPdo();
                } catch (PDOException $e) {
                    // PENTING: JANGAN reconnect jika sedang dalam transaction!
                    // Reconnecting dalam transaction akan menyebabkan transaction abort
                    $transactionLevel = $connection->transactionLevel();

                    if ($this->isPreparedStatementError($e)) {
                        // hanya reconnect jika TIDAK dalam transaction
                        if ($transactionLevel === 0) {
                            Log::info('Reconnecting database due to prepared statement error');
                            $connection->reconnect();
                        } else {
                            // jika dalam transaction, log dan re-throw exception
                            // biar transaction bisa di-rollback dengan benar
                            Log::warning('Connection error during transaction, cannot reconnect', [
                                'transaction_level' => $transactionLevel,
                                'error' => $e->getMessage()
                            ]);
                            throw $e;
                        }
                    }
                }
            });
        }
    }

    /**
     * cek apakah error adalah prepared statement error
     */
    protected function isPreparedStatementError(PDOException $e): bool
    {
        // PostgreSQL error codes untuk prepared statement issues
        $preparedStatementErrors = [
            '26000', // invalid sql statement name
            '08003', // connection does not exist
            '08006', // connection failure
            '08001', // sqlclient unable to establish sqlconnection
            '08004', // sqlserver rejected establishment of sqlconnection
        ];

        return in_array($e->getCode(), $preparedStatementErrors) ||
               str_contains($e->getMessage(), 'prepared statement') ||
               str_contains($e->getMessage(), 'does not exist');
    }
}