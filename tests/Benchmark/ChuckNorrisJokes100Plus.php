<?php

declare(strict_types=1);

namespace JsonMapper\Tests\Benchmark;


use JsonMapper\Enums\TextNotation;
use JsonMapper\JsonMapperBuilder;
use JsonMapper\JsonMapperInterface;
use JsonMapper\Tests\Implementation\Benchmark\ChuckNorrisJoke;

class ChuckNorrisJokes100Plus
{
    /** @var string */
    private $json;
    /** @var JsonMapperInterface */
    private $mapper;

    public function __construct()
    {
        $this->json = file_get_contents(__DIR__ . '/../data/ChuckNorrisJokeApi100PlusJokes.json');

        $this->mapper = JsonMapperBuilder::new()
            ->withDocBlockAnnotationsMiddleware()
            ->withNamespaceResolverMiddleware()
            ->withCaseConversionMiddleware(TextNotation::UNDERSCORE(), TextNotation::CAMEL_CASE())
            ->build();
    }


    /**
     * @Revs(1000)
     * @Iterations(10)
     * @Assert("mode(variant.time.avg) < mode(baseline.time.avg) +/- 5%")
     * @Assert("mode(variant.mem.peak) < mode(baseline.mem.peak) +/- 5%")
     */
    public function benchMapChuckNorrisJoke(): void
    {
        sleep(3);
        $joke = new ChuckNorrisJoke();
        $this->mapper->mapObjectFromString($this->json, $joke);
    }
}