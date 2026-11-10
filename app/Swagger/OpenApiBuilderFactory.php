<?php

declare(strict_types=1);

namespace App\Swagger;

use L5Swagger\CustomBuilderInterface;
use OpenApi\Analysers\AttributeAnnotationFactory;
use OpenApi\Analysers\DocBlockAnnotationFactory;
use OpenApi\Analysers\ReflectionAnalyser;
use OpenApi\Builder;
use OpenApi\Builder\Mode;
use OpenApi\Generator;

final class OpenApiBuilderFactory implements CustomBuilderInterface
{
    public function create(): Builder
    {
        return (new Builder)
            ->setMode(Mode::CLASSIC)
            ->withGenerator(static fn (): Generator => (new Generator)->setAnalyser(
                new ReflectionAnalyser([
                    new AttributeAnnotationFactory,
                    new DocBlockAnnotationFactory,
                ]),
            ));
    }
}
