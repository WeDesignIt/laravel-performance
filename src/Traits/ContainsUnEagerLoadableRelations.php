<?php

namespace WeDesignIt\LaravelPerformance\Traits;

/**
 * Use this trait on a model which contains one or more relations that cannot
 * be eager loaded.
 */
trait ContainsUnEagerLoadableRelations
{
    /**
     * If the model has an array property called $dontEagerLoad, the relations
     * in it will not trigger a LazyLoadingViolationException.
     *
     * @return array
     */
    public function withoutEagerLoading(): array
    {
        return property_exists(
            $this,
            'without'
        ) ? $this->without : [];
    }
}
