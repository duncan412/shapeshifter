<?php

namespace Just\Shapeshifter\Relations;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Just\Shapeshifter\Core\Controllers\AdminController;
use Just\Shapeshifter\Exceptions\MethodNotExistException;
use Just\Shapeshifter\Exceptions\ShapeShifterException;
use Request;
use Route;
use View;

class ManyToManyFacebookRelation extends OneToManyRelation
{
    /**
     * @var string
     */
    private $descriptorField;

    /**
     * @param AdminController $fromController
     * @param string          $destination
     * @param string          $function
     * @param array           $descriptorField
     * @param array           $flags
     *
     * @throws \Just\Shapeshifter\Exceptions\ShapeShifterException
     */
    public function __construct(AdminController $fromController, $destination, $function, $descriptorField, $flags = [])
    {
        $routes = Route::getRoutes();

        $this->destination = 'admin.'.$destination.'.index';
        $this->destination = $this->resolveControllerByName($routes);

        $this->fromcontroller = $fromController;

        if ($current = $this->getCurrentRecordId()) {
            $repo        = $fromController->getRepository();
            $this->model = $repo->findById($current);

            if (null == $this->model) {
                throw new ShapeShifterException(sprintf('Model [%s] with id [%s] doesn\'t exist', get_class($repo->getNew()), $current));

            }
        }

        $this->function = $function;
        $this->name     = $this->destination->getTitle();
        $this->flags    = array_merge($flags, ['hide_list']);
        $this->descriptorField = $descriptorField;
    }

    /**
     * display
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     *
     * @return mixed
     *
     * @throws \Just\Shapeshifter\Exceptions\MethodNotExistException
     */
    public function compile(Model $model = null)
    {
        $this->checkDestinationModel($model);

        $descriptor = $this->descriptorField;
        $table      = $this->destination->getRepository()->getNew()->getTable();
        $results    = $model->{$this->function}()->get([$table . '.id', "{$descriptor} as name"])->toJson();
        $all        = $this->destination->getRepository()->getNew()->get([$table . '.id', "{$descriptor} as name"])->toJson();

        return View::make('shapeshifter::relations.ManyToManyFacebookRelation',  [
            'results' => $results,
            'all'     => $all,
            'name'    => $this->name,
            'label'   => translateAttribute($this->name),
        ])->render();
    }

    /**
     * @param      $val
     * @param null $oldValue
     *
     * @return mixed|void
     */
    public function setAttributeValue($val, $oldValue = null)
    {
        if (is_array($val)) {
            $val = implode(',', $val);
        }
        $this->value = $val ? explode(',', $val) : [];
    }

    /**
     * @param Model $model
     *
     * @return null
     */
    public function getSaveValue(Model $model)
    {
        $model->{$this->function}()->withTimestamps()->sync($this->value);
    }

    /**
     * @return bool|int
     */
    protected function getCurrentRecordId()
    {
        $segments = (new Collection(Request::segments()))->reverse();

        return $segments->first(function ($index, $value) {
            return is_numeric($value);
        }, false);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $model
     *
     * @throws \Just\Shapeshifter\Exceptions\MethodNotExistException
     */
    protected function checkDestinationModel(Model $model)
    {
        if (!method_exists($model, $this->function)) {
            $modelName = get_class($model);

            throw new MethodNotExistException("Relation method [{$this->function}] doest not exist on [{$modelName}] model");
        }
    }
}
