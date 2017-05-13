<?php declare(strict_types=1);

namespace ApiGen\Generator;

use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\Contracts\Generator\GeneratorInterface;
use ApiGen\Contracts\Templating\TemplateRendererInterface;
use ApiGen\Element\Annotation\AnnotationStorage;

final class AnnotationGroupsGenerator implements GeneratorInterface
{
    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @var AnnotationStorage
     */
    private $annotationStorage;

    /**
     * @var TemplateRendererInterface
     */
    private $templateRenderer;

    public function __construct(
        ConfigurationInterface $configuration,
        AnnotationStorage $annotationStorage,
        TemplateRendererInterface $templateRenderer
    ) {
        $this->configuration = $configuration;
        $this->annotationStorage = $annotationStorage;
        $this->templateRenderer = $templateRenderer;
    }

    public function generate(): void
    {
        foreach ($this->configuration->getAnnotationGroups() as $annotation) {
            $this->generateForAnnotation($annotation);
        }
    }

    private function generateForAnnotation(string $annotation): void
    {
        $elements = $this->annotationStorage->findByAnnotation($annotation);

        $this->templateRenderer->renderToFile(
            $this->configuration->getTemplateByName('annotation-group'),
            $this->configuration->getDestinationWithPrefixName('annotation-group-', $annotation),
            [
                'annotation' => $annotation,
                'hasElements' => (bool) count(array_filter($elements, 'count')),
                'classes' => $elements['classes'],
                'interfaces' => $elements['interfaces'],
                'traits' => $elements['traits'],
                'methods' => $elements['methods'],
                'functions' => $elements['functions'],
                'properties' => $elements['properties']
            ]
        );
    }
}
