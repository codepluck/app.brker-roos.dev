<?php

namespace Modules\Abstracts\Transformers;

use Illuminate\Support\Facades\Config;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\Resource\Primitive;
use League\Fractal\Scope;
use League\Fractal\TransformerAbstract as FractalTransformer;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

abstract class Transformer extends FractalTransformer
{


    // abstract public function transform();
    // Abstract method that child classes must implement
    abstract public function transformModel($model);

    public function transform($model)
    {
        if ($model instanceof Model) {
            return $this->transformModel($model);
        } 
        elseif ($model instanceof EloquentCollection) {
            return $model->map(function ($item) {
                return $this->transformModel($item);
            })->all();
        }
        elseif ($model instanceof LengthAwarePaginator || $model instanceof Paginator) {
            $transformedItems = $model->getCollection()->map(function ($item) {
                return $this->transformModel($item);
            });
    
            return [
                'items' => $transformedItems,
                'total_items' => $model->total(),
                'current_page' => $model->currentPage(),
                'per_page' => $model->perPage(),
                'last_page' => $model->lastPage(),
                'links' => [
                    'first' => $model->url(1),
                    'prev' => $model->previousPageUrl(),
                    'pages' => [
                        'page' => 1,
                        'link' => 'link',
                    ],
                    'next' => $model->nextPageUrl(),
                    'last' => $model->url($model->lastPage()),
                ],
            ];
        } 
        else {
            throw new \RuntimeException('Expected instance of ' . Model::class . ' or ' . EloquentCollection::class);
        }
    }

    //implementation
    // $transformer = new MyTransformer();
    // $transformedData = $transformer->transform($model);
    // return response()->json($transformedData);

}
