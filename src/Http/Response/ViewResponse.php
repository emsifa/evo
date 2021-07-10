<?php

namespace Emsifa\Evo\Http\Response;

use Emsifa\Evo\DTO;
use Emsifa\Evo\Helpers\ObjectHelper;
use Illuminate\Contracts\Support\Responsable;

abstract class ViewResponse extends DTO implements Responsable
{
    protected string $viewName;

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
        return $this->viewName;
    }
}
