<?php

declare(strict_types=1);

use JsonMapper\JsonMapperFactory;
use JsonMapper\Tests\Implementation\Benchmark\Joke;

class SingleJokeBench
{
    public function __construct()
    {
        $this->mapper = (new JsonMapperFactory())->bestFit();
    }

    /**
     * @Revs(1000)
     * @Iterations(10)
     * @Assert("mode(variant.time.avg) < mode(baseline.time.avg) +/- 5%")
     * @Assert("mode(variant.mem.peak) < mode(baseline.mem.peak) +/- 5%")
     */
    public function benchMapJoke(): void
    {
        $joke = new Joke();
        $json = '{"id":131,"type":"general","setup":"How do you organize a space party?","punchline":"You planet."}';
        $this->mapper->mapObjectFromString($json, $joke);

    }
}

