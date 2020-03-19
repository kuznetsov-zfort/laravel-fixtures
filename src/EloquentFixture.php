<?php

namespace KuznetsovZfort\Fixture;

use Exception;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;

/**
 * EloquentFixture represents a fixture backed up by a [[modelClass|Illuminate\Database\Eloquent\Model class]].
 *
 * [[modelClass]] must be set. You should also provide fixture data in the file
 * specified by [[dataFile]] or overriding [[getData()]] if you want to use code to generate the fixture data.
 *
 * When the fixture is being loaded, it will first call [[deleteModels()]] to remove any existing data in the table.
 * It will then populate the table with the data returned by [[getData()]].
 *
 * After the fixture is loaded, you can access the loaded data via the [[data]] property.
 */
class EloquentFixture extends BaseActiveFixture
{
    /**
     * @var string the file path that contains the fixture data to be returned by [[getData()]]
     */
    public $dataFile;

    /**
     * @inheritdoc
     */
    public function load(): void
    {
        $this->data = [];

        foreach ($this->getData() as $alias => $row) {
            $model = app($this->modelClass);
            $model->fill($row)->saveOrFail();

            $this->data[$alias] = array_merge($row, ['id' => $model->getKey()]);
        }
    }

    /**
     * @inheritdoc
     */
    public function unload(): void
    {
        $this->deleteModels();

        parent::unload();
    }

    /**
     * Removes all existing models.
     *
     * @throws Exception
     */
    protected function deleteModels(): void
    {
        $model = app($this->modelClass);
        // determine if the model uses `SoftDeletes` trait
        $usesSoftDeletes = in_array(SoftDeletes::class, class_uses($model));

        foreach ($model->all() as $value) {
            try {
                if ($usesSoftDeletes) {
                    $value->forceDelete();
                } else {
                    $value->delete();
                }
            } catch (Exception $exception) {
                Log::warning(sprintf('Error during deleting models. Table: %s. Error: %s', $model->getTable(), $exception->getMessage()));
            }
        }
    }
}
