<?php

namespace Modules\Abstracts\Repositories {

    // inspired by : https://github.com/bosnadev/repository

    use Illuminate\Support\Collection;
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Container\Container as App;
    use Illuminate\Support\Facades\DB;
    use Modules\Abstracts\Exceptions\TransactionException;
    use Modules\Abstracts\Repositories\Contracts\CriteriaInterface;
    use Modules\Abstracts\Repositories\Contracts\RepositoryInterface;
    use Modules\Abstracts\Repositories\Exceptions\RepositoryException;
    use Modules\Abstracts\Repositories\Criteria\Criteria;

    use Modules\Abstracts\Traits\ResponseTrait;

    use Auth;

    abstract class Repository implements RepositoryInterface, CriteriaInterface
    {
        use ResponseTrait;

        /**
         * @var App
         */
        private $app;

        /**
         * @var
         */
        protected $model;

        protected $newModel;

        /**
         * @var Collection
         */
        protected $criteria;

        /**
         * @var bool
         */
        protected $skipCriteria = false;

        /**
         * Prevents from overwriting same criteria in chain usage
         * @var bool
         */
        protected $preventCriteriaOverwriting = true;

        /**
         * @param App $app
         * @param Collection $collection
         * @throws RepositoryException
         */
        public function __construct(App $app, Collection $collection)
        {
            $this->app = $app;
            $this->criteria = $collection;
            $this->resetScope();
            $this->makeModel();
        }

        /**
         * Specify Model class name
         *
         * @return mixed
         */
        public abstract function model();

        /**
         * @param array $columns
         * @return mixed
         */
        public function all($columns = array('*'))
        {
            $this->applyCriteria();
            return $this->model->get($columns);
        }

        /**
         * @param array $relations
         * @return $this
         */
        public function with(array $relations)
        {
            $this->model = $this->model->with($relations);
            return $this;
        }

        /**
         * @param  string $value
         * @param  string $key
         * @return array
         */
        public function lists($value, $key = null)
        {
            $this->applyCriteria();
            $lists = $this->model->lists($value, $key);
            if (is_array($lists)) {
                return $lists;
            }
            return $lists->all();
        }

        /**
         * @param int $perPage
         * @param array $columns
         * @return mixed
         */
        public function paginate($perPage = 25, $columns = array('*'))
        {
            $this->applyCriteria();
            return $this->model->paginate($perPage, $columns);
        }

        /**
         * @param array $data
         * @return mixed
         */
        public function create(array $data)
        {
            try {
                return DB::transaction(function() use ($data) {
                    return  $this->model->create($data);
                });
            } catch (\Exception $e) {
                throw new \Exception($e->getMessage());
            }       
        }
        
        /**
         * @param array $data
         * @return mixed
         */
        public function createOrFail($data)
        {
            try {
                return DB::transaction(function() use ($data) {
                    $record = $this->model->create($data);
                    
                    if (!$record) {
                        throw new \RuntimeException("Failed to create a new record.");
                    }
        
                    return $record;
                });
            } catch (\Throwable $e) {
                throw new \RuntimeException("Failed to create a new record: " . $e->getMessage(), 0, $e);
            }
        }        
        
        /**
         * @param array $data
         * @param int $id
         * @return mixed
         */
        public function createOrUpdate(array $data, int $id = null)
        {
            try 
            {
                return DB::transaction(function() use ($data, $id) {
                    if($id):
                        $record = $this->model->find($id);
                        if ($record):
                            if($record->update($data)):
                                return $record;
                            endif;
                            throw new \RuntimeException("Failed to update the record.");
                        endif;                        
                    endif;
                    $record = $this->model->create($data);
                    
                    if ($record):
                        return $record;
                    endif;

                    throw new \RuntimeException("Failed to create a new record."); 
                });
            } 
            catch (\Throwable $e) 
            {
                throw new \RuntimeException("Failed to create a new record: " . $e->getMessage(), 0, $e);
            }
        }

        /**
         * save a model without massive assignment
         *
         * @param array $data
         * @return bool
         */
        public function saveModel(array $data)
        {
            foreach ($data as $k => $v) {
                $this->model->$k = $v;
            }
            return $this->model->save();
        }

        /**
         * @param array $data
         * @param $id
         * @param string $attribute
         * @return mixed
         */
        public function update(array $data, $id, $attribute = "id")
        {
            return $this->model->where($attribute, '=', $id)->update($data);
        }

