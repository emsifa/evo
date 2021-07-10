<?php

namespace Emsifa\Evo\Http\Response;

use Emsifa\Evo\Contracts\JsonData;
use Emsifa\Evo\Contracts\JsonTemplate;
use Emsifa\Evo\DTO;
use Emsifa\Evo\Helpers\ObjectHelper;
use Emsifa\Evo\Helpers\ReflectionHelper;
use Illuminate\Contracts\Support\Responsable;
use ReflectionAttribute;

abstract class JsonResponse extends DTO implements JsonData, Responsable
{
    /**
     * {@inheritdoc}
     */
    public function toResponse($request)
    {
        $template = $this->getJsonTemplate();
        $result = $template ? $this->getTemplatedData($template) : $this->getJsonData();

        return response()->json($result);
    }

    /**
     * Get JSON data in array format
     *
     * @return array
     */
    public function getJsonData(): array
    {
        return $this->toArray();
    }

    /**
     * Just syntax sugar for JsonTemplate
     *
     * @return JsonData
     */
    public function getData(): JsonData
    {
        return $this;
    }

    public function getTemplatedData(JsonTemplate $template): array
    {
        $result = $template->forJsonResponse($this);

        return ObjectHelper::toArray($result);
    }

    protected function getJsonTemplate(): ?JsonTemplate
    {
        $useJsonTemplate = ReflectionHelper::getFirstClassAttribute($this, UseJsonTemplate::class, ReflectionAttribute::IS_INSTANCEOF);
        if (! $useJsonTemplate) {
            return null;
        }

        $jsonTemplateClass = ($useJsonTemplate->newInstance())->getTemplateClassName();

        return app($jsonTemplateClass);
    }
}
