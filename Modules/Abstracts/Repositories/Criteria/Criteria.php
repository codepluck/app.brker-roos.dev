<?php

namespace Modules\Abstracts\Repositories\Criteria {

    use Modules\Abstracts\Repositories\Contracts\RepositoryInterface as Repository;

    abstract class Criteria
    {

        /**
         * @param $model
         * @param Repository $repository
         * @return mixed
         */
        public abstract function apply($model, Repository $repository);
    }
}