        /**
         * @param  array $data
         * @param  $id
         * @return mixed
         */
        public function updateRich(array $data, $id)
        {
            if (!($model = $this->model->find($id))) {
                return false;
            }
            return $model->fill($data)->save();
        }

        /**
         * @param $id
         * @return mixed
         */
        public function delete($id)
        {
            return $this->model->destroy($id);
        }

        /**
         * @param $id
         * @param array $columns
         * @return mixed
         */
        public function find($id, $columns = array('*'))
        {
            $this->applyCriteria();
            return $this->model->find($id, $columns);
        }

        /**
         * @param $attribute
         * @param $value
         * @param array $columns
         * @return mixed
         */
        public function findBy($attribute, $value, $columns = array('*'))
        {
            $this->applyCriteria();
            return $this->model->where($attribute, '=', $value)->first($columns);
        }

        /**
         * @param $attribute
         * @param $value
         * @param array $columns
         * @return mixed
         */
        public function findAllBy($attribute, $value, $columns = array('*'))
        {
            $this->applyCriteria();
            return $this->model->where($attribute, '=', $value)->get($columns);
        }

        /**
         * Find a collection of models by the given query conditions.
         *
         * @param array $where
         * @param array $columns
         * @param bool $or
         *
         * @return \Illuminate\Database\Eloquent\Collection|null
         */
        public function findWhere($where, $columns = ['*'], $or = false)
        {
            $this->applyCriteria();

            $model = $this->model;

            foreach ($where as $field => $value) {
                if ($value instanceof \Closure) {
                    $model = (!$or)
                        ? $model->where($value)
                        : $model->orWhere($value);
                } elseif (is_array($value)) {
                    if (count($value) === 3) {
                        list($field, $operator, $search) = $value;
                        $model = (!$or)
                            ? $model->where($field, $operator, $search)
                            : $model->orWhere($field, $operator, $search);
                    } elseif (count($value) === 2) {
                        list($field, $search) = $value;
                        $model = (!$or)
                            ? $model->where($field, '=', $search)
                            : $model->orWhere($field, '=', $search);
                    }
                } else {
                    $model = (!$or)
                        ? $model->where($field, '=', $value)
                        : $model->orWhere($field, '=', $value);
                }
            }
            return $model->get($columns);
        }

        /**
         * @return \Illuminate\Database\Eloquent\Builder
         * @throws RepositoryException
         */
        public function makeModel()
        {
            return $this->setModel($this->model());
        }

        /**
         * Set Eloquent Model to instantiate
         *
         * @param $eloquentModel
         * @return Model
         * @throws RepositoryException
         */
        public function setModel($eloquentModel)
        {
            $this->newModel = $this->app->make($eloquentModel);

            if (!$this->newModel instanceof Model)
                throw new RepositoryException("Class {$this->newModel} must be an instance of Illuminate\\Database\\Eloquent\\Model");

            return $this->model = $this->newModel;
        }

        /**
         * @return $this
         */
        public function resetScope()
        {
            $this->skipCriteria(false);
            return $this;
        }

        /**
         * @param bool $status
         * @return $this
         */
        public function skipCriteria($status = true)
        {
            $this->skipCriteria = $status;
            return $this;
        }

        /**
         * @return mixed
         */
        public function getCriteria()
        {
            return $this->criteria;
        }

        /**
         * @param Criteria $criteria
         * @return $this
         */
        public function getByCriteria(Criteria $criteria)
        {
            $this->model = $criteria->apply($this->model, $this);
            return $this;
        }

        /**
         * @param Criteria $criteria
         * @return $this
         */
        public function pushCriteria(Criteria $criteria)
        {
            if ($this->preventCriteriaOverwriting) {
                // Find existing criteria
                $key = $this->criteria->search(function ($item) use ($criteria) {
                    return (is_object($item) && (get_class($item) == get_class($criteria)));
                });

                // Remove old criteria
                if (is_int($key)) {
                    $this->criteria->offsetUnset($key);
                }
            }

            $this->criteria->push($criteria);
            return $this;
        }

        /**
         * @return $this
         */
        public function applyCriteria()
        {
            if ($this->skipCriteria === true)
                return $this;

            foreach ($this->getCriteria() as $criteria) {
                if ($criteria instanceof Criteria)
                    $this->model = $criteria->apply($this->model, $this);
            }

            return $this;
        }

        public function getAdminID()
        {
            $user = Auth::user();

            // check role has parent id as superadmin
            if ($user->roles?->first()?->user_id == 1) {
                return $user->roles->first()->user_id;
            }
            return $user->id;
        }
    }    
}
