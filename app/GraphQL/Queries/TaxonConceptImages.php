<?php

namespace App\GraphQL\Queries;

use App\Services\CantoImageService;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class TaxonConceptImages
{
    /**
     * @param null $_
     * @param array $args
     * @param \GraphQL\Type\Definition\ResolveInfo $resolveInfo
     * @return array
     */
    public function __invoke($_, array $args, GraphQLContext $context, ResolveInfo $resolveInfo): array
    {
        $imageService = new CantoImageService();

        return $imageService->findByTaxon(
                $args['taxonConceptId'],
                $args['first'],
                $args['page']);
    }
}
