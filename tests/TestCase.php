<?php

namespace Lukasss93\ModelSettings\Tests;

use CreateModelSettingsTable;
use Lukasss93\ModelSettings\ModelSettingsServiceProvider;
use Lukasss93\ModelSettings\Tests\Models\Article;
use Lukasss93\ModelSettings\Tests\Models\User;
use Lukasss93\ModelSettings\Tests\Models\UsersWithTable;
use Lukasss93\ModelSettings\Tests\Models\UserWithField;
use Lukasss93\ModelSettings\Tests\Models\UserWithRedis;
use Lukasss93\ModelSettings\Tests\Models\UserWithTextField;
use Lukasss93\ModelSettings\Tests\Models\WrongUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Lunaweb\RedisMock\Providers\RedisMockServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app)
    {
        return [
            ModelSettingsServiceProvider::class,
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpDatabase();
        $this->checkRequirements();
    }

    protected function checkRequirements()
    {
        //
    }

    public function getEnvironmentSetUp($app)
    {
        $app['config']->set('auth.providers.users.model', UserWithField::class);
        $app['config']->set('database.redis.client', 'mock');
        $app['config']->set('database.default', 'testing');
        $app->register(RedisMockServiceProvider::class);
    }

    protected function setUpDatabase()
    {
        $this->createSettingsTable();

        $this->createTables('users', 'users_with_table', 'users_with_field', 'users_with_text_field', 'wrong_users');
        $this->seedModels(UserWithField::class, UserWithTextField::class, UsersWithTable::class, WrongUser::class, UserWithRedis::class);
    }

    protected function createSettingsTable()
    {
        include_once __DIR__ . '/migrations/create_model_settings_table.php';

        (new CreateModelSettingsTable())->up();
    }

    protected function createTables(...$tableNames)
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

    protected function seedModels(...$modelClasses)
    {
        collect($modelClasses)->each(function (string $modelClass) {
            foreach (range(1, 2) as $index) {
                $modelClass::create(['name' => "name {$index}"]);
            }
        });
    }

    public function markTestAsPassed()
    {
        $this->assertTrue(true);
    }

    /**
     * @param string $type
     * @return \Illuminate\Database\Eloquent\Model|\Lukasss93\ModelSettings\Traits\HasSettings
     */
    protected function getModelByType(string $type): Model
    {
        switch ($type) {
            case 'table':
                $model = UsersWithTable::first();
                break;
            case 'text_field':
                $model = UserWithTextField::first();
                break;
            case 'redis':
                $model = UserWithRedis::first();
                break;
            default:
                $model = UserWithField::first();
                break;
        }

        return $model;
    }
}
