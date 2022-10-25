<?php

namespace Lukasss93\ModelSettings\Tests;

use CreateModelSettingsTable;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Lukasss93\ModelSettings\ModelSettingsServiceProvider;
use Lukasss93\ModelSettings\Tests\Models\Article;
use Lukasss93\ModelSettings\Tests\Models\User;
use Lukasss93\ModelSettings\Tests\Models\UsersWithTable;
use Lukasss93\ModelSettings\Tests\Models\UserWithField;
use Lukasss93\ModelSettings\Tests\Models\UserWithRedis;
use Lukasss93\ModelSettings\Tests\Models\UserWithTextField;
use Lukasss93\ModelSettings\Tests\Models\WrongUser;
use Lunaweb\RedisMock\Providers\RedisMockServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            ModelSettingsServiceProvider::class,
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->createSettingsTable();
        $this->createTables('users', 'users_with_table', 'users_with_field', 'users_with_text_field', 'wrong_users');
        $this->seedModels(UserWithField::class, UserWithTextField::class, UsersWithTable::class, WrongUser::class,
            UserWithRedis::class);
    }

    public function getEnvironmentSetUp($app): void
    {
        $app['config']->set('auth.providers.users.model', UserWithField::class);
        $app['config']->set('database.redis.client', 'mock');
        $app['config']->set('database.default', 'testing');
        $app->register(RedisMockServiceProvider::class);
    }

    protected function createSettingsTable(): void
    {
        include_once __DIR__.'/migrations/create_model_settings_table.php';

        (new CreateModelSettingsTable())->up();
    }

    protected function createTables(...$tableNames): void
    {
        collect($tableNames)->each(function (string $tableName) {
            Schema::create($tableName, function (Blueprint $table) use ($tableName) {
                $table->increments('id');
                $table->string('name')->nullable();
                $table->timestamps();

                if ('users_with_field' === $tableName) {
                    $table->json('settings')->nullable();
                }

                if ('users_with_text_field' === $tableName) {
                    $table->text('settings')->nullable();
                }
            });
        });
    }

    protected function seedModels(...$modelClasses): void
    {
        collect($modelClasses)->each(function (string $modelClass) {
            foreach (range(1, 2) as $index) {
                $modelClass::create(['name' => "name {$index}"]);
            }
        });
    }
}
