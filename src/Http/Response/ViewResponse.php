<?php

namespace Emsifa\Evo\Http\Response;

use Emsifa\Evo\DTO;
use Emsifa\Evo\Helpers\ObjectHelper;
use Emsifa\Evo\Helpers\ReflectionHelper;
use Illuminate\Contracts\Support\Responsable;
use ReflectionClass;

abstract class ViewResponse extends DTO implements Responsable
{
    protected string $viewName = "";

    /**
     * {@inheritdoc}
     */
    public function toResponse($request)
    {
        $viewName = $this->getViewName();
        $data = ObjectHelper::toArray($this, false);

        return response()->view($viewName, $data);
    }

    public function getViewName(): string
    {
        return $this->getViewNameFromAttribute() ?: $this->viewName;
    }

    protected function getViewNameFromAttribute(): ?string
    {
        $reflection = new ReflectionClass($this);

        /**
         * @var UseView|null $useView
         */
        $useView = ReflectionHelper::getFirstAttributeInstance($reflection, UseView::class);

        return $useView ? $useView->getViewName() : null;
    }
}
